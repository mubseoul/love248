<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private stream channel authorization
Broadcast::channel('private-stream.{streamId}', function ($user, $streamId) {
    // Get the stream request
    $streamRequest = \App\Models\PrivateStreamRequest::find($streamId);
    
    if (!$streamRequest) {
        return false;
    }
    
    // Allow access if user is either the streamer or the requester
    return $user->id === $streamRequest->streamer_id || $user->id === $streamRequest->user_id;
});

Broadcast::channel('room-{username}', fn () => true);
