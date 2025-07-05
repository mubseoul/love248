<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Events\PrivateChatMessageEvent;
use App\Events\LivePrivateStreamStarted;
use App\Events\LiveStreamRefresh;
use App\Events\LiveStreamStarted;
use App\Events\LiveStreamStopped;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\StreamingPrice;
use App\Models\StreamingTime;
use App\Models\PrivateStream;
use App\Models\Commission;

use Auth;
use Redirect;
use Exception;

class ChatController extends Controller
{
    // latest messages
    public function latestMessages(String $roomName)
    {
        $messages = Chat::where(['roomName'=> $roomName , 'chat_type' => 'public'])->latest()->take(50)->get();
        return $messages->reverse()->flatten();
    }

    // send message
    public function sendMessage(User $user, Request $request)
    {
        if (!auth()->check()) {
            return abort(403, __("You must be logged in to chat!"));
        }
        $request->validate(['message' => 'required']);
        $roomName = 'room-' . $user->username;
        $tokens = $user->tokens ?? '';
        if(empty($tokens) || $tokens < 0){
            return Redirect::route('token.packages')->with('message', __('By Token Packages .'));
        }
        $chat = Chat::create([
            'roomName' => $request->roomName ?? $roomName,
            'chat_type' => $request->chatType ?? 'public',
            'user_id' => $request->user()->id,
            'streamer_id' => $user->id,
            'message' => $request->message
        ]);



        broadcast(new ChatMessageEvent($chat));

        return response()->json(['result' => $chat->id]);
    }

