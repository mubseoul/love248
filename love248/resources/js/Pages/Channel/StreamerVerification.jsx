import React, { useEffect } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, usePage, useForm } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import { HiIdentification } from "react-icons/hi";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";

export default function StreamerVerification() {
  const { auth } = usePage().props;

  const { data, setData, errors, processing, post, progress } = useForm({
    document: "",
  });

  useEffect(() => {
    console.log(errors);
  }, [errors]);

  const submit = (e) => {
    e.preventDefault();

    post(route("streamer.submitVerification"));
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Verify Identity To Start Streaming")} />

      <div className="ml-0">
        <div className="p-4 sm:p-8 bg-footer text-gray-primary shadow sm:rounded-lg mb-5">
          <div className="flex items-center">
            <div>
              <HiIdentification className="h-12 w-12 mr-2" />
            </div>
            <div>
              <h2 className="text-xl font-semibold text-gray-primary">
                {__("Verify Identity to Start Streaming")}
              </h2>
              <p className="dark:text-gray-primary text-sm">
                {__(
                  "In order to start streaming, you need to send your gov. issued ID/passport to verify the account name matches to the document."
                )}
              </p>
            </div>
          </div>

          <div className="mt-5">
            <form onSubmit={submit}>
              <InputLabel
                className={
                  "block font-medium text-sm text-gray-primary mb-3undefined form-label"
                }
                value={__("Document (PNG or JPG)")}
              />

              <input
                className="border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm undefined form-control"
                id="document"
                type="file"
                required
                accept="image/jpg,image/png"
                onChange={(e) => setData("document", e.target.files[0])}
              />

              <InputError className="mt-2" message={errors.document} />

              <PrimaryButton className="mt-5" processing={processing}>
                {__("Submit Request")}
              </PrimaryButton>
            </form>

            {progress && (
              <progress value={progress.percentage} max="100">
                {progress.percentage}%
              </progress>
            )}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
