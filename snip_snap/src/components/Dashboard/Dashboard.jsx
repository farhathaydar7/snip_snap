import React, { useState, useEffect, useCallback, useRef } from "react";
import { useNavigate } from "react-router-dom";
import SnippetList from "./SnippetList";
import FilterBar from "./FilterBar";
import { useAuth } from "../../hooks/useAuth.jsx";
import "../css/Dashboard.css";
import snippetService from "../../services/snippetService";

const Dashboard = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const isMounted = useRef(true);
  const initialFetchDone = useRef(false);

  const [snippets, setSnippets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [filters, setFilters] = useState({
    search: "",
    language: "",
    tag: "",
    favorites: false,
    page: 1,
  });
  const [pagination, setPagination] = useState({
    currentPage: 1,
    lastPage: 1,
    perPage: 10,
    total: 0,
  });

  // Define fetchSnippets with useCallback to avoid recreation on every render
  const fetchSnippets = useCallback(async () => {
    if (!isMounted.current) return;

    setLoading(true);
    try {
      console.log("Fetching snippets with filters:", JSON.stringify(filters));
      const response = await snippetService.getAllSnippets(filters);

      if (!isMounted.current) return;

      console.log("API Response:", response); // For debugging

      // Check if the response has the expected structure
      if (response && Array.isArray(response.data)) {
        // Standard Laravel pagination response
        setSnippets(response.data);
        setPagination({
          currentPage: response.current_page || 1,
          lastPage: response.last_page || 1,
          perPage: response.per_page || 10,
          total: response.total || 0,
        });
      } else if (response && Array.isArray(response)) {
        // In case the API returns an array directly
        setSnippets(response);
        setPagination({
          currentPage: 1,
          lastPage: 1,
          perPage: response.length,
          total: response.length,
        });
      } else {
        // Fallback for unexpected response format
        console.error("Unexpected API response format:", response);
        setSnippets([]);
        setError("Received unexpected data format from the server");
      }

      setError(null);
    } catch (err) {
      if (!isMounted.current) return;

      console.error("Error fetching snippets:", err);
      setError("Failed to load snippets. Please try again later.");
      setSnippets([]);

      // If unauthorized, redirect to login
      if (err.response && err.response.status === 401) {
        logout();
        navigate("/login");
      }
    } finally {
      if (isMounted.current) {
        setLoading(false);
      }
    }
  }, [filters, logout, navigate]);

  // Only fetch on mount and when filters change
  useEffect(() => {
    // Always fetch when fetchSnippets changes (which happens when filters change)
    fetchSnippets();
    initialFetchDone.current = true;
  }, [fetchSnippets]);

  // Cleanup on unmount
  useEffect(() => {
    return () => {
      isMounted.current = false;
    };
  }, []);

  const handleFilterChange = useCallback((newFilters) => {
    setFilters((prevFilters) => ({
      ...newFilters,
      page: 1,
    }));
  }, []);

  const handlePageChange = useCallback((newPage) => {
    setFilters((prevFilters) => ({
      ...prevFilters,
      page: newPage,
    }));
  }, []);

  const handleToggleFavorite = async (snippetId, isFavorite) => {
    try {
      await snippetService.toggleFavorite(snippetId);

      // Update the snippet in the local state
      setSnippets((prevSnippets) =>
        prevSnippets.map((snippet) =>
          snippet.id === snippetId
            ? { ...snippet, is_favorite: !isFavorite }
            : snippet
        )
      );
    } catch (error) {
      console.error("Error toggling favorite status:", error);
      setError("Failed to update favorite status. Please try again.");
    }
  };

  const handleLogout = async () => {
    try {
      await logout();
      navigate("/");
    } catch (error) {
      console.error("Error logging out:", error);
    }
  };

  const handleCreateSnippet = () => {
    navigate("/snippet/new");
  };

  const handleEditSnippet = (snippetId) => {
    navigate(`/snippet/${snippetId}`);
  };

  return (
    <div className="dashboard">
      <div className="dashboard-header">
        <h1 className="dashboard-title">SnipSnap</h1>
        <div className="dashboard-actions">
          <button className="primary-button" onClick={handleCreateSnippet}>
            Add Snippet
          </button>
          <button className="secondary-button" onClick={handleLogout}>
            Logout
          </button>
        </div>
      </div>

      <FilterBar onFilterChange={handleFilterChange} />

      <div className="dashboard-content">
        {loading ? (
          <div className="loading-state">
            <p>Loading snippets...</p>
          </div>
        ) : error ? (
          <div className="error-state">
            <p>{error}</p>
            <button onClick={fetchSnippets}>Try Again</button>
          </div>
        ) : snippets.length === 0 ? (
          <div className="empty-state">
            <p>
              No snippets found. Try adjusting your filters or create a new
              snippet.
            </p>
            <button className="primary-button" onClick={handleCreateSnippet}>
              Create Your First Snippet
            </button>
          </div>
        ) : (
          <SnippetList
            snippets={snippets}
            onToggleFavorite={handleToggleFavorite}
            onEditSnippet={handleEditSnippet}
          />
        )}
      </div>

      {/* Pagination */}
      {!loading && !error && snippets.length > 0 && (
        <div className="pagination">
          <button
            disabled={pagination.currentPage === 1}
            onClick={() => handlePageChange(pagination.currentPage - 1)}
          >
            Previous
          </button>

          {[...Array(pagination.lastPage).keys()].map((page) => (
            <button
              key={page + 1}
              className={pagination.currentPage === page + 1 ? "active" : ""}
              onClick={() => handlePageChange(page + 1)}
            >
              {page + 1}
            </button>
          ))}

          <button
            disabled={pagination.currentPage === pagination.lastPage}
            onClick={() => handlePageChange(pagination.currentPage + 1)}
          >
            Next
          </button>
        </div>
      )}

      {/* Floating Action Button for creating a new snippet */}
      <button
        className="floating-action-button"
        onClick={handleCreateSnippet}
        aria-label="Create new snippet"
      >
        +
      </button>
    </div>
  );
};

export default Dashboard;
