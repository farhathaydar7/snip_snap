const API_BASE_URL =
  import.meta.env.VITE_API_URL || "http://52.47.95.15:8000/api/";

const ENDPOINTS = {
  AUTH: {
    REGISTER: `${API_BASE_URL}/auth/register`,
    LOGIN: `${API_BASE_URL}/auth/login`,
    LOGOUT: `${API_BASE_URL}/auth/logout`,
    REFRESH: `${API_BASE_URL}/auth/refresh`,
    ME: `${API_BASE_URL}/auth/me`,
  },
  SNIPPETS: {
    BASE: `${API_BASE_URL}/snippets`,
    FAVORITE: (id) => `${API_BASE_URL}/snippets/${id}/favorite`,
    CREATE_OR_UPDATE: (id = "") =>
      `${API_BASE_URL}/snippets/create-or-update/${id}`,
  },
  TAGS: {
    BASE: `${API_BASE_URL}/tags`,
  },
};

export default ENDPOINTS;
