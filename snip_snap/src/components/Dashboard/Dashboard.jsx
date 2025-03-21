import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import SnippetList from "./SnippetList";
import FilterBar from "./FilterBar";
import { useAuth } from "../../hooks/useAuth.jsx";
import "../css/Dashboard.css";
import snippetService from "../../services/snippetService";

const Dashboard = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

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

  // Fetch snippets when filters change
  useEffect(() => {
    fetchSnippets();
  }, [filters]);

  const fetchSnippets = async () => {
    setLoading(true);
    try {
      const response = await snippetService.getAllSnippets(filters);
      setSnippets(response.data);
      setPagination({
        currentPage: response.current_page,
        lastPage: response.last_page,
        perPage: response.per_page,
        total: response.total,
      });
      setError(null);
    } catch (err) {
      console.error("Error fetching snippets:", err);
      setError("Failed to load snippets. Please try again later.");
      // If unauthorized, redirect to login
      if (err.response && err.response.status === 401) {
        logout();
        navigate("/login");
      }
    } finally {
      setLoading(false);
    }
  };

  const handleFilterChange = (newFilters) => {
    setFilters({ ...newFilters, page: 1 });
  };

  const handlePageChange = (newPage) => {
    setFilters({ ...filters, page: newPage });
  };

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
      navigate("/login");
    } catch (error) {
      console.error("Error logging out:", error);
    }
  };

  const handleCreateSnippet = () => {
    // Navigate to the create snippet page (to be implemented)
    console.log("Create new snippet");
  };

  const handleEditSnippet = (snippetId) => {
    // Navigate to the edit snippet page (to be implemented)
    console.log("Edit snippet with ID:", snippetId);
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
    </div>
  );
};

export default Dashboard;
