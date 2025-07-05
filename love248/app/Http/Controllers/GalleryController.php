<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File as FileFacade;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewGallerySale;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\VideoCategories;
use App\Models\MercadoAccount;
use App\Models\GallerySales;
use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Http\File;
use App\Models\Gallery;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')
            ->except(['browse', 'videoPage', 'increaseViews']);
    }

    public function myGallery(Request $request)
    {
        $gallery = $request->user()
            ->purchasedGallery()
            ->with('streamer')
            ->latest();

        if ($request->has('search_term')) {
            $gallery->where('title', 'LIKE', '%' . $request->search_term . '%');
        }

        $gallery = $gallery->paginate(4);

        return Inertia::render('Gallery/OrderedGallery', compact('gallery'));
    }

    public function galleryPage(Gallery $gallery, String $slug, Request $request)
    {
        $gallery->load('streamer');

        return Inertia::render('Gallery/SingleGallery', compact('gallery'));
    }

    public function unlockGallery(Gallery $gallery, Request $request)
    {
        $gallery->load('streamer');

        if ($gallery->canBePlayed) {
            return back()->with('message', __('You already have access to this Gallery'));
        }

        // For free galleries, grant access immediately instead of showing unlock page
        if (floatval($gallery->price) === 0.0) {
            // Check if user already has a sale record to prevent duplicates
            $existingSale = GallerySales::where('gallery_id', $gallery->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$existingSale) {
                GallerySales::create([
                    'gallery_id' => $gallery->id,
                    'streamer_id' => $gallery->user_id,
                    'user_id' => $request->user()->id,
                    'price' => 0,
                    'status' => 'completed',
                ]);
            }

            return redirect()->route('gallery.page', [
                'gallery' => $gallery->id, 
                'slug' => $gallery->slug
            ])->with('message', __("Thank you, you can now play the gallery!"));
        }

        return Inertia::render('Gallery/Unlock', compact('gallery'));
    }

    public function purchaseGallery(Gallery $gallery, Request $request)
    {
        $user = Auth::user();

        if ($gallery->canBePlayed) {
            return back()->with('message', __('You already have access to this Gallery'));
        }

        // For free galleries, grant access immediately
        if ($gallery->price == 0) {
            $gallerySale = GallerySales::create([
                'gallery_id' => $gallery->id,
                'streamer_id' => $gallery->user_id,
                'user_id' => $request->user()->id,
                'price' => 0,
                'status' => 'completed',
            ]);

            return redirect(route('gallery.ordered'))->with('message', __("Thank you, you can now play the Gallery!"));
        }

        // For paid galleries, redirect to Mercado Pago payment gateway
        return redirect()->route('mercado.purchaseTokenss', ['tokenPack' => $gallery->id]);
    }

    public function purchaseGalleryWithMercado($gallery, $request)
    {
        $gallery = Gallery::find($gallery);
        if ($gallery->canBePlayed) {
            return back()->with('message', __('You already have access to this Gallery'));
        }
        // dd($admin);

        if ($gallery->price) {
            $tokens = $gallery->price;
            $streamer_token = $tokens * 0.75;


            $gallerySale = GallerySales::create([
                'gallery_id' => $gallery->id,
                'streamer_id' => $gallery->user_id,
                'user_id' => $request->user()->id,
                'price' => $streamer_token,
                'status' => 'completed',
            ]);

            $gallery->streamer->notify(new NewGallerySale($gallerySale));

            return redirect()->route('gallery.ordered')->with('message', __("Thank you, you can now play the Gallery!"));
        }
    }


    public function increaseViews(Gallery $gallery, Request $request)
    {
        $sessionName = ip2long($request->ip()) . '_' . $gallery->id . '_viewed';

        if (!$request->session()->has($sessionName)) {
            // only increase views if the user didn't already play the gallery this session
            $gallery->increment('views');

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
            $gallery = Gallery::where('status', 1)->fresh()->with(['category', 'streamer']);
        } else {
            $gallery = $videocategory->gallery()->with(['category', 'streamer']);
        }

        switch ($request->sort) {
            case 'Most Viewed':
            default:
                $gallery = $gallery->orderByDesc('views');
                break;

            case 'Recently Uploaded':
                $gallery = $gallery->orderByDesc('created_at');
                break;

            case 'Older Gallery':
                $gallery = $gallery->orderBy('created_at');
                break;

            case 'Highest Price':
                $gallery = $gallery->orderByDesc('price');
                break;

            case 'Lowest Price':
                $gallery = $gallery->orderBy('price');
                break;

            case 'Only Free':
                $gallery = $gallery->where('price', 0)->orderByDesc('views');
                break;
        }

        // if keyword
        if ($request->filled('search')) {
            $gallery->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // case categories
        if ($request->filled('selectedCategories')) {
            $gallery->whereHas('category', function ($query) use ($request) {
                $query->whereIn('category_id', $request->selectedCategories);
            });
        }

        // fetch videos

        $gallery = $gallery->paginate(12)->appends($request->query());
        // the image
        $exploreImage = asset('images/image-box.png');

        // all video categories
        $categories = VideoCategories::orderBy('category')->get();

        // assing to simple category
        $category = $videocategory;


        // render the view
        return Inertia::render('Gallery/BrowseGallery', compact('gallery', 'category', 'exploreImage', 'categories'));
    }

    public function galleryManager(Request $request)
    {
        Gate::authorize('channel-settings');

        $gallery = $request->user()->gallery()
            ->with('category')
            ->withSum('sales', 'price')
            ->orderByDesc('id')
            ->paginate(9);

        return Inertia::render('Gallery/MyGallery', compact('gallery'));
    }

    public function uploadGallery(Request $request)
    {
        $galleryCount = Gallery::where('user_id', Auth::user()->id)->count();
        if ($galleryCount >= 10) {
            return redirect()->back()->with('message', __("You can not upload more than 10 videos!"));
        }

        if (Auth::user()->is_streamer === 'yes') {
            $mercadoaccount = MercadoAccount::where('user', Auth::user()->id)->first();
            if ($mercadoaccount === null) {
                return redirect()->back()->with('message', __("You need to connect your mercado account first!"));
            }
        }

        Gate::authorize('channel-settings');

        $gallery = [
            'id' => null,
            'title' => '',
            'category_id' => '',
            'price' => 0,
            'free_for_subs' => 'no'
        ];

        $categories = VideoCategories::orderBy('category')->get();

        return Inertia::render('Gallery/Partials/UploadGallery', compact('gallery', 'categories'));
    }

    public function editGallery(Gallery $gallery)
    {
        Gate::authorize('channel-settings');

        $categories = VideoCategories::orderBy('category')->get();

        return Inertia::render('Gallery/Partials/UploadGallery', compact('gallery', 'categories'));
    }

    public function save(Request $request)
    {
        // Authorize the action
        Gate::authorize('channel-settings');

        // Validate the request
        $request->validate([
            'title' => 'required|min:2',
            'price' => 'required|numeric',
            'free_for_subs' => 'required|in:yes,no',
            'thumbnail' => 'required|mimes:png,jpg|max:5120',
            'category_id' => 'required|exists:video_categories,id'
        ], [
            'thumbnail.required' => 'The image file is required.',
            'thumbnail.file' => 'The image must be a file.',
            'thumbnail.max' => 'The image may not be greater than 5 MB.',
        ]);

        // Create gallery entry first to get the ID
        $gallery = $request->user()->gallery()->create([
            'title' => $request->title,
            'price' => $request->price,
            'free_for_subs' => $request->free_for_subs,
            'thumbnail' => '', // Will be updated
            'disk' => env('FILESYSTEM_DISK'),
            'category_id' => $request->category_id,
            'status' => 0,
        ]);

        // Create the directory structure
        $userId = auth()->id();
        $galleryId = $gallery->id;
        $baseDirectory = "users/{$userId}/gallery/{$galleryId}";

        // Process thumbnail
        $thumbnail = Image::make($request->file('thumbnail'))->encode('jpg', 80)->stream();
        $thumbExtension = $request->file('thumbnail')->getClientOriginalExtension();
        $thumbPath = "{$baseDirectory}/thumbnail.{$thumbExtension}";

        // Make sure the directory exists
        Storage::disk(env('FILESYSTEM_DISK'))->makeDirectory($baseDirectory, true);

        // Save the thumbnail
        Storage::disk(env('FILESYSTEM_DISK'))->put($thumbPath, $thumbnail);
        Storage::disk(env('FILESYSTEM_DISK'))->setVisibility($thumbPath, 'public');

        // Optimize the image if available
        try {
            $optimizer = OptimizerChainFactory::create();
            $optimizer->optimize($thumbPath);
        } catch (\Exception $e) {
            // Log error but continue
        }

        // Update the gallery record with the new path
        $gallery->update([
            'thumbnail' => $thumbPath,
            'last_refreshed_at' => now()
        ]);

        return to_route('gallery.list')->with('message', __('Image successfully uploaded'));
    }

    public function updateGallery(Gallery $gallery, Request $request)
    {
        Gate::authorize('channel-settings');

        $request->validate([
            'title' => 'required|min:2',
            'price' => 'required|numeric',
            'free_for_subs' => 'required|in:yes,no',
            'category_id' => 'required|exists:video_categories,id'
        ]);

        if ($request->user()->id !== $gallery->user_id) {
            abort(403, __("You do not seem to be the owner of this gallery"));
        }

        $userId = $request->user()->id;
        $galleryId = $gallery->id;
        $baseDirectory = "users/{$userId}/gallery/{$galleryId}";

        // Handle thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            $thumbnail = Image::make($request->file('thumbnail'))->encode('jpg', 80)->stream();
            $thumbExtension = $request->file('thumbnail')->getClientOriginalExtension();
            $thumbPath = "{$baseDirectory}/thumbnail.{$thumbExtension}";

            // Make sure the directory exists
            Storage::disk(env('FILESYSTEM_DISK'))->makeDirectory($baseDirectory, true);

            // Delete old thumbnail if it exists
            if (Storage::disk(env('FILESYSTEM_DISK'))->exists($gallery->thumbnail)) {
                Storage::disk(env('FILESYSTEM_DISK'))->delete($gallery->thumbnail);
            }

            // Save the new thumbnail
            Storage::disk(env('FILESYSTEM_DISK'))->put($thumbPath, $thumbnail);
            Storage::disk(env('FILESYSTEM_DISK'))->setVisibility($thumbPath, 'public');

            // Optimize the image if available
            try {
                $optimizer = OptimizerChainFactory::create();
                $optimizer->optimize($thumbPath);
            } catch (\Exception $e) {
                // Log error but continue
            }

            $gallery->thumbnail = $thumbPath;
            $gallery->save();
        }

        // Update gallery metadata
        $gallery->update([
            'title' => $request->title,
            'price' => $request->price,
            'free_for_subs' => $request->free_for_subs,
            'disk' => env('FILESYSTEM_DISK'),
            'category_id' => $request->category_id,
            'last_refreshed_at' => now()
        ]);

        return back()->with('message', __('Gallery successfully updated'));
    }

    public function delete(Request $request)
    {
        Gate::authorize('channel-settings');

        // find gallery
        $gallery = $request->user()->gallery()->findOrFail($request->gallery);

        $userId = $request->user()->id;
        $galleryId = $gallery->id;
        $baseDirectory = "users/{$userId}/gallery/{$galleryId}";

        // Delete the entire gallery directory
        if (Storage::disk($gallery->disk)->exists($baseDirectory)) {
            Storage::disk($gallery->disk)->deleteDirectory($baseDirectory);
        } else {
            // Fallback to the old method if directory structure is not found
            Storage::disk($gallery->disk)->delete($gallery->thumbnail);
        }

        // delete gallery sales
        $gallery->sales()->delete();

        // delete gallery
        $gallery->delete();

        return back()->with('message', __('Image removed'));
    }

    public function refresh(Request $request)
    {
        Gate::authorize('channel-settings');

        // find gallery
        $gallery = $request->user()->gallery()->findOrFail($request->gallery);

        // Refresh the gallery
        $gallery->refresh();

        return back()->with('message', __('Gallery refreshed successfully! Your content will remain visible for another 30 days.'));
    }

    /**
     * Serve gallery thumbnail directly from storage
     */
    public function serveGalleryThumbnail(Gallery $gallery)
    {
        // Get the original thumbnail path from the database
        $thumbnailPath = $gallery->getRawOriginal('thumbnail');

        if (empty($thumbnailPath)) {
            abort(404);
        }

        $disk = $gallery->disk ?? env('FILESYSTEM_DISK', 'public');

        if (!Storage::disk($disk)->exists($thumbnailPath)) {
            abort(404);
        }

        $file = Storage::disk($disk)->get($thumbnailPath);

        // Get the file extension and determine the MIME type
        $extension = pathinfo($thumbnailPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        $type = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
