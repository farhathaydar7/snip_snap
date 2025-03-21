import { authAxios } from "./authService";

const API_URL = "http://localhost:8000/api";

const snippetService = {
  getAllSnippets: async (filters = {}) => {
    // Build the query string from filters
    let queryParams = new URLSearchParams();

    if (filters.search) queryParams.append("search", filters.search.trim());
    if (filters.language) queryParams.append("language", filters.language);
    if (filters.tag) queryParams.append("tag", filters.tag.trim());
    if (filters.favorites) queryParams.append("is_favorite", "1");
    if (filters.page) queryParams.append("page", filters.page);

    const queryString = queryParams.toString();
    const url = `${API_URL}/snippets${queryString ? `?${queryString}` : ""}`;

    console.log("Sending search request:", url, filters); // Debug log

    try {
      const response = await authAxios.get(url);
      console.log("Search response:", response.data); // Debug log
      return response.data; // The Laravel API returns data within response.data
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  },

  getSnippet: async (id) => {
    const response = await authAxios.get(`${API_URL}/snippets/${id}`);
    return response.data;
  },

  createOrUpdateSnippet: async (snippetData, id = null) => {
    const url = id
      ? `${API_URL}/snippets/create-or-update/${id}`
      : `${API_URL}/snippets/create-or-update`;

    const response = await authAxios.post(url, snippetData);
    return response.data;
  },

  toggleFavorite: async (id) => {
    const response = await authAxios.post(`${API_URL}/snippets/${id}/favorite`);
    return response.data;
  },

  deleteSnippet: async (id) => {
    const response = await authAxios.delete(`${API_URL}/snippets/${id}`);
    return response.data;
  },
};

export default snippetService;
