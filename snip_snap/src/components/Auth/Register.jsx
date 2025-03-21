/* eslint-disable no-unused-vars */
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import useForm from "../../hooks/useForm";
import { useAuth } from "../../hooks/useAuth.jsx";
import axios from "axios";
import ENDPOINTS from "../../config/links";
import "../css/Auth.css";

const Register = () => {
  const navigate = useNavigate();
  const { register } = useAuth();
  const [apiError, setApiError] = useState("");

  const {
    values,
    errors,
    isSubmitting,
    handleChange,
    handleSubmit,
    setErrors,
  } = useForm({
    username: "", // Laravel expects username, not name
    email: "",
    password: "",
    password_confirmation: "",
  });

  const submitForm = async (formValues) => {
    setApiError("");
    try {
      console.log("Sending registration data:", formValues);

      // Direct API call to debug
      const response = await axios.post(ENDPOINTS.AUTH.REGISTER, formValues);
      console.log("Registration response:", response.data);

      // The API returns an access_token property in the response
      if (response.data && response.data.access_token) {
        // Store the token from the correct property
        localStorage.setItem("token", response.data.access_token);

        // Store user info if needed
        if (response.data.user) {
          localStorage.setItem("user", JSON.stringify(response.data.user));
        }

        // Navigate to dashboard on success
        navigate("/dashboard");
      } else {
        // The request was successful but didn't return a token
        setApiError(
          "Registration successful but no token received. Please try logging in."
        );
      }
    } catch (err) {
      console.error("Registration error:", err.response?.data || err.message);

      // Set validation errors from the backend
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        // Set a general error message
        setApiError(
          err.response?.data?.message ||
            "Registration failed. Please try again."
        );
      }
    }
  };

  return (
    <div className="register-container">
      <h2>Register</h2>

      {apiError && <div className="alert alert-danger">{apiError}</div>}

      <form onSubmit={handleSubmit(submitForm)}>
        <div className="form-group">
          <label htmlFor="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            value={values.username}
            onChange={handleChange}
            required
          />
          {errors.username && <span className="error">{errors.username}</span>}
        </div>

        <div className="form-group">
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

        <div className="form-group">
          <label htmlFor="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            value={values.password}
            onChange={handleChange}
            required
            minLength="8"
          />
          {errors.password && <span className="error">{errors.password}</span>}
        </div>

        <div className="form-group">
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

        <button type="submit" disabled={isSubmitting}>
          {isSubmitting ? "Registering..." : "Register"}
        </button>
      </form>

      <p>
        Already have an account?{" "}
        <button onClick={() => navigate("/login")}>Login</button>
      </p>
    </div>
  );
};

export default Register;
