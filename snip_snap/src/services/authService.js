/* eslint-disable no-unused-vars */
import axios from "axios";
import ENDPOINTS from "../config/links";

const authAxios = axios.create();

// Add token to requests
authAxios.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const register = async (userData) => {
  const response = await axios.post(ENDPOINTS.AUTH.REGISTER, userData);
  if (response.data.token) {
    localStorage.setItem("token", response.data.token);
  }
  return response.data.user;
};

export const login = async (credentials) => {
  const response = await axios.post(ENDPOINTS.AUTH.LOGIN, credentials);
  if (response.data.token) {
    localStorage.setItem("token", response.data.token);
  }
  return response.data.user;
};

export const logout = async () => {
  await authAxios.post(ENDPOINTS.AUTH.LOGOUT);
  localStorage.removeItem("token");
  return true;
};

export const getCurrentUser = async () => {
  try {
    const token = localStorage.getItem("token");
    if (!token) return null;

    const response = await authAxios.get(ENDPOINTS.AUTH.ME);
    return response.data;
  } catch (error) {
    localStorage.removeItem("token");
    return null;
  }
};
