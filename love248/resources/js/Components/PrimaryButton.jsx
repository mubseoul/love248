import React from "react";

export default function PrimaryButton({
  type = "submit",
  className = "",
  processing,
  children,
  onClick,
}) {
  return (
    <button
      type={type}
      onClick={onClick}
      className={
        `inline-flex text-uppercase items-center btn btn-primary transition ease-in-out duration-150 btn-sm false ${
          processing && "opacity-25"
        } ` + className
      }
      disabled={processing}
    >
      {children}
    </button>
  );
}
