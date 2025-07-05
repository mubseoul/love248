import { AiOutlineArrowRight } from "react-icons/ai";
import { MdGeneratingTokens } from "react-icons/md";
import { useState, useEffect, memo } from "react";
import { Inertia } from "@inertiajs/inertia";
import { FaGrinStars } from "react-icons/fa";
import Spinner from "@/Components/Spinner";
import { toast } from "react-toastify";
import __ from "@/Functions/Translate";
import nl2br from "react-nl2br";
import axios from "axios";
import { Row } from "react-bootstrap";

const TiersTab = memo(({ user }) => {
  const [loading, setLoading] = useState(true);
  const [tiers, setTiers] = useState([]);
  const [tierSubscribed, setTierSubscribed] = useState(0);

  const getTiers = () => {
    axios
      .get(route("streaming.getTiers", { user: user.id }))
      .then((resp) => {
        setTiers(resp.data);
        setLoading(false);
      })
      .catch((Err) => toast.error(Err.response?.data?.message));
  };

  const getSubscriptionStatus = () => {
    setLoading(true);
    axios
      .get(route("subscription.isSubscribed", { user: user.id }))
      .then((resp) => {
        setLoading(false);
        setTierSubscribed(resp.data);
      });
  };

  useEffect(() => {
    getTiers();
    getSubscriptionStatus();
  }, []);

  if (loading) return <Spinner />;
  return (
    <Row className="row-cols-1 row-cols-md-2 row-cols-lg-4">
      {tiers.length === 0 && (
        <div className="fade flex-grow-1 alert alert-warning show">
          {__("Streamer did not set any membership options yet.")}
        </div>
      )}
    </Row>
  );
});

export default TiersTab;
