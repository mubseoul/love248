<?php

namespace App\Http\Controllers;

use App\Events\LiveStreamBan;
use App\Http\Requests\ChannelSettingsRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPlanSell;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\RoomBans;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\Log;


class ChannelController extends Controller
{
    // midleware
    public function __construct()
    {
        // $this->middleware('auth');
    }

    // search channel
    public function search(Request $request)
    {
        $request->validate(['term' => 'required|min:2']);

        return User::isStreamer()
            ->where('username', 'like', '%' . $request->term . '%')
            ->where('name', 'like', '%' . $request->term . '%')
            ->take(6)
            ->get();
    }

    // live stream
    public function liveStream($user, Request $r)
    {
        $userInfo = Auth::user();
        // get the stream user
        $streamUser = User::whereUsername($user)
            ->withCount(['followers', 'subscribers', 'videos'])
            ->firstOrFail();

        // Retrieve the user's date of birth from $streamUser object
        $dob = $userInfo->dob ?? '';
        if (empty($dob)) {
            return Redirect::route('profile.edit')->with('message', __('User must be 18 years old or older.'));
        }
        // Validate that the DOB is in the correct format (YYYY-MM-DD)
        if (!Carbon::createFromFormat('Y-m-d', $dob)->isValid()) {
            // Handle invalid date format error
            return Redirect::back()->with('message', __('Invalid date of birth format.'));
        }
        $age = Carbon::parse($dob)->age;
        if ($age < 18) {
            return Redirect::route('profile.edit')->with('message', __('User must be 18 years old or older.'));
        }
        $tokens = $userInfo->tokens ?? '';
        if (empty($tokens) || $tokens < 0) {
            return Redirect::route('token.packages')->with('message', __('By Token Packages .'));
        }
        $streamUser->increment('popularity');
        // check this user (if authenticated) is banned form this room
        if (auth()->check()) {
            $isBanned = $r->user()->bannedFromRooms()->where('streamer_id', $streamUser->id)->exists();
            if ($isBanned) {
                return to_route('channel.bannedFromRoom', ['user' => $streamUser->username]);
            }
        }

        // check if this ip is banned from this room
        $isBannedFromRoom = RoomBans::where('ip', $r->ip())->exists();
        if ($isBannedFromRoom) {
            return to_route('channel.bannedFromRoom', ['user' => $streamUser->username]);
        }

        // if authenticated user == streamuser
        $isChannelOwner = false;

        if (auth()->check() && $streamUser->username === request()->user()->username) {
            $isChannelOwner = true;
        }

        // check if it follows channel
        $userFollowsChannel = false;
        if (auth()->check() && auth()->user()->isFollowing($streamUser)) {
            $userFollowsChannel = true;
        }

        // check if has subscription
        $userIsSubscribed = false;
        if (auth()->check() && auth()->user()->hasSubscriptionTo($streamUser)) {
            $userIsSubscribed = true;
        }

        $today = Carbon::today();
        $isSubscriptionPlan = SubscriptionPlanSell::where('expire_date', '>=', $today)
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // room name
        if (user_meta('streaming_key', true, $streamUser->id)) {
            $roomName = user_meta('streaming_key', true, $streamUser->id);
        } else {
            $roomName = $streamUser->id . '.' . Str::random(16);
            set_user_meta('streaming_key', $roomName, true, $streamUser->id);
        }

        $aws = [
            'accessKeyId' => env('AWS_ACCESS_KEY_ID'),
            'secretAccessKey' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
        ];

        // Get Stripe public key
        $stripePublicKey = opt('STRIPE_PUBLIC_KEY');

        // Get HLS URL from environment variable
        $hls_url = env('HLS_URL', 'https://live.dg4e.com/hls');

        return Inertia::render('Channel/LiveStream', compact('isChannelOwner', 'streamUser', 'userFollowsChannel', 'userIsSubscribed', 'roomName', 'isSubscriptionPlan', 'aws', 'stripePublicKey', 'hls_url'));
    }

