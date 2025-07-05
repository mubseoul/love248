import __ from "@/Functions/Translate";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import TextInput from "@/Components/TextInput";
import PrimaryButton from "@/Components/PrimaryButton";
import { usePage, useForm, Head, Link } from "@inertiajs/inertia-react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import SecondaryButton from "@/Components/SecondaryButton";
import AccountNavi from "../Channel/Partials/AccountNavi";
import { useState, useEffect, useMemo } from "react";
import { FaGrinStars } from "react-icons/fa";

export default function EditStreamer({ streamerData, auth }) {
  const { currency_symbol } = usePage().props;
  const isStreamer = auth.user.is_streamer;

  const daysOfWeek = [
    { id: 0, name: "Sunday", short: "Sun" },
    { id: 1, name: "Monday", short: "Mon" },
    { id: 2, name: "Tuesday", short: "Tue" },
    { id: 3, name: "Wednesday", short: "Wed" },
    { id: 4, name: "Thursday", short: "Thu" },
    { id: 5, name: "Friday", short: "Fri" },
    { id: 6, name: "Saturday", short: "Sat" },
  ];

  // Convert days from string to array if needed
  const initialDays = useMemo(() => {
    if (Array.isArray(streamerData?.days_of_week)) {
      return streamerData.days_of_week.map((day) => Number(day));
    } else if (typeof streamerData?.days_of_week === "string") {
      return streamerData.days_of_week
        .split(",")
        .map((day) => Number(day.trim()))
        .filter((day) => !isNaN(day));
    } else {
      return [];
    }
  }, [streamerData]);



  const { data, setData, post, processing, errors, reset } = useForm({
    start_time: streamerData?.start_time || "",
    end_time: streamerData?.end_time || "",
    days_of_week: initialDays,
    streamering_id: streamerData?.id,
  });

  // If old format, convert streaming_time to start_time and populate a default end_time
  useEffect(() => {
    if (streamerData?.get_streamer_price?.streaming_time && !data.start_time) {
      setData("start_time", streamerData.get_streamer_price.streaming_time);

      // Calculate end time (1 hour after start time as default)
      const startTimeParts = streamerData.get_streamer_price.streaming_time.split(":");
      if (startTimeParts.length === 2) {
        let hours = parseInt(startTimeParts[0]);
        const minutes = startTimeParts[1];
        hours = (hours + 1) % 24;
        const endTime = `${hours.toString().padStart(2, "0")}:${minutes}`;
        setData("end_time", endTime);
      }
    }
  }, [streamerData]);

  // Ensure days_of_week is always an array of numbers for component rendering
  useEffect(() => {
    if (data.days_of_week) {
      const normalizedDays = Array.isArray(data.days_of_week)
        ? data.days_of_week.map((day) => Number(day))
        : typeof data.days_of_week === "string"
        ? data.days_of_week
            .split(",")
            .map((day) => Number(day.trim()))
            .filter((day) => !isNaN(day))
        : [];

      if (
        JSON.stringify(normalizedDays) !== JSON.stringify(data.days_of_week)
      ) {
        setData("days_of_week", normalizedDays);
      }
    }
  }, [streamerData]);

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

    post(route("streamer.availability.update"), {
      data: data,
    });
  };

  if (!isStreamer) {
    return (
      <AuthenticatedLayout auth={auth}>
        <Head title={__("Streaming Availability")} />
        <div className="lg:flex lg:space-x-10 w-full">
          <AccountNavi active={"tiers"} />
          <div className="p-4 sm:p-8 bg-footer w-full dark:bg-zinc-900 mb-5">
            <div className="bg-gray-800 p-4 rounded-md text-center">
              <p className="text-amber-400">
                {__(
                  "Only streamers can edit availability timings"
                )}
              </p>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    );
  }

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Edit Streaming Availability")} />

      <div className="lg:flex lg:space-x-10 w-full">
        <AccountNavi active={"tiers"} />
        <div className="p-4 sm:p-8 bg-footer w-full dark:bg-zinc-900 mb-5">
          <header>
            <h2 className="text-lg inline-flex items-center md:text-xl font-medium text-gray-primary">
              <FaGrinStars className="mr-2" />
              {__("Edit Streaming Availability")}
            </h2>

            <p className="mt-1 mb-2 text-sm text-gray-primary">
              {__(
                "Update your streaming availability times. Room rental fees are controlled by admin."
              )}
            </p>
            <div className="iq-button">
              <Link
                className="btn d-inline-flex align-items-center btn-sm text-capitalize"
                href={route("streamer.availability.index")}
              >
                {__("<< Back")}
              </Link>
            </div>
          </header>

          <hr className="my-5" />
          <form onSubmit={submit}>
            <input
              type="hidden"
              name="streamering_id"
              value={data.streamering_id}
              onChange={onHandleChange}
            />

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
                  {__("Update")}
                </PrimaryButton>
              </div>
            </div>
          </form>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
