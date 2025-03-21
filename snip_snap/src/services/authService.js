/* eslint-disable no-unused-vars */
import axios from "axios";
import ENDPOINTS from "../config/links";

export const authAxios = axios.create();

// Add token to requests
authAxios.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle unauthorized responses (401)
authAxios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      console.log("Unauthorized access detected, redirecting to login");
      // Clear auth data
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      // Redirect to login page
      if (window.location.pathname !== "/") {
        window.location.href = "/";
      }
    }
    return Promise.reject(error);
  }
);

export const register = async (userData) => {
  const response = await axios.post(ENDPOINTS.AUTH.REGISTER, userData);
  if (response.data.access_token) {
    localStorage.setItem("token", response.data.access_token);
    if (response.data.user) {
      localStorage.setItem("user", JSON.stringify(response.data.user));
    }
  }
  return response.data.user;
};

export const login = async (credentials) => {
  const response = await axios.post(ENDPOINTS.AUTH.LOGIN, credentials);
  if (response.data.access_token) {
    localStorage.setItem("token", response.data.access_token);
    if (response.data.user) {
      localStorage.setItem("user", JSON.stringify(response.data.user));
    }
  }
  return response.data.user;
};

export const logout = async () => {
  await authAxios.post(ENDPOINTS.AUTH.LOGOUT);
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  return true;
};

export const getCurrentUser = async () => {
  try {
    const token = localStorage.getItem("token");
    if (!token) return null;

    const storedUser = localStorage.getItem("user");
    if (storedUser) {
      return JSON.parse(storedUser);
    }

    const response = await authAxios.get(ENDPOINTS.AUTH.ME);
    return response.data;
  } catch (error) {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    return null;
  }
};

// Add a function to check if the token is still valid
export const checkTokenValidity = async () => {
  try {
    // Try to fetch user data with the current token
    const response = await authAxios.get("/auth/me");
    return response.status === 200;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      console.log("Token validation failed, 401 Unauthorized");
      return false;
    }
    // For any other error, we'll assume token is invalid
    console.error("Error validating token:", error);
    return false;
  }
};
