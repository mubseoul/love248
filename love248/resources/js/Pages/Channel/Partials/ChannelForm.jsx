import React, { useState } from "react";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Link, useForm, usePage } from "@inertiajs/inertia-react";
import { Transition } from "@headlessui/react";
import __ from "@/Functions/Translate";
import Textarea from "@/Components/Textarea";
import { FaCog } from "react-icons/fa";

export default function ChannelForm({ mustVerifyEmail, status, className }) {
  const user = usePage().props.auth.user;
  const { categories } = usePage().props;

  const [previewProfile, setPreviewProfile] = useState(user.profile_picture);
  const [previewCover, setPreviewCover] = useState(user.cover_picture);

  const {
    data,
    setData,
    errors,
    processing,
    recentlySuccessful,
    post,
    progress,
  } = useForm({
    username: user.username,
    about: user.about,
    category: user.firstCategory.id,
    headline: user.headline,
  });

  const submit = (e) => {
    e.preventDefault();

    post(route("channel.update-settings"), {
      preserveState: false,
    });
  };

  const changeCover = (file) => {
    setData("coverPicture", file);
    setPreviewCover((window.URL ? URL : webkitURL).createObjectURL(file));
  };

  const changeProfilePicture = (file) => {
    setData("profilePicture", file);
    setPreviewProfile((window.URL ? URL : webkitURL).createObjectURL(file));
  };

  return (
    <section className={className}>
      <header>
        <div className="flex items-center">
          <FaCog className="text-gray-primary mr-2" />
          <h2 className="text-xl font-medium text-gray-primary">
            {__("Channel Settings")}
          </h2>
        </div>

        <p className="mt-1 text-sm text-gray-primary">
          {__("Update your channel infos")}
        </p>
      </header>

      <form onSubmit={submit} className="mt-6 space-y-6">
        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="username"
            value={__("Username")}
          />

          <TextInput
            id="username"
            className="form-control"
            value={data.username}
            handleChange={(e) => setData("username", e.target.value)}
            required
            autofocus
          />

          <InputError className="mt-2" message={errors.username} />
        </div>

        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="category"
            value={__("Category")}
          />

          <select
            name="category"
            onChange={(e) => setData("category", e.target.value)}
            className={`form-select form-select`}
            defaultValue={data.category}
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

          <InputError className="mt-2" message={errors.category} />
        </div>

        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="profilePicture"
            value={__("Profile Picture - 80x80 recommended")}
          />

          <TextInput
            className="text-white form-control"
            id="profilePicture"
            type="file"
            handleChange={(e) => changeProfilePicture(e.target.files[0])}
          />

          <InputError className="mt-2" message={errors.profilePicture} />
          <img
            src={previewProfile}
            alt="profile picture"
            className="h-20 rounded-full mt-2 border-white border-2 dark:border-indigo-200"
          />
        </div>

        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="coverPicture"
            value={__("Cover Picture - 960x280 recommended")}
          />

          <TextInput
            className="text-white form-control"
            id="coverPicture"
            type="file"
            handleChange={(e) => changeCover(e.target.files[0])}
          />

          <InputError className="mt-2" message={errors.coverPicture} />

          <div className="mt-3">
            <img
              src={previewCover}
              alt="cover picture"
              className="rounded-md border-2 border-white dark:border-indigo-200 h-40"
            />
          </div>
        </div>

        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="headline"
            value={__("Profile Headline")}
          />

          <TextInput
            id="headline"
            className="form-control"
            value={data.headline}
            handleChange={(e) => setData("headline", e.target.value)}
            required
            autofocus
          />

          <InputError className="mt-2" message={errors.headline} />
        </div>

        <div>
          <InputLabel
            className="block font-medium text-sm text-gray-primary fw-500 mb-2 form-label"
            for="about"
            value={__("Channel About - html <img /> tag allowed")}
          />
          <Textarea
            id="about"
            className="form-control"
            value={data.about ? data.about : ""}
            handleChange={(e) => setData("about", e.target.value)}
          />
          <div role="alert" className="fade mt-3 alert alert-info show">
            <strong className="font-bold">Allowed HTML Tags: </strong>
            img, h3, h4, h5, h6, blockquote, p, a, ul,ol,nl,li,b,i,strong,em,
            strike,code,hr,br,div,table,thead, caption,tbody,tr,th,td,pre'
          </div>

          <InputError className="mt-2" message={errors.about} />
        </div>

        <div className="flex items-center gap-4">
          <PrimaryButton processing={processing}>{__("Save")}</PrimaryButton>

          <Transition
            show={recentlySuccessful}
            enterFrom="opacity-0"
            leaveTo="opacity-0"
            className="transition ease-in-out"
          >
            <p className="inline-flex text-uppercase items-center btn btn-primary transition ease-in-out duration-150 btn-sm false ">
              {__("Saved")}.
            </p>
          </Transition>
        </div>

        {progress && (
          <progress value={progress.percentage} max="100">
            {progress.percentage}%
          </progress>
        )}
      </form>
    </section>
  );
}
