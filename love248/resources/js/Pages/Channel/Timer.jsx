import React, { useState, useEffect } from 'react';
import { MdOutlineTimer } from "react-icons/md";
import { toast } from "react-toastify";

const Timer = ({ timerData , sendDataToParentTimer}) => {
  const [timer, setTimer] = useState(calculateSeconds(timerData?.stream_time));

  useEffect(() => {
    let interval;
    const streamId = timerData?.id;
    if (timer > 0) {
    
      interval = setInterval(() => {
        setTimer(prevTimer => prevTimer - 1);
      }, 1000);
    }else{
      finishedStreaming(streamId);
    }

    return () => clearInterval(interval); 
  }, [timer, timerData]);

  useEffect(() => {
    setTimer(calculateSeconds(timerData?.stream_time));
  }, [timerData]);

  function calculateSeconds(timeString) {
    if (!timeString) return 0;
    const [minutes, seconds] = timeString.split(':').map(Number);
    return minutes * 60 + seconds;
  }

  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
  }

      const finishedStreaming = (streamId) => {
        axios.post(route("chat.finished-streaming-chat"), {
          streamId,
        })
        .then((resp) => {
          if (resp.data.status === true) {
            sendDataToParentTimer(resp.data);
            console.log("message ",resp?.data?.message);
            toast.success(resp.data.message);
        } else {
            toast.error(resp.data.message);
        }
        })
        .catch((Error) => {
          console.log("Error",Error);
          //  const errors = Error.response.data?.errors;
 
          //   Object.keys(errors).forEach((key) => {
          //       console.log(errors[key][0]);
          //       toast.error(errors[key][0]);
          //   });
        });
      };
  return (
    <div>
      <p> <MdOutlineTimer /> : {formatTime(timer)}</p>
    </div>
  );
};

export default Timer;
