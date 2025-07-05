<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StreamingPrice;
use App\Models\PrivateStream;
use App\Models\StreamingTime;
use App\Models\StreamerAvailability;
use Inertia\Inertia;

class StreamerAvailabilityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the streaming availability slots.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch from new table (room rental fees are now admin-controlled)
        $streamerData = StreamerAvailability::where('streamer_id', $user->id)->get();

        // Note: Room rental fees are now controlled by admin settings, not individual streamer rates

        return Inertia::render('Streaming/addStreaming', compact('streamerData'));
    }

    /**
     * Store a newly created streaming availability in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'tokens_per_minute' => 'nullable|numeric', // Made optional since admin setting is used
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'days_of_week' => 'required|array|min:1', // At least one day must be selected
        ]);

        $user = Auth::user();

        // Ensure days_of_week is properly formatted as a comma-separated string
        $daysOfWeek = is_array($request->days_of_week)
            ? implode(',', $request->days_of_week)
            : $request->days_of_week;

        // Create availability record
        $availability = StreamerAvailability::create([
            'streamer_id' => $user->id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days_of_week' => $daysOfWeek,
            'tokens_per_minute' => $request->tokens_per_minute ?? opt('private_room_rental_tokens_per_minute', 5), // Use admin setting if not provided
        ]);

        // Create a record for admin to allow this streaming option
        PrivateStream::create([
            'user_id' => 1, // Admin user ID
            'status' => 'confirmed',
            'message' => 'Admin Allowed',
            'streamer_id' => $user->id,
            'tokens' => opt('private_room_rental_tokens_per_minute', 5), // Use admin setting
            'stream_time' => $request->start_time . ' - ' . $request->end_time,
        ]);

        return back()->with('message', __("Streaming availability added successfully!"));
    }

    /**
     * Show the form for editing the specified streaming availability.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        // Find in new table (room rental fees are now admin-controlled)
        $streamerData = StreamerAvailability::where('id', $id)->first();

        if (!$streamerData) {
            return redirect()->back()->with('error', __('Streaming availability not found'));
        }

        return Inertia::render('Streaming/EditStreamer', compact('streamerData'));
    }

    /**
     * Update the specified streaming availability in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'tokens_per_minute' => 'nullable|numeric', // Made optional since admin setting is used
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'days_of_week' => 'required|array|min:1', // At least one day must be selected
        ]);

        $id = $request->streamering_id;

        // Ensure days_of_week is properly formatted as a comma-separated string
        $daysOfWeek = is_array($request->days_of_week)
            ? implode(',', $request->days_of_week)
            : $request->days_of_week;

        // Check if it exists in the new table
        $availability = StreamerAvailability::find($id);

        if ($availability) {
            // Update in the new table
            $availability->update([
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'days_of_week' => $daysOfWeek,
                'tokens_per_minute' => $request->tokens_per_minute ?? opt('private_room_rental_tokens_per_minute', 5), // Use admin setting if not provided
            ]);
        } else {
            return to_route('streamer.availability.index')->with('error', __('Streaming availability not found'));
        }

        return to_route('streamer.availability.index')->with('message', __('Streaming availability successfully updated'));
    }

    /**
     * Remove the specified streaming availability from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // Try to delete from new table
        $deleted = StreamerAvailability::where('id', $id)->delete();

        // If not found in new table, try old structure
        if (!$deleted) {
            $dataDetails = StreamingPrice::find($id);
            if ($dataDetails) {
                StreamingTime::where('id', $dataDetails->streamer_time_id)->delete();
                StreamingPrice::where('id', $id)->delete();
            }
        }

        return back()->with('message', __('Streaming availability successfully deleted'));
    }

    /**
     * Get streaming availability data for API.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailabilityData($id)
    {
        // Try to fetch from new table first
        $streamerData = StreamerAvailability::where('streamer_id', $id)->get();

        // If no data in new table, fallback to old structure
        if ($streamerData->isEmpty()) {
            $streamerData = StreamingPrice::where('streamer_id', $id)->with('getStreamerPrice')->get();
        }

        return response()->json([
            'streamerData' => $streamerData,
            'status'       => true,
        ], 200);
    }
}
