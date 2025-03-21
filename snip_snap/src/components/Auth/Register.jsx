/* eslint-disable no-unused-vars */
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import useForm from "../../hooks/useForm";
import axios from "axios";
import ENDPOINTS from "../../config/links";
import "../css/Auth.css";

const Register = () => {
  const navigate = useNavigate();
  const [apiError, setApiError] = useState("");

  const {
    values,
    errors,
    isSubmitting,
    handleChange,
    handleSubmit,
    setErrors,
  } = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });

  const submitForm = async (formValues) => {
    setApiError("");
    try {
      console.log("Sending registration data:", formValues);

      // Direct API call
      const response = await axios.post(ENDPOINTS.AUTH.REGISTER, formValues);
      console.log("Registration response:", response.data);

      // Check if the registration was successful
      if (response.data && response.data.user) {
        // Show success message or automatically log the user in
        // For now, we'll just navigate to login
        navigate("/login", {
          state: { message: "Registration successful! Please log in." },
        });
      } else {
        setApiError(
          "Registration successful but unexpected response. Please try logging in."
        );
      }
    } catch (err) {
      console.error("Registration error:", err.response?.data || err.message);

      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        setApiError(
          err.response?.data?.message ||
            "Registration failed. Please try again."
        );
      }
    }
  };

  return (
    <div className="register-container">
      <div className="auth-content-wrapper">
        <h2>Create Account</h2>

        {apiError && <div className="auth-alert error">{apiError}</div>}

        <form className="auth-form" onSubmit={handleSubmit(submitForm)}>
          <div className="auth-form-group">
            <label htmlFor="name">Name</label>
            <input
              type="text"
              id="name"
              name="name"
              value={values.name}
              onChange={handleChange}
              required
            />
            {errors.name && <span className="error">{errors.name}</span>}
          </div>

          <div className="auth-form-group">
            <label htmlFor="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              value={values.email}
              onChange={handleChange}
              required
            />
            {errors.email && <span className="error">{errors.email}</span>}
          </div>

          <div className="auth-form-group">
            <label htmlFor="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              value={values.password}
              onChange={handleChange}
              required
            />
            {errors.password && (
              <span className="error">{errors.password}</span>
            )}
          </div>

          <div className="auth-form-group">
            <label htmlFor="password_confirmation">Confirm Password</label>
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              value={values.password_confirmation}
              onChange={handleChange}
              required
            />
            {errors.password_confirmation && (
              <span className="error">{errors.password_confirmation}</span>
            )}
          </div>

          <button
            className="auth-form-button"
            type="submit"
            disabled={isSubmitting}
          >
            {isSubmitting ? "Creating Account..." : "Register"}
          </button>
        </form>

        <div className="auth-footer">
          Already have an account?{" "}
          <button onClick={() => navigate("/login")}>Login</button>
        </div>
      </div>
    </div>
  );
};

export default Register;
