import __ from "@/Functions/Translate";
import { MdGeneratingTokens } from "react-icons/md";
import PrimaryButton from "@/Components/PrimaryButton";
import { useState, useEffect } from "react";
import Modal from "@/Components/Modal";
import InputLabel from "@/Components/InputLabel";
import Textarea from "@/Components/Textarea";
import axios from "axios";
import { toast } from "react-toastify";
import {
  FaCalendarAlt,
  FaClock,
  FaMoneyBillWave,
  FaCreditCard,
  FaStripe,
  FaPaypal,
  FaChevronLeft,
  FaChevronRight,
} from "react-icons/fa";
import { loadStripe } from "@stripe/stripe-js";
import {
  CardElement,
  Elements,
  useStripe,
  useElements,
} from "@stripe/react-stripe-js";
import { usePage } from "@inertiajs/inertia-react";

// Duration options in minutes
const DURATION_OPTIONS = [3, 5, 7];

// Payment methods available
const PAYMENT_METHODS = [
  { id: "stripe", name: "Credit Card (Escrow)", icon: FaStripe, hasEscrow: true },
  { id: "mercado_pago", name: "Mercado Pago (Escrow)", icon: FaCreditCard, hasEscrow: true },
];

// Days of the week
const DAYS = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// Component for displaying the private chat request form with payment integration
function PrivateChatForm({
  streamer,
  onClose,
  paymentMethod,
  onRequestComplete,
  userRequests,
  onViewRequests,
}) {
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(false);
  const [availableDates, setAvailableDates] = useState([]);
  const [currentMonth, setCurrentMonth] = useState(new Date());
  const [timeSlots, setTimeSlots] = useState([]);
  const [selectedDate, setSelectedDate] = useState(null);
  const [selectedTimeSlot, setSelectedTimeSlot] = useState(null);
  const [duration, setDuration] = useState(5); // Default to 5 minutes
  const [message, setMessage] = useState("");
  const [userOffer, setUserOffer] = useState(30); // Default offer in currency
  const [paymentProcessing, setPaymentProcessing] = useState(false);
  const [buttonClicked, setButtonClicked] = useState(false);
  const [paymentCompleted, setPaymentCompleted] = useState(false);
  const [paymentError, setPaymentError] = useState(null);
  const [clientSecret, setClientSecret] = useState(null);
  const [requestId, setRequestId] = useState(null);
  const [roomRentalTokens, setRoomRentalTokens] = useState(0);
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState(
    paymentMethod || "stripe"
  );
  const [userTokens, setUserTokens] = useState(0);
  const [hasEnoughTokens, setHasEnoughTokens] = useState(true);
  const [currentYear, setCurrentYear] = useState(new Date().getFullYear());
  const [paymentOptions, setPaymentOptions] = useState({});
  const [email, setEmail] = useState("");

  // Get current user and their tokens from auth
  const { auth } = usePage().props;

  // Initialize user tokens from auth
  useEffect(() => {
    if (auth?.user?.tokens) {
      setUserTokens(auth.user.tokens);
    }
  }, [auth]);

  // Check if user has enough tokens whenever room rental tokens change
  useEffect(() => {
    if (roomRentalTokens > 0) {
      setHasEnoughTokens(userTokens >= roomRentalTokens);
    }
  }, [roomRentalTokens, userTokens]);

  // Only initialize Stripe elements if Stripe is the selected payment method
  const stripe = selectedPaymentMethod === "stripe" ? useStripe() : null;
  const elements = selectedPaymentMethod === "stripe" ? useElements() : null;

  // Load available dates when the component mounts
  useEffect(() => {
    if (streamer?.id) {
      fetchAvailableDates();
    }
  }, [streamer]);

  // Fetch user's token balance
  const fetchUserTokenBalance = async () => {
    try {
      const response = await axios.get(route("user.token-balance"));
      if (response.data.status) {
        setUserTokens(response.data.tokens || 0);
      }
    } catch (error) {
      console.error("Error fetching token balance:", error);
    }
  };

  // Fetch available payment options
  const fetchPaymentOptions = async (streamerFee) => {
    try {
      const response = await axios.post(route("private-stream.payment-options"), {
        streamer_fee: streamerFee
      });
      if (response.data.status) {
        setPaymentOptions(response.data.payment_options);
        
        // Auto-select recommended payment method if available
        if (response.data.recommended) {
          setSelectedPaymentMethod(response.data.recommended);
        }
      }
    } catch (error) {
      console.error("Error fetching payment options:", error);
      
      // Handle subscription requirement error from HTTP error response
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        if (errorData.required_level && errorData.redirect_url) {
          toast.error(errorData.message);
          setTimeout(() => {
            window.location.href = errorData.redirect_url;
          }, 2000); // Redirect after 2 seconds
          return;
        }
      }
    }
  };

  // Calculate room rental tokens whenever duration or selected time slot changes
  useEffect(() => {
    if (selectedTimeSlot && duration) {
      const tokensPerMinute = selectedTimeSlot.tokens_per_minute || 0;
      setRoomRentalTokens(tokensPerMinute * duration);
    }
  }, [selectedTimeSlot, duration]);

  // Fetch available dates from the API
  const fetchAvailableDates = async () => {
    try {
      setLoading(true);
      const response = await axios.get(
        route("private-stream.dates", { streamerId: streamer.id })
      );

      if (response.data.status) {
        // Convert dates strings to Date objects
        const formattedDates = response.data.dates.map((date) => ({
          ...date,
          dateObj: new Date(date.date),
        }));
        setAvailableDates(formattedDates);
      } else {
        // Handle subscription requirement error
        if (response.data.required_level && response.data.redirect_url) {
          toast.error(response.data.message);
          setTimeout(() => {
            window.location.href = response.data.redirect_url;
          }, 2000); // Redirect after 2 seconds
        } else {
          toast.error(
            response.data.message ||
              __("No available dates found for this streamer.")
          );
        }
      }

      // Fetch user's token balance
      await fetchUserTokenBalance();
    } catch (error) {
      console.error("Error fetching available dates:", error);
      
      // Handle subscription requirement error from HTTP error response
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        if (errorData.required_level && errorData.redirect_url) {
          toast.error(errorData.message);
          setTimeout(() => {
            window.location.href = errorData.redirect_url;
          }, 2000); // Redirect after 2 seconds
          return;
        }
      }
      
      toast.error(__("Failed to load available dates. Please try again."));
    } finally {
      setLoading(false);
    }
  };

  // Fetch time slots for a specific date
  const fetchTimeSlots = async (date) => {
    try {
      setLoading(true);
      const response = await axios.post(
        route("private-stream.time-slots", { streamerId: streamer.id }),
        {
          date: date,
        }
      );

      if (response.data.status && response.data.timeSlots.length > 0) {
        setTimeSlots(response.data.timeSlots);
        setSelectedTimeSlot(null); // Reset selected time slot
      } else {
        setTimeSlots([]);
        // Handle subscription requirement error
        if (response.data.required_level && response.data.redirect_url) {
          toast.error(response.data.message);
          setTimeout(() => {
            window.location.href = response.data.redirect_url;
          }, 2000); // Redirect after 2 seconds
        } else {
          toast.info(
            response.data.message || __("No available time slots for this date.")
          );
        }
      }
    } catch (error) {
      console.error("Error fetching time slots:", error);
      
      // Handle subscription requirement error from HTTP error response
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        if (errorData.required_level && errorData.redirect_url) {
          toast.error(errorData.message);
          setTimeout(() => {
            window.location.href = errorData.redirect_url;
          }, 2000); // Redirect after 2 seconds
          return;
        }
      }
      
      toast.error(__("Failed to load time slots. Please try again."));
    } finally {
      setLoading(false);
    }
  };

  // Handle date selection
  const handleDateSelect = (date) => {
    setSelectedDate(date);
    fetchTimeSlots(date.date);
    setStep(2);
  };

  // Handle time slot selection
  const handleTimeSlotSelect = (timeSlot) => {
    setSelectedTimeSlot(timeSlot);
    setStep(3);
  };

  // Handle duration selection and price confirmation
  const handleConfirmDurationAndPrice = async () => {
    if (!hasEnoughTokens) {
      toast.error(__("You don't have enough tokens for this private stream."));
      return;
    }
    
    // Fetch payment options based on the offer amount
    await fetchPaymentOptions(userOffer);
    setStep(4);
  };

  // Navigate to the previous month in the calendar
  const goToPreviousMonth = () => {
    setCurrentMonth(
      new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1)
    );
  };

  // Navigate to the next month in the calendar
  const goToNextMonth = () => {
    setCurrentMonth(
      new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1)
    );
  };

  // Navigate to token packages page
  const navigateToTokenPackages = () => {
    window.location.href = route("token.packages");
  };

  // Create a private stream request
  const createRequest = async (e) => {
    e.preventDefault();

    // Prevent multiple simultaneous requests
    if (paymentProcessing || buttonClicked) {
      return;
    }

    if (selectedPaymentMethod === "stripe" && (!stripe || !elements)) {
      toast.error(
        __("Payment method not properly initialized. Please try again later.")
      );
      return;
    }

    if (selectedPaymentMethod === "mercado_pago" && !email) {
      toast.error(__("Please enter your email address for Mercado Pago payment."));
      return;
    }

    if (selectedPaymentMethod === "mercado_pago" && email && !/\S+@\S+\.\S+/.test(email)) {
      toast.error(__("Please enter a valid email address."));
      return;
    }

    try {
      setButtonClicked(true);
      setPaymentProcessing(true);

      // Create the stream request
      const requestData = {
        streamer_id: streamer.id,
        availability_id: selectedTimeSlot.id,
        requested_date: selectedDate.date,
        requested_time: selectedTimeSlot.time,
        duration_minutes: duration,
        streamer_fee: userOffer,
        message: message,
        payment_method: selectedPaymentMethod,
      };

      // Add email for Mercado Pago payments
      if (selectedPaymentMethod === "mercado_pago") {
        requestData.email = email;
      }

      const response = await axios.post(route("private-stream.create"), requestData);


      if (response.data.status) {
        // Handle payment based on selected method
        if (selectedPaymentMethod === "stripe") {
          setClientSecret(response.data.client_secret);
          const cardElement = elements.getElement(CardElement);

          const result = await stripe.confirmCardPayment(
            response.data.client_secret,
            {
              payment_method: {
                card: cardElement,
                billing_details: {
                  name: window.auth?.user?.name || "",
                },
              },
            }
          );

          if (result.error) {
            setPaymentError(result.error.message);
            toast.error(result.error.message);
          } else {
            // Payment confirmed, now finalize the request
            const confirmResponse = await axios.post(
              route("private-stream.confirm-payment"),
              {
                payment_reference: response.data.payment_reference,
                payment_intent_id: result.paymentIntent.id,
                payment_method: selectedPaymentMethod,
              }
            );

            if (confirmResponse.data.status) {
              setRequestId(confirmResponse.data.request_id);
              setPaymentCompleted(true);
              toast.success(__("Your private stream request has been sent!"));
              // Call the callback to refresh the requests list
              if (typeof onRequestComplete === "function") {
                onRequestComplete();
              }
            } else {
              toast.error(
                confirmResponse.data.message || __("Failed to confirm payment.")
              );
            }
          }
        } else if (selectedPaymentMethod === "mercado_pago") {
          // Handle Mercado Pago payment flow
          if (response.data.redirect_url) {
            toast.success(__("Redirecting to Mercado Pago..."));
            window.location.href = response.data.redirect_url;
          } else if (response.data.preference_url) {
            toast.success(__("Redirecting to Mercado Pago..."));
            window.location.href = response.data.preference_url;
          } else {
            toast.error(__("Mercado Pago payment flow could not be initialized."));
          }
        }
      } else {
        // Handle case where tokens are insufficient or other errors
        if (response.data.redirect) {
          if (response.data.tokens_missing) {
            toast.error(response.data.message);
            // Update the user token balance with the server's value
            setUserTokens(response.data.token_balance || 0);
            setHasEnoughTokens(false);
            // Go back to step 3 (duration selection) to show the "Buy Tokens" button
            setStep(3);
          } else {
            toast.info(__("You need to purchase more tokens first."));
            navigateToTokenPackages();
          }
        } else {
          // Handle subscription requirement error
          if (response.data.required_level && response.data.redirect_url) {
            toast.error(response.data.message);
            setTimeout(() => {
              window.location.href = response.data.redirect_url;
            }, 2000); // Redirect after 2 seconds
          } else {
            toast.error(response.data.message || __("Failed to create request."));
          }
        }
      }
    } catch (error) {
      console.error("Error creating stream request:", error);
      
      // Handle subscription requirement error from HTTP error response
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        if (errorData.required_level && errorData.redirect_url) {
          toast.error(errorData.message);
          setTimeout(() => {
            window.location.href = errorData.redirect_url;
          }, 2000); // Redirect after 2 seconds
          return;
        }
      }
      
      setPaymentError(error.message || __("An unexpected error occurred."));
      toast.error(__("Failed to process request. Please try again."));
    } finally {
      setPaymentProcessing(false);
      setButtonClicked(false);
    }
  };

  // Generate calendar for current month
  const renderCalendar = () => {
    const firstDayOfMonth = new Date(
      currentMonth.getFullYear(),
      currentMonth.getMonth(),
      1
    );
    const lastDayOfMonth = new Date(
      currentMonth.getFullYear(),
      currentMonth.getMonth() + 1,
      0
    );

    // Create an array of day cells
    const days = [];

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < firstDayOfMonth.getDay(); i++) {
      days.push(<div key={`empty-${i}`} className="h-10"></div>);
    }

    // Current date for comparison
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Add cells for each day of the month
    for (let day = 1; day <= lastDayOfMonth.getDate(); day++) {
      const date = new Date(
        currentMonth.getFullYear(),
        currentMonth.getMonth(),
        day
      );

      // Check if this date is available - use timezone-safe formatting
      const formattedDate = date.getFullYear() + '-' + 
        String(date.getMonth() + 1).padStart(2, '0') + '-' + 
        String(date.getDate()).padStart(2, '0');
      const isAvailable = availableDates.some((d) => d.date === formattedDate);

      const matchingDate = availableDates.find((d) => d.date === formattedDate);

      days.push(
        <div
          key={day}
          className={`flex items-center justify-center h-10 rounded-full transition-colors ${
            isAvailable
              ? "cursor-pointer hover:bg-primary/20 hover:text-white"
              : "opacity-30"
          } ${
            matchingDate &&
            selectedDate &&
            matchingDate.date === selectedDate.date
              ? "bg-primary/30 text-white"
              : ""
          }`}
          onClick={() => {
            if (isAvailable) {
              handleDateSelect(matchingDate);
            }
          }}
        >
          {day}
        </div>
      );
    }

    return days;
  };

  // Render the content based on the current step
  const renderStepContent = () => {
    switch (step) {
      case 1: // Calendar date selection
        return (
          <div className="h-full">
            <h4 className="text-base font-medium text-gray-primary mb-3">
              {__("Select a date")}
            </h4>

            {loading ? (
              <div className="flex justify-center my-5">
                <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
              </div>
            ) : (
              <div className="w-full  mx-auto">
                <div className="flex justify-between items-center mb-4">
                  <button
                    onClick={goToPreviousMonth}
                    className="p-2 rounded-full hover:bg-gray-700"
                  >
                    <FaChevronLeft className="text-gray-400" />
                  </button>
                  <div className="text-center text-gray-primary font-medium">
                    {currentMonth.toLocaleString("default", {
                      month: "long",
                      year: "numeric",
                    })}
                  </div>
                  <button
                    onClick={goToNextMonth}
                    className="p-2 rounded-full hover:bg-gray-700"
                  >
                    <FaChevronRight className="text-gray-400" />
                  </button>
                </div>

                <div className="grid grid-cols-7 gap-1 mb-2">
                  {DAYS.map((day) => (
                    <div
                      key={day}
                      className="text-center text-sm text-gray-400"
                    >
                      {day}
                    </div>
                  ))}
                </div>

                <div className="grid grid-cols-7 gap-1">{renderCalendar()}</div>

                <div className="mt-4 text-sm text-gray-400 flex items-center">
                  <div className="w-3 h-3 rounded-full bg-primary/30 mr-2"></div>
                  <span>{__("Available dates")}</span>
                </div>
              </div>
            )}
          </div>
        );

      case 2: // Time slot selection
        return (
          <div className="h-full">
            <div className="flex items-center mb-3">
              <button
                className="text-sm text-red-600 hover:text-red-500 flex items-center"
                onClick={() => setStep(1)}
              >
                <FaChevronLeft className="mr-1 text-red-600" />
                {__("Back to calendar")}
              </button>
            </div>

            <h4 className="text-base font-medium text-gray-primary mb-3">
              {__("Select a time slot for")} {selectedDate?.display}
            </h4>

            {loading ? (
              <div className="flex justify-center my-5">
                <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
              </div>
            ) : timeSlots.length > 0 ? (
              <div className="grid grid-cols-2 md:grid-cols-3 gap-2 overflow-y-auto pr-1">
                {timeSlots.map((slot, index) => {
                  // Convert UTC time to user's local timezone for display
                  const localTime = slot.utc_datetime 
                    ? new Date(slot.utc_datetime).toLocaleTimeString([], { 
                        hour: 'numeric', 
                        minute: '2-digit',
                        hour12: true 
                      })
                    : slot.display; // fallback to original display if no UTC datetime
                  
                  return (
                    <button
                      key={index}
                      className={`flex items-center p-3 rounded border ${
                        selectedTimeSlot?.id === slot.id
                          ? "border-primary bg-primary/20"
                          : "border-gray-600 hover:border-primary"
                      } transition-colors`}
                      onClick={() => handleTimeSlotSelect(slot)}
                    >
                      <FaClock className="mr-2 text-gray-300" />
                      <span className="text-gray-primary">{localTime}</span>
                    </button>
                  );
                })}
              </div>
            ) : (
              <div className="text-center p-4 border border-gray-700 rounded">
                <p className="text-gray-primary">
                  {__("No available time slots for this date.")}
                </p>
              </div>
            )}
          </div>
        );

      case 3: // Duration and price setting
      case 4: // Payment method selection
      case 5: // Success
        // All other steps remain the same structurally but need step-container class
        const stepContent = originalRenderStepContent();

        return <div className="flex flex-col">{stepContent}</div>;

      default:
        return null;
    }
  };

  // Original render step content for steps 3-5
  const originalRenderStepContent = () => {
    switch (step) {
      case 3: // Duration and price setting
        return (
          <>
            <div className="flex items-center mb-3">
              <button
                className="text-sm text-red-600 hover:text-red-500 flex items-center"
                onClick={() => setStep(2)}
              >
                <FaChevronLeft className="mr-1 text-red-600" />
                {__("Back to time slots")}
              </button>
            </div>

            <div className="mb-5">
              <h4 className="text-base font-medium text-gray-primary mb-3">
                {__("Booking details")}
              </h4>

              <div className="p-3 bg-black rounded mb-4">
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <span className="text-sm text-gray-400">{__("Date:")}</span>
                    <p className="text-gray-primary">{selectedDate?.display}</p>
                  </div>
                  <div>
                    <span className="text-sm text-gray-400">{__("Time:")}</span>
                    <p className="text-gray-primary">
                      {selectedTimeSlot?.display}
                    </p>
                  </div>
                  <div>
                    <span className="text-sm text-gray-400">{__("Rate:")}</span>
                    <p className="text-gray-primary">
                      {selectedTimeSlot?.tokens_per_minute} {__("tokens/min")}
                    </p>
                  </div>
                  <div>
                    <span className="text-sm text-gray-400">
                      {__("Your balance:")}
                    </span>
                    <p
                      className={`text-gray-primary ${
                        hasEnoughTokens ? "" : "text-red-400"
                      }`}
                    >
                      {userTokens} {__("tokens")}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <h4 className="text-base font-medium text-gray-primary mb-3">
              {__("Select duration")}
            </h4>

            <div className="grid grid-cols-3 gap-2 mb-4">
              {DURATION_OPTIONS.map((mins) => (
                <button
                  key={mins}
                  className={`p-3 rounded border ${
                    duration === mins
                      ? "border-primary bg-primary/20"
                      : "border-gray-600 hover:border-primary"
                  } transition-colors`}
                  onClick={() => setDuration(mins)}
                >
                  <span className="text-gray-primary">
                    {mins} {__("minutes")}
                  </span>
                </button>
              ))}
            </div>

            <div className="mb-4">
              <label className="block text-sm text-gray-400 mb-1">
                {__("Your Offer (USD):")}
              </label>
              <input
                type="number"
                min="1"
                step="1"
                value={userOffer}
                onChange={(e) => setUserOffer(parseInt(e.target.value) || 1)}
                className="w-full form-control"
              />
            </div>

            <div className="p-3 bg-black rounded mb-4">
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-primary">
                  {__("Room rental:")}
                </span>
                <span className="text-sm font-medium text-gray-primary">
                  {roomRentalTokens} {__("tokens")}
                </span>
              </div>
            </div>

            <div className="mb-4">
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-400">
                  {__("Token balance:")}
                </span>
                <span
                  className={`text-sm font-medium ${
                    hasEnoughTokens ? "text-green-500" : "text-red-500"
                  }`}
                >
                  {userTokens} {__("tokens")}
                </span>
              </div>
            </div>

            {!hasEnoughTokens && (
              <div className="p-2 bg-red-500/10 border border-red-500/30 rounded mb-4">
                <p className="text-sm text-red-400">
                  {__("You need ")}
                  <span className="font-medium">
                    {roomRentalTokens - userTokens}
                  </span>
                  {__(" more tokens to rent this private room.")}
                </p>
              </div>
            )}

            <div className="mb-4">
              <label className="block text-sm text-gray-400 mb-1">
                {__("Message to streamer:")}
              </label>
              <Textarea
                value={message}
                handleChange={(e) => setMessage(e.target.value)}
                className="w-full form-control"
                rows={2}
              />
            </div>

            <div className="mt-auto">
              {hasEnoughTokens ? (
                <PrimaryButton
                  onClick={handleConfirmDurationAndPrice}
                  className="w-full justify-center"
                >
                  {__("Continue to Payment")}
                </PrimaryButton>
              ) : (
                <PrimaryButton
                  onClick={navigateToTokenPackages}
                  className="w-full justify-center bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-700"
                >
                  {__("Buy Tokens")}
                </PrimaryButton>
              )}
            </div>
          </>
        );

      case 4: // Payment method selection
        return (
          <>
            <div className="flex items-center mb-3">
              <button
                className="text-sm text-red-600 hover:text-red-500 flex items-center"
                onClick={() => setStep(3)}
              >
                <FaChevronLeft className="mr-1 text-red-600" />
                {__("Back to booking details")}
              </button>
            </div>

            <div className="mb-5 p-3 bg-black rounded">
              <div className="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <span className="text-sm text-gray-400">{__("Date:")}</span>
                  <p className="text-gray-primary">{selectedDate?.display}</p>
                </div>
                <div>
                  <span className="text-sm text-gray-400">{__("Time:")}</span>
                  <p className="text-gray-primary">
                    {selectedTimeSlot?.display}
                  </p>
                </div>
                <div>
                  <span className="text-sm text-gray-400">
                    {__("Duration:")}
                  </span>
                  <p className="text-gray-primary">
                    {duration} {__("minutes")}
                  </p>
                </div>
                <div>
                  <span className="text-sm text-gray-400">
                    {__("Room rental:")}
                  </span>
                  <p className="text-gray-primary">
                    {roomRentalTokens} {__("tokens")}
                  </p>
                </div>
                <div className="col-span-2">
                  <span className="text-sm text-gray-400">
                    {__("Your Offer:")}
                  </span>
                  <p className="text-gray-primary">${userOffer}</p>
                </div>
                <div className="col-span-2">
                  <span className="text-sm text-gray-400">
                    {__("Your token balance:")}
                  </span>
                  <p className="text-gray-primary">
                    {userTokens} {__("tokens")}
                    <span className="text-green-500 ml-2">✓</span>
                  </p>
                </div>
              </div>
            </div>

            <h4 className="text-base font-medium text-gray-primary mb-3">
              {__("Select payment method")}
            </h4>

            <div className="space-y-3 mb-4">
              {PAYMENT_METHODS.map((method) => {
                const Icon = method.icon;
                const isAvailable = paymentOptions[method.id]?.available !== false;
                const isDisabled = !isAvailable;
                
                return (
                  <button
                    key={method.id}
                    className={`w-full p-3 rounded border flex items-center justify-between ${
                      selectedPaymentMethod === method.id
                        ? "border-primary bg-primary/20"
                        : isDisabled 
                          ? "border-gray-700 opacity-50 cursor-not-allowed"
                          : "border-gray-600 hover:border-primary"
                    } transition-colors`}
                    onClick={() => !isDisabled && setSelectedPaymentMethod(method.id)}
                    disabled={isDisabled}
                  >
                    <div className="flex items-center">
                      <Icon className="mr-3 text-xl text-gray-300" />
                      <div className="text-left">
                        <span className="text-gray-primary block">{method.name}</span>
                        {method.hasEscrow && (
                          <span className="text-xs text-green-400">✓ Escrow Protection</span>
                        )}
                        {!method.hasEscrow && (
                          <span className="text-xs text-yellow-400">⚠ Direct Payment</span>
                        )}
                      </div>
                    </div>
                    {!isAvailable && (
                      <span className="text-xs text-red-400">Unavailable</span>
                    )}
                  </button>
                );
              })}
            </div>

            {selectedPaymentMethod === "stripe" && (
              <div className="mb-4">
                <label className="block text-sm text-gray-400 mb-1">
                  {__("Card details:")}
                </label>
                <div className="p-3 rounded bg-gray-700 border border-gray-600">
                  <CardElement
                    options={{
                      style: {
                        base: {
                          fontSize: "16px",
                          color: "#D1D5DB",
                          "::placeholder": {
                            color: "#9CA3AF",
                          },
                        },
                        invalid: {
                          color: "#F87171",
                        },
                      },
                    }}
                  />
                </div>
                {paymentError && (
                  <p className="mt-2 text-sm text-red-400">{paymentError}</p>
                )}
              </div>
            )}

            {selectedPaymentMethod === "mercado_pago" && (
              <div className="mb-4">
                <label className="block text-sm text-gray-400 mb-1">
                  {__("Email for payment:")}
                </label>
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="w-full form-control bg-black border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-primary"
                  placeholder={__("Enter your email address")}
                  required
                />
                <p className="mt-1 text-xs text-gray-400">
                  {__("This email will be used for the Mercado Pago payment process")}
                </p>
              </div>
            )}

            <div className="text-xs text-gray-400 mb-3">
              {__(
                "Note: Room rental tokens will be deducted from your balance. Your offer amount will be charged via your selected payment method."
              )}
            </div>

            <div className="mt-auto">
              <PrimaryButton
                onClick={createRequest}
                className="w-full justify-center"
                disabled={
                  paymentProcessing ||
                  buttonClicked ||
                  (selectedPaymentMethod === "stripe" && !stripe) ||
                  (selectedPaymentMethod === "mercado_pago" && !email)
                }
              >
                {(paymentProcessing || buttonClicked) ? (
                  <span className="flex items-center">
                    <div className="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white mr-2"></div>
                    {__("Processing...")}
                  </span>
                ) : (
                  __("Send Request")
                )}
              </PrimaryButton>
            </div>
          </>
        );

      case 5: // Success
        return (
          <div className="flex flex-col items-center justify-center h-full">
            <div className="text-green-500 mb-3">
              <svg
                className="w-16 h-16"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M5 13l4 4L19 7"
                />
              </svg>
            </div>
            <h4 className="text-xl font-medium text-gray-primary mb-3">
              {__("Request Sent!")}
            </h4>
            <p className="text-gray-primary mb-4">
              {__(
                "Your private stream request has been sent to the streamer. You'll be notified when they respond."
              )}
            </p>
            <PrimaryButton onClick={onClose}>{__("Close")}</PrimaryButton>
          </div>
        );

      default:
        return null;
    }
  };

  // Show success screen if payment is completed
  useEffect(() => {
    if (paymentCompleted) {
      setStep(5);
    }
  }, [paymentCompleted]);

  // Count active requests (pending + accepted)
  const activeRequestsCount =
    userRequests?.filter(
      (req) => req.status === "pending" || req.status === "accepted"
    ).length || 0;

  return (
    <div className="p-4 w-full">
      {/* Header with Request Info */}
      <div className="flex items-center justify-between mb-3">
        <h3 className="text-xl flex items-center font-semibold text-gray-primary">
          <MdGeneratingTokens className="mr-2 h-6 w-6" />
          {__("Request Private Stream")}
        </h3>
        {activeRequestsCount > 0 && (
          <button
            onClick={onViewRequests}
            className="text-blue-400 hover:text-blue-300 text-sm flex items-center"
          >
            {__("View My Requests")} ({activeRequestsCount})
            <svg
              className="w-4 h-4 ml-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </button>
        )}
      </div>

      {/* Existing Requests Summary (if any) */}
      {activeRequestsCount > 0 && (
        <div className="mb-4 p-3 bg-blue-900/30 border border-blue-500/30 rounded-lg">
          <p className="text-sm text-blue-300">
            {__("You have")}{" "}
            <span className="font-medium">{activeRequestsCount}</span>{" "}
            {__("active request(s) with this streamer.")}
          </p>
          <p className="text-xs text-blue-400 mt-1">
            {__(
              "You can still request additional sessions with different times."
            )}
          </p>
        </div>
      )}

      <div className="h-[420px] overflow-y-auto pr-1 sm:h-[390px]">
        {renderStepContent()}
      </div>
    </div>
  );
}

