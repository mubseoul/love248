import __ from "@/Functions/Translate";
import { MdGeneratingTokens } from "react-icons/md";
import PrimaryButton from "@/Components/PrimaryButton";
import { useState } from "react";
import Modal from "@/Components/Modal";
import InputLabel from "@/Components/InputLabel";
import Textarea from "@/Components/Textarea";
import NumberInput from "@/Components/NumberInput";
import axios from "axios";
import { toast } from "react-toastify";

export default function PrivateStreamTip({ streamRequest, onTipSent }) {
  const [show, setShow] = useState(false);
  const [message, setMessage] = useState("");
  const [tip, setTip] = useState("");
  const [isSendingTip, setIsSendingTip] = useState(false);

  const sendTip = async (e) => {
    e.preventDefault();
    setIsSendingTip(true);

    try {
      const response = await axios.post(
        route("private-stream.tip.send", streamRequest.id),
        {
          tip: parseInt(tip),
          message,
        }
      );

      if (response.data.status) {
        setTip("");
        setMessage("");
        toast.success(__("Thanks, your tip has been sent!"));
        setShow(false);

        // Notify parent component that a tip was sent
        if (onTipSent) {
          onTipSent();
        }
      } else {
        toast.error(response.data.message || __("Failed to send tip"));
      }
    } catch (error) {
      const errors = error.response?.data?.errors;

      if (errors) {
        Object.keys(errors).forEach((key) => {
          toast.error(errors[key][0]);
        });
      } else {
        toast.error(error.response?.data?.message || __("Error sending tip"));
      }
    } finally {
      setIsSendingTip(false);
    }
  };

  return (
    <>
      <Modal show={show} onClose={(e) => setShow(false)} maxWidth="xs">
        <div className="p-5 text-center">
          <h3 className="text-lg mb-3 justify-center flex items-center font-semibold text-gray-primary">
            <MdGeneratingTokens className="mr-2 h-6 w-6" />
            {__("Send Tip")}
          </h3>

          <form onSubmit={sendTip}>
            <InputLabel
              className="text-base text-gray-primary text-left"
              forInput="tokens"
              value={__("Token Amount")}
            />

            <NumberInput
              type="number"
              name="tokens"
              min={1}
              className="w-full mt-2 form-control"
              value={tip}
              required
              handleChange={(e) => setTip(e.target.value)}
              disabled={isSendingTip}
            />

            <InputLabel
              className="text-base mt-4 text-gray-primary text-left"
              forInput={"message"}
              value={__("Message")}
            />

            <Textarea
              className="w-full mt-2 form-control"
              value={message}
              required
              handleChange={(e) => setMessage(e.target.value)}
              disabled={isSendingTip}
            />

            <div className="iq-button">
              <PrimaryButton
                className="mt-3"
                disabled={isSendingTip || !tip || !message.trim()}
              >
                {isSendingTip ? __("Sending...") : __("Send Tip")}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>

      <button
        onClick={(e) => setShow(true)}
        className="p-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg disabled:opacity-50 flex items-center"
        title={__("Send Tip")}
      >
        <MdGeneratingTokens className="h-4 w-4" />
      </button>
    </>
  );
}
