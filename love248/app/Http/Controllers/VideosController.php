<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File as FileFacade;
use App\Services\VideoProcessingService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewVideoSale;
use App\Models\VideoCategories;
use App\Models\MercadoAccount;
use Illuminate\Http\Request;
use App\Models\Commission;
use App\Models\VideoSales;
use Illuminate\Http\File;
use App\Models\Video;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class VideosController extends Controller
{
    protected $videoService;
    public function __construct(VideoProcessingService $videoService)
    {
        $this->videoService = $videoService;
        $this->middleware('auth')
            ->except(['browse', 'videoPage', 'increaseViews', 'serveThumbnail']);

        // Ensure chunks directory exists in storage
        $chunksDir = storage_path('app/chunks');
        if (!file_exists($chunksDir)) {
            mkdir($chunksDir, 0755, true);
        }
    }

    public function myVideos(Request $request)
    {
        $videos = $request->user()
            ->purchasedVideos()
            ->with('streamer')
            ->latest();

        if ($request->has('search_term')) {
            $videos->where('title', 'LIKE', '%' . $request->search_term . '%');
        }

        $videos = $videos->paginate(4);

        return Inertia::render('Videos/OrderedVideos', compact('videos'));
    }

    public function videoPage(Video $video, String $slug, Request $request)
    {
        $video->load('streamer');

        return Inertia::render('Videos/SingleVideo', compact('video'));
    }

    public function unlockVideo(Video $video, Request $request)
    {
        $video->load('streamer');

        if ($video->canBePlayed) {
            return back()->with('message', __('You already have access to this video'));
        }

        // For free videos, grant access immediately instead of showing unlock page
        if (floatval($video->price) === 0.0) {
            // Check if user already has a sale record to prevent duplicates
            $existingSale = VideoSales::where('video_id', $video->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$existingSale) {
                VideoSales::create([
                    'video_id' => $video->id,
                    'streamer_id' => $video->user_id,
                    'user_id' => $request->user()->id,
                    'price' => 0,
                    'status' => 'completed',
                ]);
            }

            return redirect()->route('video.page', [
                'video' => $video->id, 
                'slug' => $video->slug
            ])->with('message', __("Thank you, you can now play the video!"));
        }

        return Inertia::render('Videos/Unlock', compact('video'));
    }

    public function purchaseVideo(Video $video, Request $request)
    {
        $user = Auth::user();

        if ($video->canBePlayed) {
            return back()->with('message', __('You already have access to this video'));
        }

        // For free videos, grant access immediately
        if ($video->price == 0) {
            $videoSale = VideoSales::create([
                'video_id' => $video->id,
                'streamer_id' => $video->user_id,
                'user_id' => $request->user()->id,
                'price' => 0,
                'status' => 'completed',
            ]);

            return redirect(route('videos.ordered'))->with('message', __("Thank you, you can now play the video!"));
        }

        // For paid videos, redirect to Mercado Pago payment gateway
        return redirect()->route('mercado.videoPurchase', ['tokenPack' => $video->id]);
    }

    public function purchaseVideoWithMercado($video, $request)
    {
        $video = Video::find($video);
        if ($video->canBePlayed) {
            return back()->with('message', __('You already have access to this video'));
        }

        if ($video->price) {
            $price = $video->price;
            $admin_commission = $price * 0.25;
            $streamer_commission = $price * 0.75;

            // Create video sale record
            $videoSale = VideoSales::create([
                'video_id' => $video->id,
                'streamer_id' => $video->user_id,
                'user_id' => $request->user()->id,
                'price' => $price,
                'status' => 'completed',
            ]);

            // Record transaction history
            Transaction::create([
                'user_id' => $request->user()->id,
                'transaction_type' => 'video_purchase',
                'reference_id' => $videoSale->id,
                'reference_type' => VideoSales::class,
                'amount' => $price,
                'currency' => opt('payment-settings.currency_code', 'USD'),
                'payment_method' => 'mercado_pago',
                'payment_id' => null,
                'status' => 'completed',
                'description' => 'Purchase of video: ' . $video->title,
                'metadata' => json_encode([
                    'video_id' => $video->id,
                    'video_title' => $video->title,
                    'streamer_id' => $video->user_id,
                    'streamer_name' => $video->streamer->name,
                    'admin_commission' => $admin_commission,
                    'streamer_commission' => $streamer_commission
                ]),
            ]);

            // Create commission record
            $admin = User::where('is_supper_admin', 'yes')->first();
            if ($admin) {
                Commission::create([
                    'type' => 'Buy Videos',
                    'video_id' => $video->id,
                    'streamer_id' => $video->user_id,
                    'tokens' => $admin_commission,
                    'admin_id' => $admin->id,
                ]);
            }

            $video->streamer->notify(new NewVideoSale($videoSale));

            return redirect(route('videos.ordered'))->with('message', __("Thank you, you can now play the video!"));
        }
    }


    public function increaseViews(Video $video, Request $request)
    {
        $sessionName = ip2long($request->ip()) . '_' . $video->id . '_viewed';

        if (!$request->session()->has($sessionName)) {
            // only increase views if the user didn't already play the video this session
            $video->increment('views');

            // set the session to avoid increasing again
            $request->session()->put($sessionName, date('Y-m-d H:i:s'));

            // return the result
            return response()->json(['result' => 'INCREASED', 'session' => $sessionName]);
        } else {
            return response()->json(['result' => 'ALREADY VIEWED THIS SESSION, NOT INCREASING VIEW COUNT']);
        }
    }

    public function browse(VideoCategories $videocategory = null, String $slug = null)
    {
        $request = request();

        if (!$videocategory) {
            $videos = Video::where('status', 1)->fresh()->with(['category', 'streamer']);
        } else {
            $videos = $videocategory->videos()->with(['category', 'streamer']);
        }

        switch ($request->sort) {
            case 'Most Viewed':
            default:
                $videos = $videos->orderByDesc('views');
                break;

            case 'Recently Uploaded':
                $videos = $videos->orderByDesc('created_at');
                break;

            case 'Older Videos':
                $videos = $videos->orderBy('created_at');
                break;

            case 'Highest Price':
                $videos = $videos->orderByDesc('price');
                break;

            case 'Lowest Price':
                $videos = $videos->orderBy('price');
                break;

            case 'Only Free':
                $videos = $videos->where('price', 0)->orderByDesc('views');
                break;
        }

        // if keyword
        if ($request->filled('search')) {
            $videos->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // case categories
        if ($request->filled('selectedCategories')) {
            $videos->whereHas('category', function ($query) use ($request) {
                $query->whereIn('category_id', $request->selectedCategories);
            });
        }

        // fetch videos
        $videos = $videos->paginate(12)->appends($request->query());

        // the image
        $exploreImage = asset('images/browse-videos-icon.png');

        // all video categories
        $categories = VideoCategories::orderBy('category')->get();

        // assing to simple category
        $category = $videocategory;


        // render the view
        return Inertia::render('Videos/BrowseVideos', compact('videos', 'category', 'exploreImage', 'categories'));
    }

    public function videosManager(Request $request)
    {
        Gate::authorize('channel-settings');

        // Use direct query to Video model instead of going through the user relationship
        // This bypasses the status=1 constraint in the User->videos() relationship
        $videos = Video::where('user_id', $request->user()->id)
            ->with('category')
            ->withSum('sales', 'price')
            ->orderByDesc('id')
            ->paginate(9);

        return Inertia::render('Videos/MyVideos', compact('videos'));
    }

    public function uploadVideos(Request $request)
    {
        $videoCount = Video::where('user_id', Auth::user()->id)->count();
        if ($videoCount >= 10) {
            return redirect()->back()->with('message', __("You can not upload more than 10 videos!"));
        }

        if (Auth::user()->is_streamer === 'yes') {
            $mercadoaccount = MercadoAccount::where('user', Auth::user()->id)->first();
            if ($mercadoaccount === null) {
                return redirect()->back()->with('message', __("You need to connect your mercado account first!"));
            }
        }

        Gate::authorize('channel-settings');

        $video = [
            'id' => null,
            'title' => '',
            'category_id' => '',
            'price' => 0,
            'free_for_subs' => 'no'
        ];

        $categories = VideoCategories::orderBy('category')->get();

        return Inertia::render('Videos/Partials/UploadVideo', compact('video', 'categories'));
    }

    public function editVideo(Video $video)
    {
        Gate::authorize('channel-settings');

        $categories = VideoCategories::orderBy('category')->get();

        return Inertia::render('Videos/Partials/UploadVideo', compact('video', 'categories'));
    }

    public function save(Request $request)
    {
        Gate::authorize('channel-settings');

        $request->validate([
            'title' => 'required|min:2',
            'price' => 'required|numeric',
            'free_for_subs' => 'required|in:yes,no',
            'thumbnail' => 'required|mimes:png,jpg',
            'video_file' => 'required',
            'category_id' => 'required|exists:video_categories,id'
        ]);

        // Create video entry first to get the ID
        $video = $request->user()->videos()->create([
            'title' => $request->title,
            'price' => $request->price,
            'free_for_subs' => $request->free_for_subs,
            'thumbnail' => '', // Will be updated
            'video' => '', // Will be updated
            'disk' => env('FILESYSTEM_DISK'),
            'category_id' => $request->category_id,
            'status' => 0, // Set initial status to pending
        ]);

        // Create the directory structure
        $userId = auth()->id();
        $videoId = $video->id;
        $baseDirectory = "users/{$userId}/videos/{$videoId}";

        // Process thumbnail
        $thumbnail = Image::make($request->file('thumbnail'))->stream();
        $thumbExtension = $request->file('thumbnail')->getClientOriginalExtension();
        $thumbPath = "{$baseDirectory}/thumbnail/thumbnail.{$thumbExtension}";

        // Save the thumbnail
        Storage::disk(env('FILESYSTEM_DISK'))->put($thumbPath, $thumbnail);

        // Process video - copy from temp location to permanent
        $videoPath = "{$baseDirectory}/video/video.mp4";
        $tempVideo = $request->video_file;

        if (Storage::disk(env('FILESYSTEM_DISK'))->exists($tempVideo)) {
            // Copy the video to its final destination
            Storage::disk(env('FILESYSTEM_DISK'))->copy($tempVideo, $videoPath);
            // Delete the temp file
            Storage::disk(env('FILESYSTEM_DISK'))->delete($tempVideo);
        }

        // Update the video record with the new paths
        $video->update([
            'thumbnail' => $thumbPath,
            'video' => $videoPath,
            'last_refreshed_at' => now()
        ]);

        return to_route('videos.list')->with('message', __('Video successfully uploaded'));
    }


    public function updateVideo(Video $video, Request $request)
    {
        Gate::authorize('channel-settings');

        $request->validate([
            'title' => 'required|min:2',
            'price' => 'required|numeric',
            'free_for_subs' => 'required|in:yes,no',
            'category_id' => 'required|exists:video_categories,id'
        ]);

        if ($request->user()->id !== $video->user_id) {
            abort(403, __("You do not seem to be the owner of this video"));
        }

        $userId = $request->user()->id;
        $videoId = $video->id;
        $baseDirectory = "users/{$userId}/videos/{$videoId}";

        // Handle video file if provided
        if ($request->filled('video_file')) {
            $tempVideo = $request->video_file;
            $videoPath = "{$baseDirectory}/video/video.mp4";

            // Make sure the directory exists
            Storage::disk(env('FILESYSTEM_DISK'))->makeDirectory("{$baseDirectory}/video", true);

            if (Storage::disk(env('FILESYSTEM_DISK'))->exists($tempVideo)) {
                // Delete old video if it exists
                if (Storage::disk(env('FILESYSTEM_DISK'))->exists($video->video)) {
                    Storage::disk(env('FILESYSTEM_DISK'))->delete($video->video);
                }

                // Copy the video to its final destination
                Storage::disk(env('FILESYSTEM_DISK'))->copy($tempVideo, $videoPath);
                // Delete the temp file
                Storage::disk(env('FILESYSTEM_DISK'))->delete($tempVideo);

                $video->video = $videoPath;
                $video->save();
            }
        }

        // Handle thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $thumbnail = Image::make($request->file('thumbnail'))->stream();
            $thumbExtension = $request->file('thumbnail')->getClientOriginalExtension();
            $thumbPath = "{$baseDirectory}/thumbnail/thumbnail.{$thumbExtension}";

            // Make sure the directory exists
            Storage::disk(env('FILESYSTEM_DISK'))->makeDirectory("{$baseDirectory}/thumbnail", true);

            // Delete old thumbnail if it exists
            if (Storage::disk(env('FILESYSTEM_DISK'))->exists($video->thumbnail)) {
                Storage::disk(env('FILESYSTEM_DISK'))->delete($video->thumbnail);
            }

            // Save the new thumbnail
            Storage::disk(env('FILESYSTEM_DISK'))->put($thumbPath, $thumbnail);

            $video->thumbnail = $thumbPath;
            $video->save();
        }

        // Update video metadata
        $video->update([
            'title' => $request->title,
            'price' => $request->price,
            'free_for_subs' => $request->free_for_subs,
            'disk' => env('FILESYSTEM_DISK'),
            'category_id' => $request->category_id,
            'last_refreshed_at' => now()
        ]);

        return back()->with('message', __('Video successfully updated'));
    }

    public function uploadChunkedVideo(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 51200 KB = 50 MB
        ], [
            'file.required' => 'The video file is required.',
            'file.file' => 'The video must be a file.',
            'file.uploaded' => 'The file failed to upload. Please ensure it is less than 50 MB.',
            'file.max' => 'The video may not be greater than 50 MB.',
        ]);

        $file = $request->file;
        $is_last = $request->is_last;

        // Create chunks directory if it doesn't exist in storage
        $chunksDirectory = storage_path('app/chunks');
        if (!file_exists($chunksDirectory)) {
            mkdir($chunksDirectory, 0755, true);
        }

        // temp chunks path (using storage instead of public)
        $path = storage_path('app/chunks/' . $file->getClientOriginalName());

        // filename without .part in it
        $withoutPart = basename($path, '.part');

        // set file name inside path without .part
        $renamePath = storage_path('app/chunks/' . $withoutPart);

        // set allowed extensions
        $allowedExt = ['ogg', 'wav', 'mp4', 'webm', 'mov', 'qt'];
        $fileExt = explode('.', $withoutPart);
        $fileExt = end($fileExt);
        $fileExt = strtolower($fileExt);

        // preliminary: validate allowed extensions
        // we're validating true mime later, but just to avoid the effort if fails from the begining
        if (!in_array($fileExt, $allowedExt)) {
            if (file_exists($renamePath)) {
                unlink($renamePath);
            }
            throw new \Exception('Invalid extension');
        }

        // build allowed mimes
        $allowedMimes = [
            'video/mp4',
            'video/webm',
            'video/mov',
            'video/ogg',
            'video/qt',
            'video/quicktime'
        ];

        // append chunk to the file
        file_put_contents($path, $file->get(), FILE_APPEND);

        // finally, let's make the file complete
        if ($is_last == "true") {
            // rename the file to original name
            rename($path, $renamePath);

            // set a ref to local file
            $localFile = new File($renamePath);

            try {
                // first, lets get the mime type
                $finfo = new \finfo();
                $mime = $finfo->file($renamePath, FILEINFO_MIME_TYPE);
            } catch (\Exception $e) {
                $mime = null;
            }

            // validate allowed mimes
            if ($mime) {
                if (!in_array($mime, $allowedMimes) && $mime != 'application/octet-stream') {
                    throw new \Exception('Invalid file type: ' . $mime);
                }

                // this is from chunks, keep it as it passed the other validation
                if ($mime == 'application/octet-stream') {
                    $mime = 'video';
                }
            } else {
                $mime = 'video';
            }

            // Generate a temporary file path
            $fileDestination = 'temp_videos';
            $tempFileName = uniqid() . '_' . basename($renamePath);

            // Store the file in the storage
            $fileContent = file_get_contents($renamePath);
            $fileName = $fileDestination . '/' . $tempFileName;
            Storage::disk(env('FILESYSTEM_DISK'))->put($fileName, $fileContent);

            // Compress the video if needed
            if (method_exists($this->videoService, 'compressVideo')) {
                try {
                    $compressedPath = $this->videoService->compressVideo($renamePath, $fileName);
                    if ($compressedPath) {
                        $fileName = $compressedPath;
                    }
                } catch (\Exception $e) {
                    // Continue with original file if compression fails
                    Log::error('Video compression failed: ' . $e->getMessage());
                }
            }

            // remove it from chunks folder
            if (file_exists($renamePath)) {
                unlink($renamePath);
            }

            return response()->json(['result' => $fileName]);
        } // if is_last
    }

    public function uploadMessageVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:webm,wav,mp4,mov,ogg,qt|max:2000', // Max 2MB
        ]);
        $user = User::findOrFail($request->user);

        if ($request->hasFile('video')) {
            // Create storage directories if they don't exist
            $storageDirectory = storage_path('app/public/users/' . $user->id . '/message');
            if (!file_exists($storageDirectory)) {
                mkdir($storageDirectory, 0755, true);
            }

            // Generate unique filename
            $filename = uniqid() . '.' . $request->file('video')->getClientOriginalExtension();

            // Move the file to storage/app/public directory
            $request->file('video')->move($storageDirectory, $filename);

            // Path relative to storage/app/public
            $relativePath = 'users/' . $user->id . '/message/' . $filename;

            // Delete old video if exists
            if ($user->message_video && file_exists(storage_path('app/public/' . $user->message_video))) {
                unlink(storage_path('app/public/' . $user->message_video));
            }

            $user->message_video = $relativePath;
            $user->save();

            // Return the URL with /storage/ prefix which links to the public access point
            $publicUrl = url('storage/' . $relativePath);

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully!',
                'path' => $publicUrl,
                'file_path' => $relativePath
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'No video file was uploaded.'
        ], 400);
    }

    public function delete(Request $request)
    {
        Gate::authorize('channel-settings');

        // find video
        $video = $request->user()->videos()->findOrFail($request->video);

        $userId = $request->user()->id;
        $videoId = $video->id;
        $baseDirectory = "users/{$userId}/videos/{$videoId}";

        // Delete the entire video directory
        if (Storage::disk($video->disk)->exists($baseDirectory)) {
            Storage::disk($video->disk)->deleteDirectory($baseDirectory);
        } else {
            // Fallback to the old method if directory structure is not found
            Storage::disk($video->disk)->delete($video->video);
            Storage::disk($video->disk)->delete($video->thumbnail);
        }

        // delete video sales
        $video->sales()->delete();

        // delete video
        $video->delete();

        return back()->with('message', __('Video removed'));
    }

    public function refresh(Request $request)
    {
        Gate::authorize('channel-settings');

        // find video
        $video = $request->user()->videos()->findOrFail($request->video);

        // Refresh the video
        $video->refresh();

        return back()->with('message', __('Video refreshed successfully! Your content will remain visible for another 30 days.'));
    }

    /**
     * Stream a video securely
     */
    public function streamVideo(Request $request, $id)
    {
        // Find the video
        $video = Video::findOrFail($id);

        // Check permissions
        if (!$video->canBePlayed) {
            abort(403, __('You do not have permission to view this video'));
        }

        // Check if video is approved or if requester is the owner
        if ($video->status == 0 && (!auth()->check() || auth()->id() != $video->user_id)) {
            abort(403, __('This video is pending approval and not yet available for viewing'));
        }

        // If it's a new format path (containing users/)
        if (Str::contains($video->getRawOriginal('video'), 'users/')) {
            $disk = env('FILESYSTEM_DISK', 'local');
            $path = $video->getRawOriginal('video');

            // Check if file exists
            if (!Storage::disk($disk)->exists($path)) {
                abort(404, __('Video file not found'));
            }

            // Get file content
            $fileContent = Storage::disk($disk)->get($path);

            // Determine mime type from file extension
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $mimeMap = [
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'ogg' => 'video/ogg',
                'mov' => 'video/quicktime',
                'qt' => 'video/quicktime'
            ];
            $mimeType = $mimeMap[strtolower($extension)] ?? 'video/mp4';

            // Return file with proper headers
            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Content-Length', Storage::disk($disk)->size($path));
        }

        // For old format, redirect to the public URL
        return redirect(asset('storage/' . $video->getRawOriginal('video')));
    }

    /**
     * Serve a video thumbnail securely
     */
    public function serveThumbnail(Request $request, $id)
    {
        // Find the video
        $video = Video::findOrFail($id);

        // Thumbnails are publicly accessible to everyone
        // No permission checks needed as thumbnails should be viewable by all

        // If it's a new format path (containing users/)
        if (Str::contains($video->getRawOriginal('thumbnail'), 'users/')) {
            $disk = env('FILESYSTEM_DISK', 'local');
            $path = $video->getRawOriginal('thumbnail');

            // Check if file exists
            if (!Storage::disk($disk)->exists($path)) {
                abort(404, __('Thumbnail file not found'));
            }

            // Get file content
            $fileContent = Storage::disk($disk)->get($path);

            // Determine mime type from file extension
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $mimeMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp'
            ];
            $mimeType = $mimeMap[strtolower($extension)] ?? 'image/jpeg';

            // Return file with proper headers
            return response($fileContent)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline')
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('Content-Length', Storage::disk($disk)->size($path));
        }

        // For old format, redirect to the public URL
        return redirect(asset('storage/' . $video->getRawOriginal('thumbnail')));
    }
}
