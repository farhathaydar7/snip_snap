import React, { useState, useEffect, useRef } from "react";

const FilterBar = ({ onFilterChange }) => {
  const [searchTerm, setSearchTerm] = useState("");
  const [language, setLanguage] = useState("");
  const [tag, setTag] = useState("");
  const [favorites, setFavorites] = useState(false);
  const isInitialMount = useRef(true);

  // Languages supported by the application
  const languages = [
    "JavaScript",
    "TypeScript",
    "Python",
    "Java",
    "C#",
    "C++",
    "PHP",
    "Go",
    "Ruby",
    "Swift",
    "Kotlin",
    "Rust",
    "HTML",
    "CSS",
    "SQL",
    "Bash",
    "Other",
  ];

  // Apply filters when they change, but skip the first render
  useEffect(() => {
    // Skip the first render to prevent double API calls on component mount
    if (isInitialMount.current) {
      isInitialMount.current = false;
      return;
    }

    // Debounce filter changes
    const timeoutId = setTimeout(() => {
      const filters = {
        search: searchTerm,
        language: language,
        tag: tag,
        favorites: favorites,
      };
      onFilterChange(filters);
    }, 300); // 300ms debounce

    return () => clearTimeout(timeoutId);
  }, [searchTerm, language, tag, favorites]); // Remove onFilterChange from dependencies

  // Reset all filters
  const handleResetFilters = () => {
    // Reset local state
    setSearchTerm("");
    setLanguage("");
    setTag("");
    setFavorites(false);

    // Explicitly call onFilterChange with empty filters
    // This ensures the parent component gets notified immediately
    onFilterChange({
      search: "",
      language: "",
      tag: "",
      favorites: false,
    });
  };

  return (
    <div className="filter-bar">
      <div className="filter-section">
        <input
          type="text"
          placeholder="Search snippets..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="search-input"
        />
      </div>

      <div className="filter-section">
        <select
          value={language}
          onChange={(e) => setLanguage(e.target.value)}
          className="language-select"
        >
          <option value="">All Languages</option>
          {languages.map((lang) => (
            <option key={lang} value={lang}>
              {lang}
            </option>
          ))}
        </select>
      </div>

      <div className="filter-section">
        <input
          type="text"
          placeholder="Filter by tag"
          value={tag}
          onChange={(e) => setTag(e.target.value)}
          className="tag-input"
        />
      </div>

      <div className="filter-section checkbox">
        <label htmlFor="favorites-only">
          <input
            type="checkbox"
            id="favorites-only"
            checked={favorites}
            onChange={(e) => setFavorites(e.target.checked)}
          />
          Favorites only
        </label>
      </div>

      <button className="reset-filters-btn" onClick={handleResetFilters}>
        Reset Filters
      </button>
    </div>
  );
};

export default FilterBar;
