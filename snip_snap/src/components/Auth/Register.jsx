/* eslint-disable no-unused-vars */
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import useForm from "../../hooks/useForm";
import { useAuth } from "../../hooks/useAuth.jsx";
import axios from "axios";
import ENDPOINTS from "../../config/links";
// import "../../components/css/Auth.css";

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
    name: "", // Laravel expects this as 'name'
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

      if (response.data.token) {
        localStorage.setItem("token", response.data.token);
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
