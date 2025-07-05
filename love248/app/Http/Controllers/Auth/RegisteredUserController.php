<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\UniqueUsernameOnSignup;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    // show user type selection page
    public function signup()
    {
        return Inertia::render('Auth/Signup');
    }

    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
       

        $request->validate([
            'username' => ['required', new UniqueUsernameOnSignup(), 'regex:/^[\w-]*$/'],
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_streamer' => ['required', 'in:yes,no'],
            'dob' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'skin_tone' => 'required',
            // 'whatsapp_number' => 'required',
            // 'dob' => 'required|date',
        ],[
            'username.regex' => 'The username may only contain letters, numbers, dashes, and underscores.',
            'username'=>'Username must required',
            'name'=>'Name must required',
            'email'=>'Email must required',
            'password'=>'Password must required',
            'dob.before_or_equal' => 'You must be 18 years old to use the platform.',
            'dob.required' => 'The date of birth is required.',
            // 'whatsapp_number.required' => 'Whatsapp number is required.',
            'dob.date' => 'Please enter a valid date for your date of birth.',
            'skin_tone.required' => 'Please select any skin tone.'
        ]);
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dob' =>  $request->dob ?? '',
            'is_streamer' => $request->is_streamer ?? '',
            'whatsapp_number' => $request->whatsapp_number ?? null,
            'skin_tone' => $request->skin_tone ?? null,
        ]);

        // if category
        if ($request->filled('category')) {
            $user->categories()->attach($request->category);
        }

        // if streamer identity verification is required
        if (opt('streamersIdentityRequired') == 'No' && $request->is_streamer == 'yes') {
            $user->is_streamer_verified = 'yes';
            $user->streamer_verification_sent = 'yes';
            $user->save();
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
