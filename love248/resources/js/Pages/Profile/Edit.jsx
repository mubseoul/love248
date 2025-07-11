import React from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import DeleteUserForm from "./Partials/DeleteUserForm";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm";
import ConnectMercadoAccountForm from "./Partials/ConnectMercadoAccountForm";
import { Head } from "@inertiajs/inertia-react";
import __ from "@/Functions/Translate";
import AccountNavi from "../Channel/Partials/AccountNavi";

export default function Edit({ auth, mustVerifyEmail, status }) {
  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={__("Profile")} />

      <div className="lg:flex lg:space-x-10">
        <AccountNavi active={"account"} />
        <div className="ml-0 w-full">
          <div className="p-4 sm:p-8 bg-footer shadow">
            <UpdateProfileInformationForm
              mustVerifyEmail={mustVerifyEmail}
              status={status}
              className="max-w-xl"
            />
          </div>

          <div className="mt-10 p-4 sm:p-8 bg-footer shadow">
            <UpdatePasswordForm className="max-w-xl" />
          </div>

          <div className="mt-10 p-4 sm:p-8 bg-footer shadow mb-5">
            <DeleteUserForm className="max-w-xl" />
          </div>
          {auth.user.is_streamer === "yes" && (
            <div className="mt-10 p-4 sm:p-8 bg-footer shadow mb-5">
              <ConnectMercadoAccountForm className="max-w-xl" />
            </div>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
