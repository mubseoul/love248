import React, { useRef } from "react";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { useForm } from "@inertiajs/inertia-react";
import { Transition } from "@headlessui/react";
import __ from "@/Functions/Translate";

export default function UpdatePasswordForm({ className }) {
  const passwordInput = useRef();
  const currentPasswordInput = useRef();

  const { data, setData, errors, put, reset, processing, recentlySuccessful } =
    useForm({
      current_password: "",
      password: "",
      password_confirmation: "",
    });

  const updatePassword = (e) => {
    e.preventDefault();

    put(route("password.update"), {
      preserveScroll: true,
      onSuccess: () => reset(),
      onError: () => {
        if (errors.password) {
          reset("password", "password_confirmation");
          passwordInput.current.focus();
        }

        if (errors.current_password) {
          reset("current_password");
          currentPasswordInput.current.focus();
        }
      },
    });
  };

  return (
    <section className={className}>
      <header>
        <h2 className="text-lg font-medium text-gray-primary">
          {__("Update Password")}
        </h2>

        <p className="mt-1 text-sm text-gray-primary">
          {__(
            "Ensure your account is using a long, random password to stay secure."
          )}
        </p>
      </header>

      <form onSubmit={updatePassword} className="mt-6 space-y-6">
        <div>
          <InputLabel
            className="text-gray-primary"
            for="current_password"
            value="Current Password"
          />

          <TextInput
            id="current_password"
            ref={currentPasswordInput}
            value={data.current_password}
            handleChange={(e) => setData("current_password", e.target.value)}
            type="password"
            className="mt-1 block w-full form-control"
            autocomplete="current-password"
          />

          <InputError message={errors.current_password} className="mt-2" />
        </div>

        <div>
          <InputLabel
            className="text-gray-primary"
            for="password"
            value="New Password"
          />

          <TextInput
            id="password"
            ref={passwordInput}
            value={data.password}
            handleChange={(e) => setData("password", e.target.value)}
            type="password"
            className="mt-1 block w-full form-control"
            autocomplete="new-password"
          />

          <InputError message={errors.password} className="mt-2" />
        </div>

        <div>
          <InputLabel
            className="text-gray-primary"
            for="password_confirmation"
            value="Confirm Password"
          />

          <TextInput
            id="password_confirmation"
            value={data.password_confirmation}
            handleChange={(e) =>
              setData("password_confirmation", e.target.value)
            }
            type="password"
            className="mt-1 block w-full form-control"
            autocomplete="new-password"
          />

          <InputError message={errors.password_confirmation} className="mt-2" />
        </div>

        <div className="flex items-center gap-4">
          <div className="iq-button">
            <PrimaryButton processing={processing}>Save</PrimaryButton>
          </div>

          <Transition
            show={recentlySuccessful}
            enterFrom="opacity-0"
            leaveTo="opacity-0"
            className="transition ease-in-out"
          >
            <p className="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
          </Transition>
        </div>
      </form>
    </section>
  );
}
