import React from "react";
import { formatDistanceToNow } from "date-fns";

const SnippetCard = ({ snippet, onToggleFavorite, onEdit }) => {
  const {
    id,
    title,
    description,
    code,
    language,
    is_favorite,
    created_at,
    tags,
  } = snippet;

  const handleToggleFavorite = (e) => {
    e.stopPropagation();
    onToggleFavorite(id, is_favorite);
  };

  const handleEdit = () => {
    onEdit(id);
  };

  // Format the date to be more readable
  const formattedDate = formatDistanceToNow(new Date(created_at), {
    addSuffix: true,
  });

  // Truncate code for preview
  const previewCode = code.length > 150 ? code.substring(0, 150) + "..." : code;

  return (
    <div className="snippet-card" onClick={handleEdit}>
      <div className="snippet-header">
        <h3 className="snippet-title">{title}</h3>
        <button
          className={`favorite-btn ${is_favorite ? "active" : ""}`}
          onClick={handleToggleFavorite}
          aria-label={
            is_favorite ? "Remove from favorites" : "Add to favorites"
          }
        >
          {is_favorite ? "★" : "☆"}
        </button>
      </div>

      {description && <p className="snippet-description">{description}</p>}

      <div className="code-preview">
        <pre className={`language-${language}`}>
          <code>{previewCode}</code>
        </pre>
      </div>

      <div className="snippet-footer">
        <div className="snippet-meta">
          <span className="language-badge">{language}</span>
          <span className="created-at">{formattedDate}</span>
        </div>

        {tags && tags.length > 0 && (
          <div className="snippet-tags">
            {tags.map((tag) => (
              <span key={tag.id} className="tag">
                {tag.name}
              </span>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default SnippetCard;
