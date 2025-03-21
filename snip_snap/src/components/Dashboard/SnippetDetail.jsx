import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import snippetService from "../../services/snippetService";

const SnippetDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const isNewSnippet = id === "new";

  const [snippet, setSnippet] = useState({
    title: "",
    description: "",
    code: "",
    language: "",
    is_favorite: false,
    tags: [],
  });

  const [isEditing, setIsEditing] = useState(isNewSnippet);
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState("");
  const [successMessage, setSuccessMessage] = useState("");
  const [tagsInput, setTagsInput] = useState("");

  useEffect(() => {
    if (!isNewSnippet) {
      fetchSnippetDetails();
    }
  }, [id]);

  const fetchSnippetDetails = async () => {
    try {
      const data = await snippetService.getSnippet(id);
      setSnippet(data);
      setTagsInput(data.tags.map((tag) => tag.name).join(", "));
    } catch (error) {
      setError("Failed to load snippet details.");
      console.error(error);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setSnippet({ ...snippet, [name]: value });
  };

  const handleTagsChange = (e) => {
    setTagsInput(e.target.value);
  };

  const handleToggleFavorite = async () => {
    if (isNewSnippet) {
      setSnippet({ ...snippet, is_favorite: !snippet.is_favorite });
      return;
    }

    try {
      const updatedSnippet = await snippetService.toggleFavorite(id);
      setSnippet({ ...snippet, is_favorite: updatedSnippet.is_favorite });
    } catch (error) {
      setError("Failed to update favorite status.");
      console.error(error);
    }
  };

  const handleSave = async () => {
    if (!snippet.title || !snippet.code || !snippet.language) {
      setError("Title, code, and language are required.");
      return;
    }

    setIsSaving(true);
    setError("");

    try {
      // Process tags from comma-separated string to array
      const tagArray = tagsInput
        .split(",")
        .map((tag) => tag.trim())
        .filter((tag) => tag.length > 0);

      const snippetToSave = {
        ...snippet,
        tags: tagArray,
      };

      const savedSnippet = await snippetService.createOrUpdateSnippet(
        snippetToSave,
        isNewSnippet ? null : id
      );

      setSuccessMessage("Snippet saved successfully!");
      setIsEditing(false);

      if (isNewSnippet) {
        navigate(`/snippet/${savedSnippet.id}`);
      } else {
        setSnippet(savedSnippet);
        setTagsInput(savedSnippet.tags.map((tag) => tag.name).join(", "));
      }
    } catch (error) {
      setError("Failed to save snippet.");
      console.error(error);
    } finally {
      setIsSaving(false);
    }
  };

  const handleDelete = async () => {
    if (window.confirm("Are you sure you want to delete this snippet?")) {
      try {
        await snippetService.deleteSnippet(id);
        navigate("/");
      } catch (error) {
        setError("Failed to delete snippet.");
        console.error(error);
      }
    }
  };

  const handleCopyCode = () => {
    navigator.clipboard
      .writeText(snippet.code)
      .then(() => {
        setSuccessMessage("Code copied to clipboard!");
        setTimeout(() => setSuccessMessage(""), 3000);
      })
      .catch(() => {
        setError("Failed to copy code.");
      });
  };

  return (
    <div className="snippet-detail-container">
      {error && <div className="error-message">{error}</div>}
      {successMessage && (
        <div className="success-message">{successMessage}</div>
      )}

      <div className="snippet-detail-header">
        <div className="title-section">
          {isEditing ? (
            <input
              type="text"
              name="title"
              value={snippet.title}
              onChange={handleInputChange}
              placeholder="Snippet Title"
              className="title-input"
            />
          ) : (
            <h2>{snippet.title || "New Snippet"}</h2>
          )}

          <button
            className={`favorite-btn ${snippet.is_favorite ? "active" : ""}`}
            onClick={handleToggleFavorite}
          >
            {snippet.is_favorite ? "★" : "☆"}
          </button>
        </div>

        <div className="action-buttons">
          {!isEditing && (
            <button className="edit-btn" onClick={() => setIsEditing(true)}>
              Edit
            </button>
          )}

          {!isNewSnippet && !isEditing && (
            <button className="delete-btn" onClick={handleDelete}>
              Delete
            </button>
          )}

          {isEditing && (
            <button
              className="save-btn"
              onClick={handleSave}
              disabled={isSaving}
            >
              {isSaving ? "Saving..." : "Save"}
            </button>
          )}

          {isEditing && !isNewSnippet && (
            <button
              className="cancel-btn"
              onClick={() => {
                setIsEditing(false);
                fetchSnippetDetails();
              }}
            >
              Cancel
            </button>
          )}

          {isEditing && isNewSnippet && (
            <button className="cancel-btn" onClick={() => navigate("/")}>
              Cancel
            </button>
          )}
        </div>
      </div>

      <div className="snippet-detail-content">
        <div className="snippet-form">
          {isEditing ? (
            <>
              <div className="form-group">
                <label>Description</label>
                <textarea
                  name="description"
                  value={snippet.description}
                  onChange={handleInputChange}
                  placeholder="Snippet Description"
                  rows="3"
                />
              </div>

              <div className="form-group">
                <label>Language</label>
                <input
                  type="text"
                  name="language"
                  value={snippet.language}
                  onChange={handleInputChange}
                  placeholder="Programming Language"
                  required
                />
              </div>

              <div className="form-group">
                <label>Tags (comma-separated)</label>
                <input
                  type="text"
                  value={tagsInput}
                  onChange={handleTagsChange}
                  placeholder="javascript, function, utility"
                />
              </div>

              <div className="form-group">
                <label>Code</label>
                <textarea
                  name="code"
                  value={snippet.code}
                  onChange={handleInputChange}
                  placeholder="Your code here..."
                  rows="10"
                  className="code-textarea"
                  required
                />
              </div>
            </>
          ) : (
            <>
              {snippet.description && (
                <div className="description-section">
                  <h3>Description</h3>
                  <p>{snippet.description}</p>
                </div>
              )}

              <div className="code-section">
                <div className="code-header">
                  <span className="language-badge">{snippet.language}</span>
                  <button className="copy-btn" onClick={handleCopyCode}>
                    Copy Code
                  </button>
                </div>
                <pre className={`language-${snippet.language}`}>
                  <code>{snippet.code}</code>
                </pre>
              </div>

              {snippet.tags && snippet.tags.length > 0 && (
                <div className="tags-section">
                  <h3>Tags</h3>
                  <div className="tags-list">
                    {snippet.tags.map((tag) => (
                      <span key={tag.id} className="tag">
                        {tag.name}
                      </span>
                    ))}
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  );
};

export default SnippetDetail;
