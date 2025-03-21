/* eslint-disable no-unused-vars */
import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import useForm from "../../hooks/useForm";
import { useAuth } from "../../hooks/useAuth.jsx";
import axios from "axios";
import ENDPOINTS from "../../config/links";
import "../css/Auth.css";

const Login = () => {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [apiError, setApiError] = useState("");

  const {
    values,
    errors,
    isSubmitting,
    handleChange,
    handleSubmit,
    setErrors,
  } = useForm({
    email: "",
    password: "",
  });

  const submitForm = async (formValues) => {
    setApiError("");
    try {
      console.log("Sending login data:", formValues);

      // Direct API call to debug
      const response = await axios.post(ENDPOINTS.AUTH.LOGIN, formValues);
      console.log("Login response:", response.data);

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
        setApiError(
          "Login successful but no token received. Please try again."
        );
      }
    } catch (err) {
      console.error("Login error:", err.response?.data || err.message);

      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        setApiError(
          err.response?.data?.message ||
            "Login failed. Please check your credentials."
        );
      }
    }
  };

  return (
    <div className="login-container">
      <h2>Login</h2>

      {apiError && <div className="alert alert-danger">{apiError}</div>}

      <form onSubmit={handleSubmit(submitForm)}>
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
          />
          {errors.password && <span className="error">{errors.password}</span>}
        </div>

        <button type="submit" disabled={isSubmitting}>
          {isSubmitting ? "Logging in..." : "Login"}
        </button>
      </form>

      <p>
        Don't have an account?{" "}
        <button onClick={() => navigate("/register")}>Register</button>
      </p>
    </div>
  );
};

export default Login;
