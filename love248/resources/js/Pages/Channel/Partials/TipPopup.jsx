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

export default function TipPopup({ streamer }) {
  const [show, setShow] = useState(false);
  const [message, setMessage] = useState("");
  const [tip, setTip] = useState("");

  const sendTip = (e) => {
    e.preventDefault();

    axios
      .post(route("tips.send"), {
        streamer: streamer.id,
        tip,
        message,
      })
      .then((resp) => {
        if (resp.data.result == "ok") {
          setTip("");
          setMessage("");
          toast.success(__("Thanks, your tip has been sent!"));
          setShow(false);
        } else {
          toast.error(resp.data.result);
        }
      })
      .catch((Error) => {
        const errors = Error.response.data?.errors;

        Object.keys(errors).forEach((key) => {
          console.log(errors[key][0]);
          toast.error(errors[key][0]);
        });
      });
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
              handleChange={(e) => setTip(e.target.value)}
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
            />

            <div className="iq-button">
              <PrimaryButton className="mt-3">{__("Send Tip")}</PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>

      <PrimaryButton
        onClick={(e) => setShow(true)}
        className="btn btn-sm d-flex align-items-center justify-content-center me-2 text-uppercase btn btn-primary"
      >
        <MdGeneratingTokens className="mr-1" /> {__("Tip")}
      </PrimaryButton>
    </>
  );
}