// Wrapper component that provides the Stripe elements only if needed
function PaymentMethodWrapper({ children, paymentMethod }) {
  if (paymentMethod === "stripe") {
    return <>{children}</>;
  }
  return children;
}

// Main component that wraps the form with Stripe Elements
export default function PrivateChat({ streamer, stripePublicKey }) {
  const [show, setShow] = useState(false);
  const [stripePromise, setStripePromise] = useState(null);
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState("stripe"); // Default to stripe
  const [userRequests, setUserRequests] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("new-request"); // 'new-request' or 'my-requests'

  useEffect(() => {
    // Initialize Stripe when the component mounts
    if (!stripePromise && selectedPaymentMethod === "stripe") {
      const publishableKey = stripePublicKey;
      setStripePromise(loadStripe(publishableKey));
    }
  }, [selectedPaymentMethod, stripePublicKey]);

  // Fetch user's requests for this streamer
  useEffect(() => {
    if (streamer?.id) {
      fetchUserRequests();
    }
  }, [streamer]);

  const fetchUserRequests = async () => {
    try {
      setLoading(true);
      const response = await axios.get(
        route("private-stream.user-requests", {
          streamerId: streamer.id,
        })
      );

      if (response.data.status) {
        const requests = response.data.requests || [];
        setUserRequests(requests);
      }
    } catch (error) {
      console.error("Error fetching user requests:", error);
    } finally {
      setLoading(false);
    }
  };

  const openModal = async () => {
    // Check subscription level before opening modal
    try {
      const response = await axios.get(
        route("private-stream.dates", { streamerId: streamer.id })
      );
      
      // If we get here successfully, user has required subscription
      setShow(true);
    } catch (error) {
      // Handle subscription requirement error
      if (error.response && error.response.data) {
        const errorData = error.response.data;
        if (errorData.required_level && errorData.redirect_url) {
          toast.error(errorData.message);
          setTimeout(() => {
            window.location.href = errorData.redirect_url;
          }, 2000); // Redirect after 2 seconds
          return;
        }
      }
      // For other errors, still open the modal and let the form handle it
      setShow(true);
    }
  };

  const closeModal = () => {
    setShow(false);
    setActiveTab("new-request"); // Reset to new request tab when closing
  };

  // Format date and time for display with timezone conversion
  const formatDateTime = (date, time) => {
    if (!date || !time) return "N/A";
    
    try {
      // Create UTC datetime and convert to local timezone
      const utcDateTime = new Date(`${date}T${time}:00.000Z`);
      
      // Check if the date is valid
      if (isNaN(utcDateTime.getTime())) {
        throw new Error("Invalid date");
      }
      
      const localDate = utcDateTime.toLocaleDateString();
      const localTime = utcDateTime.toLocaleTimeString([], { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
      });
      return `${localDate} at ${localTime}`;
    } catch (error) {
      // Fallback to original format if conversion fails
      try {
        const formattedDate = new Date(date);
        if (isNaN(formattedDate.getTime())) {
          return "Invalid Date";
        }
        return `${formattedDate.toLocaleDateString()} at ${time}`;
      } catch (fallbackError) {
        return "Invalid Date";
      }
    }
  };

  // Get status badge color based on request status
  const getStatusBadgeClass = (status) => {
    switch (status) {
      case "pending":
        return "bg-yellow-500";
      case "accepted":
        return "bg-green-500";
      case "completed":
        return "bg-blue-500";
      case "rejected":
        return "bg-red-500";
      case "no_show":
        return "bg-gray-500";
      case "expired":
        return "bg-purple-500";
      default:
        return "bg-gray-500";
    }
  };

  // Count active requests (pending + accepted)
  const activeRequestsCount = userRequests.filter(
    (req) => req.status === "pending" || req.status === "accepted"
  ).length;

  // Render requests list tab
  const renderMyRequestsTab = () => {
    if (loading) {
      return (
        <div className="flex justify-center py-8">
          <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
        </div>
      );
    }

    return (
      <div className="h-[420px] overflow-y-auto pr-1 sm:h-[390px]">
        <h4 className="text-lg font-medium text-gray-primary mb-3">
          {__("Your Requests to")} {streamer.name}
        </h4>

        {userRequests.length > 0 ? (
          <div className="space-y-3">
            {userRequests.map((request) => (
              <div
                key={request.id}
                className="bg-gray-800 rounded-lg p-4 border border-gray-700"
              >
                <div className="flex justify-between items-start mb-2">
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-sm text-gray-400">
                        {__("Status:")}
                      </span>
                      <span
                        className={`px-2 py-1 rounded text-white text-xs ${getStatusBadgeClass(
                          request.status
                        )}`}
                      >
                        {request.status.toUpperCase()}
                      </span>
                    </div>
                    <p className="text-sm text-gray-300">
                      {formatDateTime(
                        request.requested_date,
                        request.requested_time
                      )}
                    </p>
                    <p className="text-sm text-gray-400">
                      {request.duration_minutes} {__("min")} • $
                      {request.streamer_fee} • {request.room_rental_tokens}{" "}
                      {__("tokens")}
                    </p>
                  </div>
                </div>

                {request.message && (
                  <div className="mt-2 p-2 bg-gray-700 rounded text-sm text-gray-300">
                    <span className="text-gray-400">{__("Message:")}</span>{" "}
                    {request.message}
                  </div>
                )}

                {(request.status === "pending" ||
                  request.status === "accepted") && (
                  <div className="mt-2 text-xs text-blue-400">
                    {request.status === "pending" &&
                      __("Waiting for streamer response...")}
                    {request.status === "accepted" &&
                      __("Stream confirmed! Check your notifications.")}
                  </div>
                )}
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-8">
            <p className="text-gray-400">
              {__("No requests sent to this streamer yet")}
            </p>
          </div>
        )}

        <div className="mt-4 pt-4 border-t border-gray-700">
          <PrimaryButton
            onClick={() => setActiveTab("new-request")}
            className="w-full justify-center"
          >
            {__("Request New Session")}
          </PrimaryButton>
        </div>
      </div>
    );
  };

  // Render the appropriate form based on the payment method and active tab
  const renderFormWithPaymentMethod = () => {
    if (activeTab === "my-requests") {
      return (
        <div className="p-4 w-full">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-xl font-semibold text-gray-primary">
              {__("Private Stream Requests")}
            </h3>
            <button
              onClick={() => setActiveTab("new-request")}
              className="text-blue-400 hover:text-blue-300 text-sm"
            >
              {__("← New Request")}
            </button>
          </div>
          {renderMyRequestsTab()}
        </div>
      );
    }

    // Render new request form
    if (selectedPaymentMethod === "stripe" && stripePromise) {
      return (
        <Elements stripe={stripePromise}>
          <PrivateChatForm
            streamer={streamer}
            onClose={closeModal}
            paymentMethod={selectedPaymentMethod}
            onRequestComplete={fetchUserRequests}
            userRequests={userRequests}
            onViewRequests={() => setActiveTab("my-requests")}
          />
        </Elements>
      );
    }

    return (
      <PrivateChatForm
        streamer={streamer}
        onClose={closeModal}
        paymentMethod={selectedPaymentMethod}
        onRequestComplete={fetchUserRequests}
        userRequests={userRequests}
        onViewRequests={() => setActiveTab("my-requests")}
      />
    );
  };

  return (
    <>
      <Modal show={show} onClose={closeModal} maxWidth="lg">
        <div className="w-[550px] max-w-full mx-auto">
          {renderFormWithPaymentMethod()}
        </div>
      </Modal>

      <div className="iq-button">
        <PrimaryButton
          onClick={openModal}
          className={`btn btn-sm d-flex align-items-center justify-content-center me-2 text-uppercase btn ${
            activeRequestsCount > 0 ? "btn-success" : "btn-primary"
          }`}
        >
          {activeRequestsCount > 0 ? (
            <span className="flex items-center">
              {__("Private")}
              <span className="ml-1 bg-white text-black rounded-full px-1 text-xs min-w-[16px] h-4 flex items-center justify-center">
                {activeRequestsCount}
              </span>
            </span>
          ) : (
            __("Private")
          )}
        </PrimaryButton>
      </div>
    </>
  );
}
