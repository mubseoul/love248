import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import TextInput from "@/Components/TextInput";
import Textarea from "@/Components/Textarea";
import PrimaryButton from "@/Components/PrimaryButton";
import __ from "@/Functions/Translate";
import { toast } from "react-toastify";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { usePage, useForm, Head, Link } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";
import { useEffect, useState } from "react";
import axios from "axios";
import Spinner from "@/Components/Spinner";
import { MdVideoLibrary } from "react-icons/md";
import AccountNavi from "@/Pages/Channel/Partials/AccountNavi";

export default function UploadGallery({ gallery, categories }) {
  console.log("gallery", gallery);
  const { data, setData, post, processing, errors, progress } = useForm({
    title: gallery?.title,
    category_id: gallery?.category_id,
    price: gallery?.price,
    free_for_subs: gallery?.free_for_subs,
    thumbnail: "",
  });

  const [chunks, setChunks] = useState([]);
  const [spinner, setSpinner] = useState(false);
  const [uploaded, setUploaded] = useState(0);

  useEffect(() => {
    if (Object.keys(errors).length) {
      Object.keys(errors).map((key) => {
        toast.error(errors.key);
      });
    }
  }, [errors]);

  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const uploadGalleryHandel = (e) => {
    // e.preventDefault();
    setSpinner(true);

    const formData = new FormData();
    formData.append("title", data.title);
    formData.append("category_id", data.category_id);
    formData.append("price", data.price);
    formData.append("free_for_subs", data.free_for_subs);
    formData.append("thumbnail", data.thumbnail);

    axios
      .post(route("gallery.save"), formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (event) => {
          setUploaded(uploaded + e.loaded);
        },
      })
      .then(function (response) {
        setUploaded(0);
        setSpinner(false);

        Inertia.visit(route("gallery.list"));
        updateGallery();
      })
      .catch(function (error) {
        setUploaded(0);
        setSpinner(false);
        toast.error(error.response?.data?.message);
      })
      .then(function () {
        setSpinner(false);
      });
  };
  const updateGallery = () => {
    post(route("gallery.update", { gallery: gallery.id }));
  };

  const submit = (e) => {
    e.preventDefault();

    if (gallery?.id !== null) {
      updateGallery();
    } else {
      uploadGalleryHandel();
    }
  };

  return (
    <AuthenticatedLayout>
      <Head title={__("Upload Gallery")} />
      <div className="lg:flex lg:space-x-10">
        <AccountNavi active="upload-gallery" />
        <div className="p-4 sm:p-8 bg-footer w-full dark:bg-zinc-900 mb-5">
          <header className="mb-5">
            <h2 className="text-lg inline-flex items-center md:text-xl font-medium text-gray-primary">
              <MdVideoLibrary className="mr-2" />
              {gallery.id === null ? __("Upload Image") : __("Edit Image")}
            </h2>

            <p className="mt-1 mb-2 text-sm text-gray-primary">
              {__("Upload a new Images")}
            </p>

            <div className="iq-button">
              <PrimaryButton
                className="flex items-center"
                onClick={(e) => Inertia.visit(route("gallery.list"))}
              >
                {__("Back to Gallery")}
              </PrimaryButton>
            </div>
          </header>

          <hr className="my-5" />
          <form onSubmit={submit} encType="multipart/form-data">
            <div className="mb-5">
              <InputLabel
                className="text-gray-primary"
                for="title"
                value={__("Title")}
              />
              <TextInput
                name="title"
                value={data.title}
                handleChange={onHandleChange}
                required
                className="mt-1 block w-full md:w-1/2 form-control"
              />
              <InputError message={errors.title} className="mt-2" />
            </div>

            <div className="mb-5">
              <InputLabel
                className="text-gray-primary"
                for="category"
                value={__("Category")}
              />
              <select
                name="category_id"
                value={data.category_id}
                onChange={onHandleChange}
                required
                className={`mt-1 form-control`}
              >
                <option value="">{__("--Select--")}</option>
                {categories.map((c) => (
                  <option key={`category-${c.id}`} value={c.id}>
                    {c.category}
                  </option>
                ))}
              </select>
              <InputError message={errors.category_id} className="mt-2" />
            </div>

            <div className="flex w-full md:w-8/12 flex-col md:flex-row md:items-center md:space-x-10 md:justify-between">
              <div className="mb-5">
                <InputLabel
                  className="text-gray-primary"
                  for="price"
                  value={__("Price")}
                />
                <div className="flex items-center">
                  <TextInput
                    type="number"
                    name="price"
                    value={data.price}
                    handleChange={onHandleChange}
                    required
                    className="mt-1 form-control w-32"
                  />
                  <div className="ml-1 text-gray-primary">BRL</div>
                </div>
                <InputError message={errors.price} className="mt-2" />
              </div>
              <div className="mb-5">
                <InputLabel
                  className="text-gray-primary"
                  for="free_for_subs"
                  value={__("Free for subscribers?")}
                />
                <select
                  name="free_for_subs"
                  value={data.free_for_subs}
                  onChange={onHandleChange}
                  required
                  className={`mt-1 form-control w-32`}
                >
                  <option value="yes">{__("Yes")}</option>
                  <option value="no">{__("No")}</option>
                </select>
                <InputError message={errors.free_for_subs} className="mt-2" />
              </div>
            </div>

            <div className="mb-5">
              <InputLabel
                className="text-gray-primary"
                for="thumbnail"
                value={__(
                  "Images - helps to attract sales (will be resized to 640x320px)"
                )}
              />
              <TextInput
                className="mt-1 border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm undefined form-control"
                type="file"
                name="thumbnail"
                handleChange={(e) => setData("thumbnail", e.target.files[0])}
                required={gallery?.id === null}
              />
              <InputError message={errors.thumbnail} className="mt-2" />
            </div>

            <div className="flex justify-between items-center">
              <div className="iq-button">
                <PrimaryButton processing={processing || spinner}>
                  {gallery.id === null
                    ? __("Save Gallery")
                    : __("Update Gallery")}
                </PrimaryButton>
              </div>
            </div>

            {spinner && (
              <div className="my-3">
                <Spinner />
              </div>
            )}

            {progress && (
              <progress className="mt-5" value={progress.percentage} max="100">
                {progress.percentage}%
              </progress>
            )}
          </form>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
