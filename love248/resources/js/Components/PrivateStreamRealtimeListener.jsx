import React, { useEffect, useState } from 'react';
import { toast } from 'react-toastify';
import __ from '@/Functions/Translate';

export default function PrivateStreamRealtimeListener({ 
  streamRequest, 
  onStreamStateChange,
  onCountdownUpdate 
}) {
  const [isConnected, setIsConnected] = useState(false);
  const [connectionError, setConnectionError] = useState(null);

  useEffect(() => {
    // Check if Pusher is properly configured
    if (!window.PUSHER_KEY || window.PUSHER_KEY === '' || window.PUSHER_KEY === 'your_key_here') {
      console.warn('Pusher not configured - real-time features disabled');
      console.warn('Please set PUSHER_APP_KEY and PUSHER_APP_CLUSTER in your .env file');
      return;
    }

    if (!window.Echo || !streamRequest?.id) {
      console.warn('Echo not available or stream request ID missing');
      return;
    }


    
    const channel = window.Echo.private(`private-stream.${streamRequest.id}`);
    
    // Connection success
    channel.subscribed(() => {

      setIsConnected(true);
      setConnectionError(null);
    });

    // Connection error
    channel.error((error) => {
      console.error('❌ Echo connection error:', error);
      setConnectionError(error);
      setIsConnected(false);
      
      // Show user-friendly error message
      if (process.env.NODE_ENV === 'development') {
        toast.error('Real-time connection failed. Check console for details.', {
          position: "top-right",
          autoClose: 5000,
        });
      }
    });

    // Listen for stream state changes
    channel.listen('.stream.state.changed', (e) => {

      
      // Show notification for certain events
      const notifications = {
        'countdown_started': __('Stream preparation has begun'),
        'stream_started': __('Stream is now live!'),
        'user_joined': __('User has joined the stream'),
        'stream_ended': __('Stream has ended'),
        'feedback_submitted': e.data.message || __('Feedback has been submitted'),
      };

      if (notifications[e.event_type]) {
        toast.info(notifications[e.event_type], {
          position: "top-right",
          autoClose: 3000,
        });
      }

      // Update stream state
      if (onStreamStateChange) {
        onStreamStateChange({
          ...streamRequest,
          ...e.stream_request,
        }, e.timing, e.event_type);
      }
    });

    // Listen for countdown updates
    channel.listen('.stream.countdown.update', (e) => {

      
      if (onCountdownUpdate) {
        onCountdownUpdate({
          timeRemaining: e.time_remaining,
          timeUntilUserCanJoin: e.time_until_user_can_join,
          canUserJoin: e.can_user_join,
          canStreamerStart: e.can_streamer_start,
          isInPreparationPeriod: e.is_in_preparation_period,
        });
      }
    });

    // Cleanup on unmount
    return () => {

      channel.stopListening('.stream.state.changed');
      channel.stopListening('.stream.countdown.update');
      window.Echo.leaveChannel(`private-stream.${streamRequest.id}`);
      setIsConnected(false);
    };
  }, [streamRequest?.id]);

  // Show connection status in development
  if (process.env.NODE_ENV === 'development') {
    if (connectionError) {
      console.warn('⚠️ Real-time connection error:', connectionError);
    }
    if (isConnected) {
  
    }
  }

  return null; // This component doesn't render anything
} 