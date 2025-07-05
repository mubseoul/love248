import React, { useEffect } from "react";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, useForm, usePage } from "@inertiajs/inertia-react";
import Front from "@/Layouts/Front";
import __ from "@/Functions/Translate";
import { toast } from "react-toastify";

export default function Register() {
  const routeName = route().current();

  const { data, setData, post, processing, errors, reset } = useForm({
    username: "",
    category: "",
    is_streamer: routeName == "streamer.signup" ? "yes" : "no",
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    dob: "",
    skin_tone: "",
  });

  useEffect(() => {
    return () => {
      reset("password", "password_confirmation");
    };
  }, []);

  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const submit = (e) => {
    e.preventDefault();

    post(route("register"));
  };

  const influencerIcon = "/images/streamer-icon.png";
  const userIcon = "/images/user-signup-icon.png";

  const { categories, flash } = usePage().props;

  useEffect(() => {
    // Flash messages now handled globally in app.jsx
    
    if (Object.keys(errors).length !== 0) {
      Object.keys(errors).map((key, index) => {
        toast.error(errors[key]);
      });
    }
  }, [errors]);

  return (
    <Front>
      <Head title={__("Register")} />

      <div className="user-login-card bg-dark my-5">
        <div className="w-full">
          <h2 className="mb-5 text-3xl text-white dark:text-zinc-200 font-semibold text-center">
            {routeName === "streamer.signup"
              ? __("Join as a Streamer")
              : __("Join as an User")}
          </h2>
        </div>

        <div className="flex-grow pt-10 w-full">
          <form onSubmit={submit}>
            <input type="hidden" name="is_streamer" value={data.is_streamer} />

            <div className="mb-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="username"
                value={__("Username")}
              />

              <TextInput
                name="username"
                value={data?.username}
                className="rounded-0 form-control"
                autoComplete="username"
                handleChange={onHandleChange}
                isFocused={true}
                required
              />

              <InputError message={errors?.username} className="mt-2" />
            </div>

            <div>
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="name"
                value={__("Name")}
              />

              <TextInput
                name="name"
                value={data.name}
                className="rounded-0 form-control"
                autoComplete="name"
                handleChange={onHandleChange}
                required
              />

              <InputError message={errors.name} className="mt-2" />
            </div>

            <div className="mt-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="email"
                value={__("Email")}
              />

              <TextInput
                type="email"
                name="email"
                value={data.email}
                className="rounded-0 form-control"
                autoComplete="username"
                handleChange={onHandleChange}
                required
              />

              <InputError message={errors.email} className="mt-2" />
            </div>

            <div className="mt-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="password"
                value={__("Password")}
              />

              <TextInput
                type="password"
                name="password"
                value={data.password}
                className="rounded-0 form-control"
                autoComplete="new-password"
                handleChange={onHandleChange}
                required
              />

              <InputError message={errors.password} className="mt-2" />
            </div>

            <div className="mt-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="password_confirmation"
                value={__("Confirm Password")}
              />

              <TextInput
                type="password"
                name="password_confirmation"
                value={data.password_confirmation}
                className="rounded-0 form-control"
                handleChange={onHandleChange}
                required
              />

              <InputError
                message={errors.password_confirmation}
                className="mt-2"
              />
            </div>

            <div className="mt-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="dob"
                value={__("D.O.B")}
              />

              <TextInput
                type="date"
                name="dob"
                value={data.dob}
                className="rounded-0 form-control"
                handleChange={onHandleChange}
                required
              />

              <InputError message={errors.dob} className="mt-2" />
            </div>

            {routeName === "streamer.signup" && (
              <div className="mt-4">
                <InputLabel
                  className={"text-white fw-500 mb-2 form-label"}
                  forInput="category"
                  value={__("Category")}
                />

                <select
                  name="category"
                  onChange={(e) => onHandleChange(e)}
                  required
                  className={`rounded-0 form-control border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm `}
                >
                  <option value={""}>{__("- Select -")}</option>
                  {categories.map((c, cIndex) => {
                    return (
                      <option value={c.id} key={c.id}>
                        {c.category}
                      </option>
                    );
                  })}
                </select>

                <InputError message={errors.category} className="mt-2" />
              </div>
            )}

            <div className="mt-4">
              <InputLabel
                className={"text-white fw-500 mb-2 form-label"}
                forInput="skin_tone"
                value={__("Skin Tone")}
              />

              <select
                name="skin_tone"
                onChange={(e) => onHandleChange(e)}
                required
                className={`rounded-0 form-control border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm `}
              >
                <option value="">{__("- Select -")}</option>
                <option value="white skin">{__("White Skin")}</option>
                <option value="medium skin">{__("Medium Skin")}</option>
                <option value="black skin">{__("Black Skin")}</option>
                <option value="red skin">{__("Red Skin")}</option>
                <option value="blonde skin">{__("Blonde Skin")}</option>
              </select>

              <InputError message={errors.skin_tone} className="mt-2" />
            </div>

            <div className="flex items-center justify-end mt-4 mb-4 iq-button">
              <PrimaryButton
                className="btn py-3 text-uppercase position-relative d-flex w-100 mt-2 btn btn-primary btn-sm justify-content-center align-items-center btn btn-primary"
                processing={processing}
              >
                {__("Register")}
                <i class="fa-solid fa-play"></i>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </Front>
  );
}