    public function sendPrivateRequest(Request $request){
        try {
            // Check if streaming ID is provided
            if (empty($request->streamingId)) {
                return response()->json(['status' => false, 'message' => __("Please select time and tokens!")]);
            }

            $user = Auth::user();

            // Retrieve streaming data
            $streamingData = StreamingPrice::where('id', $request->streamingId)->with('getStreamerPrice')->first();

            // Don't allow tipping yourself
            if ($user->id === $streamingData->streamer_id) {
                return response()->json(['status' => false, 'message' => __("Do not send private chat to yourself!")]);
            }

            // Validate if user's balance is enough
            if ($streamingData->token_amount > $user->tokens) {
                return response()->json(['status' => false, 'message' => __("Your balance is not enough for sending a private chat!")]);
            }

            // Create private stream
            $privateStream = PrivateStream::create([
                'streamer_id' => $streamingData->streamer_id,
                'user_id' => $user->id,
                'tokens' => $streamingData->token_amount,
                'stream_time' => $streamingData->getStreamerPrice->streaming_time ?? '',
                'message' => $request->message ?? '',
            ]);
            $streamer = User::find($streamingData->streamer_id);
            $userToken = $user->tokens - $streamingData->token_amount;
            $StreamerToken =$streamer->tokens + $streamingData->token_amount;
            User::where('id',$user->id)->update(['tokens' =>$userToken]);
            User::where('id' ,$streamingData->streamer_id)->update(['tokens' => $StreamerToken]);
            return response()->json(['status' => true, 'message' => __("Private request sent successfully!")]);
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    }

    public function sendPrivateRequestWithMercado(Request $request)
    {
        try {
            // Check if streaming ID is provided
            if (empty($request->streamingId)) {
                return response()->json(['status' => false, 'message' => __("Please select time and tokens!")]);
            }

            $user = Auth::user();

            // Retrieve streaming data
            $streamingData = StreamingPrice::where('id', $request->streamingId)->with('getStreamerPrice')->first();

            // Don't allow tipping yourself
            if ($user->id === $streamingData->streamer_id) {
                return response()->json(['status' => false, 'message' => __("Do not send private chat to yourself!")]);
            }

            // Validate if user's balance is enough
            if ($streamingData->token_amount > $user->tokens) {
                return response()->json(['status' => false, 'message' => __("Your balance is not enough for sending a private chat!")]);
            }

            // Create private stream
            $privateStream = PrivateStream::create([
                'streamer_id' => $streamingData->streamer_id,
                'user_id' => $user->id,
                'tokens' => $streamingData->token_amount,
                'stream_time' => $streamingData->getStreamerPrice->streaming_time ?? '',
                'message' => $request->message ?? '',
            ]);
            $streamer = User::find($streamingData->streamer_id);
            $userToken = $user->tokens - $streamingData->token_amount;
            $StreamerToken = $streamer->tokens + $streamingData->token_amount;
            User::where('id', $user->id)->update(['tokens' => $userToken]);
            User::where('id', $streamingData->streamer_id)->update(['tokens' => $StreamerToken]);
            return response()->json(['status' => true, 'message' => __("Private request sent successfully!")]);
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    }

    public function getPrivateRequest(Request $request){
        try {
            $user = Auth::user();
            $privateStream = PrivateStream::where('streamer_id', $user->id)
                                        ->where('status', '!=', 'conform') // Assuming 'conform' is a status value
                                        ->with('getUsersInfo')
                                        ->get();
    
            return response()->json(['status' => true, 'data'=> $privateStream]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    }
    
    public function cancelStreaming($id){
        try{
           $streamerData =  PrivateStream::findOrFail($id);
           $result = $this->TokensStatusUpadet($streamerData);
           if ($result) {
                $streamerData->delete();
               return response()->json(['status' => true, 'message' => __("Cancel request sent successfully!")]);
           } else {
            return response()->json(['status' => false, 'message' => __("try again !")]);
           }
            
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    }
    public function acceptStreaming($id){
        try{
           $streamerData =  PrivateStream::findOrFail($id);
           if($streamerData){
            PrivateStream::where('id',$id)->update(['status' => 'accept']);
            return response()->json(['status' => true, 'message' => __("Accept request sent successfully!")]);
           }
           
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    }
    public function TokensStatusUpadet($streamerData) {
        try {
            $streamer = User::findOrFail($streamerData->streamer_id);
            $users = User::findOrFail($streamerData->user_id);

            $userToken = $users->tokens + $streamerData->tokens;
            $streamerToken = $streamer->tokens - $streamerData->tokens;

            User::where('id', $users->id)->update(['tokens' => $userToken]);
            User::where('id', $streamerData->streamer_id)->update(['tokens' => $streamerToken]);

            return true; // Indicate success
        } catch (Exception $e) {
            // Log the error or handle it as needed
            \Log::error('Error updating tokens status: ' . $e->getMessage());
            return false; // Indicate failure
        }
    }

  
    public function privateChatStart(Request $request){
        try{
            $privateStream = PrivateStream::findOrFail($request->streamingId);

            if ($privateStream->status === 'accept') {
                $user = Auth::user();
                $chatType = 'private-' . time() . '_' . $user->username;

                $chat = Chat::create([
                    'roomName' => $roomName = 'room-' . $user->username,
                    'chat_type' => $chatType,
                    'user_id' => $privateStream->user_id,
                    'streamer_id' => $privateStream->streamer_id,
                    'message' => 'start streaming'
                ]);
                
                broadcast(new LiveStreamRefresh());
                event(new PrivateChatMessageEvent($chat ,$privateStream));
                broadcast(new LiveStreamStarted($user));
                event(new LivePrivateStreamStarted($user ,$privateStream,$chat));
                $messages = Chat::where('chat_type', $chatType)->latest()->take(50)->get();
                $chatMessage = $messages->reverse()->flatten();

                return response()->json([
                    'status' => true,
                    'streamerData' => $privateStream,
                    'chatType' => $chatType,
                    'chatMessage' => $chatMessage,
                    'message' => __("Accept request sent successfully!")
                ]);
            } else {
                return response()->json(['status' => false, 'message' => __("Please Accept request !")]);
            }
           
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    } 

    public function finishedStreamingChat(Request $request){
        try{
          $privateStream = PrivateStream::findOrFail($request->streamId);
            if ($privateStream->status === 'accept') {
                $user = Auth::user();
                $chatType = 'public';

                $chat = Chat::create([
                    'roomName' => $roomName = 'room-' . $user->username,
                    'chat_type' => $chatType,
                    'user_id' => $privateStream->user_id,
                    'streamer_id' => $privateStream->streamer_id,
                    'message' => 'finished streaming'
                ]);

                $admin = User::where('is_supper_admin', 'yes')->first();
                $tokens = $privateStream->tokens;
                $admin_token = $tokens * 0.25;
                $streamer_token = $tokens * 0.75;
        
                Commission::create([
                    'type' => 'Private Streaming',
                    'video_id' => $privateStream->id,
                    'streamer_id' => $user->id,
                    'tokens' => $admin_token,
                    'admin_id' => $admin->id,
                ]);

                $user->increment('tokens', $streamer_token);
                $admin->increment('tokens', $admin_token);


                PrivateStream::where('id',$privateStream->id)->update(['status' => 'conform']);
                event(new PrivateChatMessageEvent($chat ,$privateStream));
                broadcast(new LiveStreamStopped($user));
                broadcast(new LiveStreamRefresh());
                $messages = Chat::where('chat_type', $chatType)->latest()->take(50)->get();
                $chatMessage = $messages->reverse()->flatten();

                return response()->json([
                    'status' => true,
                    'streamerData' => $privateStream,
                    'chatType' => $chatType,
                    'chatMessage' => $chatMessage,
                    'message' => __("Finished streaming successfully!")
                ]);
            } else {
                return response()->json(['status' => false, 'message' => __("Please Accept request !")]);
            }
           
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    } 

    public function reStartStreaming(Request $request){
        try{
            $user = Auth::user();
            // fire socket event
            broadcast(new LiveStreamStarted($user));
            broadcast(new LiveStreamRefresh());
            return response()->json([
                'status' => true,
                'message' => __("Public streaming start !")
            ]);
           
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    } 
    public function stopStreaming(Request $request){
        try{
            $user = Auth::user();
            // fire socket event
            broadcast(new LiveStreamStopped($user));
            broadcast(new LiveStreamRefresh());
            return response()->json([
                'status' => true,
                'message' => __("Public streaming stoped !")
            ]);
           
        } catch (Exception $e) {
            // Handle exceptions
            return response()->json(['status' => false, 'message' => __("An error occurred. Please try again later.")]);
        }
    } 
}