    // start stream
    public function userProfile($user)
    {
        // get the stream user
        $streamUser = User::whereUsername($user)
            ->withCount(['followers', 'subscribers', 'videos'])
            ->firstOrFail();

        $streamUser->about = nl2br($streamUser->about);

        // increase popularity
        $streamUser->increment('popularity');

        // get authenticated user
        $user = auth()->user();
        // if authenticated user == streamuser, show start stream
        $isChannelOwner = false;

        if (auth()->check() && $streamUser->username === $user->username) {
            $isChannelOwner = true;
        }

        // if authenticated user != streamuser, show view stream
        $isChannelLive = false;

        if (auth()->check() && $streamUser->username !== $user->username) {
            if ($streamUser->live_status == true) {
                $isChannelLive = true;
            } else {
                $isChannelLive = false;
            }
        }

        // check if it follows channel
        $userFollowsChannel = false;
        if (auth()->check() && auth()->user()->isFollowing($streamUser)) {
            $userFollowsChannel = true;
        }

        // check if has subscription
        $userIsSubscribed = false;
        if (auth()->check() && auth()->user()->hasSubscriptionTo($streamUser)) {
            $userIsSubscribed = true;
        }

        // build opengraph tags
        $ogTags = [
            'title' => __(":channelName's channel (:handle)", ['channelName' => $streamUser->name, 'handle' => '@' . $streamUser->username]),
            'url' => route('channel', ['user' => $streamUser->username]),
            'image' => $streamUser->cover_picture
        ];


        return Inertia::render('Channel/User', compact('user', 'isChannelOwner', 'isChannelLive', 'streamUser', 'userFollowsChannel', 'userIsSubscribed', 'ogTags'));
    }

    // channel settings
    public function channelSettings()
    {
        Gate::authorize('channel-settings');

        return Inertia::render('Channel/Settings');
    }

    // update channel settings
    public function updateChannelSettings(ChannelSettingsRequest $request)
    {
        Gate::authorize('channel-settings');

        // user
        $user = $request->user();

        // save details to database
        $user->about = $request->about;
        $user->username = $request->username;
        $user->headline = $request->headline;
        $user->save();

        // save category
        $user->categories()->detach();
        $user->categories()->attach($request->category);

        // save profile picture if needed
        if ($request->hasFile('profilePicture')) {
            $profilePicture = Image::make($request->file('profilePicture'));
            $picturePath = 'profilePics/' . $request->user()->id . '-' . uniqid() . '.' . $request->file('profilePicture')->getClientOriginalExtension();

            $profilePicture->fit(80, 80, function ($constrain) {
                $constrain->upsize();
            })->save(public_path($picturePath), 100);

            $user->profile_picture = $picturePath;
            $user->save();
        }

        // save cover picture if needed
        if ($request->hasFile('coverPicture')) {
            $coverPicture = Image::make($request->file('coverPicture'));
            $picturePath = 'coverPics/' . $request->user()->id . '-' . uniqid() . '.' . $request->file('coverPicture')->getClientOriginalExtension();

            $coverPicture->fit(960, 280, function ($constrain) {
                $constrain->upsize();
            })->save(public_path($picturePath), 100);

            $user->cover_picture = $picturePath;
            $user->save();
        }


        return back()->with('message', __("Profile updated"));
    }

    // followers
    public function followers($user, Request $request)
    {
        Gate::authorize('channel-settings');

        $followers = $request->user()->followers;

        return Inertia::render('Channel/Followers', compact('followers'));
    }

    // tiers
    public function getTiers(User $user)
    {
        return $user->tiers;
    }

    // videos
    public function channelVideos(User $user)
    {
        return $user->videos()->with('streamer')->paginate(9);
    }

    // banned users
    public function bannedUsers()
    {
        Gate::authorize('channel-settings');

        $roomBans = auth()->user()->streamerBans()->with('user')->get();

        return Inertia::render('Channel/BannedUsers', compact('roomBans'));
    }


    // banned from room
    public function bannedFromRoom($user)
    {
        // find this room
        $streamUser = User::where('username', $user)->firstOrFail();

        return Inertia::render('Channel/BannedFromRoom', compact('streamUser'));
    }

    // lift user ban
    public function liftUserBan(RoomBans $roomban, Request $r)
    {
        Gate::authorize('channel-settings');


        if ($roomban->streamer_id != $r->user()->id) {
            abort(403);
        }

        $roomban->delete();

        toast(__('User ban lifted'), 'success');

        return back();
    }


    // ban user from room
    public function banUserFromRoom(User $user, Request $r)
    {
        Gate::authorize('channel-settings');

        $ban = $r->user()->streamerBans()->create([
            'user_id' => $user->id,
            'ip' => $user->ip
        ]);


        broadcast(new LiveStreamBan($r->user()));

        return response()->json(['ban' => $ban]);
    }
}
