import React, { useState, useEffect } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm, usePage } from "@inertiajs/inertia-react";
import axios from "axios";
import __ from "@/Functions/Translate";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import { RiDeleteBin5Line } from "react-icons/ri";
import { AiOutlineEdit } from "react-icons/ai";
import { Inertia } from "@inertiajs/inertia";
import AccountNavi from "../Channel/Partials/AccountNavi";
import { FaGrinStars, FaClock, FaCalendarAlt } from "react-icons/fa";

export default function addStreaming({ auth, streamerData }) {
  const { currency_symbol } = usePage().props;
  const isStreamer = auth.user.is_streamer;

  const [showAddModal, setShowAddModal] = useState(false);
  const [showDeleteConfirmation, setShowDeleteConfirmation] = useState(false);
  const [deleteId, setDeleteId] = useState(0);
  const [roomRentalFee, setRoomRentalFee] = useState(0);

  const { data, setData, post, processing, errors, reset } = useForm({
    start_time: "",
    end_time: "",
    days_of_week: [],
  });

  const daysOfWeek = [
    { id: 0, name: "Sunday", short: "Sun" },
    { id: 1, name: "Monday", short: "Mon" },
    { id: 2, name: "Tuesday", short: "Tue" },
    { id: 3, name: "Wednesday", short: "Wed" },
    { id: 4, name: "Thursday", short: "Thu" },
    { id: 5, name: "Friday", short: "Fri" },
    { id: 6, name: "Saturday", short: "Sat" },
  ];

  // Fetch admin room rental fee
  useEffect(() => {
    const fetchRoomRentalFee = async () => {
      try {
        const response = await axios.get('/private-room-settings');
        if (response.data.status) {
          setRoomRentalFee(response.data.tokens_per_minute);
        }
      } catch (error) {
        console.error('Error fetching room rental fee:', error);
      }
    };

    fetchRoomRentalFee();
  }, []);

  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const handleDaySelection = (dayId) => {
    const selectedDays = Array.isArray(data.days_of_week)
      ? [...data.days_of_week]
      : [];

    // Convert dayId to Number to ensure consistent comparison
    dayId = Number(dayId);

    // Use some() to check if the day is already selected
    const isDaySelected = selectedDays.some((id) => Number(id) === dayId);

    if (isDaySelected) {
      setData(
        "days_of_week",
        selectedDays.filter((id) => Number(id) !== dayId)
      );
    } else {
      setData("days_of_week", [...selectedDays, dayId]);
    }
  };

  const submit = (e) => {
    e.preventDefault();

    // Ensure days_of_week is included as an array
    if (
      !data.days_of_week ||
      !Array.isArray(data.days_of_week) ||
      data.days_of_week.length === 0
    ) {
      alert("Please select at least one day of the week.");
      return;
    }

    post(route("streamer.availability.store"), {
      onSuccess: () => {
        setShowAddModal(false), reset();
      },
    });
  };

  const confirmDelete = (e, id) => {
    e.preventDefault();
    setShowDeleteConfirmation(true);
    setDeleteId(id);
  };

  const deleteAvailability = () => {
    Inertia.visit(route("streamer.availability.destroy"), {
      method: "POST",
      data: { id: deleteId },
      preserveState: false,
    });
  };

  const formatTimeRange = (start, end) => {
    if (!start || !end) return "";
    return `${start} - ${end}`;
  };

  const formatDaysOfWeek = (daysArray) => {
    if (!daysArray || !Array.isArray(daysArray)) {
      // If it's a string, try to convert it to an array
      if (typeof daysArray === "string") {
        try {
          daysArray = daysArray.split(",").map((day) => parseInt(day.trim()));
        } catch (e) {
          console.error("Error parsing days_of_week string:", e);
          return "";
        }
      } else {
        return ""; // Return empty string if daysArray is undefined or not an array
      }
    }
    return daysArray
      .map((day) => daysOfWeek[day]?.short || "")
      .filter(Boolean)
      .join(", ");
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Streaming Availability")} />

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"tiers"} />

        <div className="p-4 flex-shrink sm:p-8 bg-footer mb-5">
          <header>
            <h2 className="text-lg inline-flex items-center md:text-xl font-medium text-gray-primary">
              <FaGrinStars className="mr-2" />
              {__("Streaming Availability")}
            </h2>

            <p className="mt-1 mb-2 text-sm text-gray-primary">
              {__(
                "Set your streaming availability times. Room rental fees are controlled by admin."
              )}
            </p>

            {isStreamer ? (
              <div className="iq-button">
                <PrimaryButton onClick={(e) => setShowAddModal(true)}>
                  {__("+ Add Availability")}
                </PrimaryButton>
              </div>
            ) : (
              <p className="text-amber-500 mt-2">
                {__("Only streamers can add availability timings")}
              </p>
            )}
          </header>

          <hr className="my-5" />

          <Modal show={showAddModal} onClose={(e) => setShowAddModal(false)}>
            <div className="p-5">
              <h3 className="text-xl mb-4 text-gray-primary">
                {__("Add Streaming Availability")}
              </h3>
              <form onSubmit={submit}>
                <div className="mb-4">
                  <InputLabel
                    className="text-gray-primary"
                    for="start_time"
                    value={__("Start Time")}
                  />

                  <input
                    type="time"
                    id="start_time"
                    name="start_time"
                    value={data.start_time}
                    onChange={onHandleChange}
                    required
                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:focus:border-primary-dark dark:focus:ring-primary-dark dark:focus:ring-opacity-50 form-control"
                  />
                  <InputError message={errors.start_time} className="mt-2" />
                </div>

                <div className="mb-4">
                  <InputLabel
                    className="text-gray-primary"
                    for="end_time"
                    value={__("End Time")}
                  />

                  <input
                    type="time"
                    id="end_time"
                    name="end_time"
                    value={data.end_time}
                    onChange={onHandleChange}
                    required
                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 dark:focus:border-primary-dark dark:focus:ring-primary-dark dark:focus:ring-opacity-50 form-control"
                  />
                  <InputError message={errors.end_time} className="mt-2" />
                </div>

                <div className="mb-4">
                  <InputLabel
                    className="text-gray-primary mb-2"
                    value={__("Available Days")}
                  />

                  <div className="flex flex-wrap gap-2">
                    {daysOfWeek.map((day) => {
                      // Convert day.id to Number for consistent comparison
                      const dayId = Number(day.id);
                      // Check if this day is selected
                      const isSelected =
                        Array.isArray(data.days_of_week) &&
                        data.days_of_week.some((id) => Number(id) === dayId);

                      return (
                        <button
                          key={day.id}
                          type="button"
                          onClick={() => handleDaySelection(day.id)}
                          className={`px-3 py-1 rounded-full text-sm ${
                            isSelected
                              ? "bg-primary text-white"
                              : "bg-gray-700 text-gray-300"
                          }`}
                        >
                          {day.short}
                        </button>
                      );
                    })}
                  </div>
                  <InputError message={errors.days_of_week} className="mt-2" />
                </div>


                <div className="flex justify-between items-center">
                  <div className="iq-button">
                    <PrimaryButton processing={processing}>
                      {__("Save")}
                    </PrimaryButton>
                  </div>

                  <div className="iq-button">
                    <a
                      className="cursor-pointer ml-2 text-sm btn btn-sm btn-primary d-inline-flex align-items-center"
                      onClick={(e) => setShowAddModal(false)}
                    >
                      {__("Cancel")}
                    </a>
                  </div>
                </div>
              </form>
            </div>
          </Modal>

          <Modal
            show={showDeleteConfirmation}
            onClose={(e) => setShowDeleteConfirmation(false)}
          >
            <div className="px-5 py-10 text-center">
              <h3 className="text-xl mb-3 text-gray-primary">
                {__("Are you sure you want to remove this availability slot?")}
              </h3>
              <div className="iq-button">
                <PrimaryButton onClick={(e) => deleteAvailability()}>
                  {__("Yes")}
                </PrimaryButton>
                <SecondaryButton
                  className="ml-3"
                  onClick={(e) => setShowDeleteConfirmation(false)}
                >
                  {__("No")}
                </SecondaryButton>
              </div>
            </div>
          </Modal>

          {!isStreamer && (
            <div className="bg-gray-800 p-4 rounded-md text-center">
              <p className="text-amber-400">
                {__(
                  "Only streamers can set availability times"
                )}
              </p>
            </div>
          )}

          <span className="text-gray-primary">
            {isStreamer &&
              !streamerData.length &&
              __("You haven't added any availability slots yet.")}
          </span>

          {isStreamer && streamerData.length > 0 && (
            <div className="relative overflow-x-auto mt-5">
              <table className="w-full text-sm text-left text-gray-primary border">
                <thead className="text-xs border-t border-b text-gray-primary uppercase bg-black">
                  <tr>
                    <th className="px-6 py-3">{__("Available Days")}</th>
                    <th className="px-6 py-3">{__("Time Slot")}</th>
                    <th className="px-6 py-3">{__("Room Rental Fee")}</th>
                    <th className="px-6 py-3">{__("Actions")}</th>
                  </tr>
                </thead>
                <tbody>
                  {streamerData.map((t) => (
                    <tr
                      key={t.id}
                      className="bg-black border-b dark:bg-zinc-900 dark:border-zinc-700"
                    >
                      <td className="px-6 py-4 font-medium border-r">
                        {t.days_of_week ? formatDaysOfWeek(t.days_of_week) : ""}
                      </td>
                      <td className="px-6 py-4 font-medium border-r">
                        {t.start_time && t.end_time
                          ? formatTimeRange(t.start_time, t.end_time)
                          : t.get_streamer_price?.streaming_time || ""}
                      </td>
                      <td className="px-6 py-4 font-medium border-r">
                        <span className="text-blue-400">
                          {roomRentalFee} {__("tokens/min")}
                        </span>
                        <br />
                        <small className="text-gray-500">
                          {__("(Admin controlled)")}
                        </small>
                      </td>
                      <td className="px-6 py-4">
                        <div className="flex items-center">
                          <Link
                            className="btn btn-sm p-1 bg-success me-1 text-dark"
                            href={route("streaming.edit", { id: t.id })}
                          >
                            <AiOutlineEdit className="w-5 h-5 text-black" />
                          </Link>
                          <button
                            className="btn btn-sm p-1 btn btn-primary"
                            onClick={(e) => confirmDelete(e, t.id)}
                          >
                            <RiDeleteBin5Line className="text-gray-primary w-5 h-5" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
