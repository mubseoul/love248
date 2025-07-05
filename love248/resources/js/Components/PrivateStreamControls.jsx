import React, { useState } from 'react';
import __ from '@/Functions/Translate';
import { toast } from 'react-toastify';
import {
  MdVideoCall,
  MdAccessTime,
  MdPersonAdd,
  MdStream,
} from 'react-icons/md';

export default function PrivateStreamControls({ 
  streamRequest, 
  isStreamer, 
  streamTiming, 
  onStreamStateChange 
}) {
  const [isLoading, setIsLoading] = useState(false);

  // Format countdown to stream start
  const formatCountdownToStart = (seconds) => {
    if (seconds <= 0) return null;

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) {
      return `${hours}h ${minutes}m ${secs}s`;
    } else if (minutes > 0) {
      return `${minutes}m ${secs}s`;
    } else {
      return `${secs}s`;
    }
  };

  const handleStartCountdown = async () => {
    setIsLoading(true);
    try {
      const response = await axios.post(
        route('private-stream.start-countdown', streamRequest.id)
      );
      
      if (response.data.status) {
        toast.success(response.data.message);
        if (onStreamStateChange) {
          onStreamStateChange(response.data.stream_request);
        }
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.error('Error starting countdown:', error);
      if (error.response?.data?.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error(__('Failed to start stream'));
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleStartStream = async () => {
    setIsLoading(true);
    try {
      const response = await axios.post(
        route('private-stream.start-stream', streamRequest.id)
      );
      
      if (response.data.status) {
        toast.success(response.data.message);
        if (onStreamStateChange) {
          onStreamStateChange(response.data.stream_request);
        }
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.error('Error starting stream:', error);
      if (error.response?.data?.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error(__('Failed to start stream'));
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleJoinStream = async () => {
    setIsLoading(true);
    try {
      const response = await axios.post(
        route('private-stream.mark-user-joined', streamRequest.id)
      );
      
      if (response.data.status) {
        toast.success(response.data.message);
        if (onStreamStateChange) {
          onStreamStateChange(response.data.stream_request);
        }
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.error('Error joining stream:', error);
      if (error.response?.data?.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error(__('Failed to join stream'));
      }
    } finally {
      setIsLoading(false);
    }
  };

  // Render different controls based on stream state and user role
  if (isStreamer) {
    // Streamer controls
    return (
      <div className="flex flex-col space-y-4">
        {/* Action buttons */}
        <div className="flex justify-center space-x-3">
          {streamRequest.status === 'accepted' && !streamRequest.countdown_started_at && (
            <button
              onClick={handleStartCountdown}
              disabled={isLoading}
              className="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white rounded-lg font-medium disabled:cursor-not-allowed text-sm"
            >
              <MdAccessTime className="mr-2" />
              {isLoading ? __('Starting...') : __('Start Streaming Setup')}
            </button>
          )}

          {streamRequest.status === 'in_progress' && !streamRequest.actual_start_time && (
            <div className="flex flex-col items-center space-y-2">
              <div className="text-center text-xs text-gray-300">
                <p>{__('You are now in preparation mode')}</p>
                <p className="text-blue-400">
                  {streamTiming.canStartActualStream 
                    ? __('You can now start the stream manually')
                    : __('You can start the stream at the scheduled time')
                  }
                </p>
                {!streamTiming.canStartActualStream && streamTiming.timeUntilStartSeconds > 0 && (
                  <div className="mt-2">
                    <p className="text-gray-400 mb-1 text-xs">{__('Can go live in')}:</p>
                    <div className="text-sm font-mono font-bold text-white bg-gray-700 px-2 py-1 rounded inline-block">
                      {formatCountdownToStart(streamTiming.timeUntilStartSeconds)}
                    </div>
                  </div>
                )}
              </div>
              <button
                onClick={handleStartStream}
                disabled={isLoading || !streamTiming.canStartActualStream}
                className={`flex items-center px-4 py-2 ${
                  streamTiming.canStartActualStream 
                    ? 'bg-green-600 hover:bg-green-700' 
                    : 'bg-gray-600 cursor-not-allowed'
                } disabled:bg-gray-600 text-white rounded-lg font-medium disabled:cursor-not-allowed text-sm`}
              >
                <MdStream className="mr-2" />
                {isLoading ? __('Starting...') : (
                  streamTiming.canStartActualStream 
                    ? __('Go Live Now') 
                    : __('Wait for Scheduled Time')
                )}
              </button>
            </div>
          )}

          {streamRequest.actual_start_time && !streamRequest.stream_ended_at && (
            <div className="flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium text-sm">
              <MdVideoCall className="mr-2" />
              <span>{__('Stream Active')} - {__('Will end automatically')}</span>
            </div>
          )}
        </div>

        {/* Duration info when streaming */}
        {streamRequest.actual_start_time && !streamRequest.stream_ended_at && (
          <div className="text-center text-sm text-gray-400">
            <p>{__('Stream Duration')}: {streamRequest.duration_minutes} {__('minutes')}</p>
            <p className="mt-1">{__('Stream will end automatically when duration expires')}</p>
          </div>
        )}
      </div>
    );
  } else {
    // User controls
    return (
      <div className="flex flex-col space-y-4">
        {/* Join button */}
        <div className="flex justify-center">
          {streamTiming.canUserJoin && !streamRequest.user_joined && (
            <button
              onClick={handleJoinStream}
              disabled={isLoading}
              className="flex items-center justify-center px-6 py-3 bg-primary hover:bg-primary-dark text-white rounded-lg font-medium"
            >
              <MdPersonAdd className="mr-2" />
              {isLoading ? __('Joining...') : __('Join Stream')}
            </button>
          )}

          {streamRequest.user_joined && (
            <div className="flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium">
              <MdVideoCall className="mr-2" />
              {__('Connected to Stream')}
            </div>
          )}
        </div>

        {/* Duration info */}
        <div className="text-center text-sm text-gray-400">
          <p>{__('Stream Duration')}: {streamRequest.duration_minutes} {__('minutes')}</p>
          {streamRequest.actual_start_time && !streamRequest.stream_ended_at && (
            <p className="mt-1">{__('Stream will end automatically when duration expires')}</p>
          )}
          {/* {!streamTiming.canUserJoin && streamTiming.timeUntilStartSeconds > 0 && (
            <div className="mt-3">
              <p className="text-gray-400 mb-1">{__('Stream starts in')}:</p>
              <div className="text-lg font-mono font-bold text-white bg-footer border border-gray-600 px-3 py-1 rounded inline-block">
                {formatCountdownToStart(streamTiming.timeUntilStartSeconds)}
              </div>
            </div>
          )} */}
        </div>
      </div>
    );
  }
} 