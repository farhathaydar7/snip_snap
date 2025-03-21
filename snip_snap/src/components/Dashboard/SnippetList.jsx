import React from "react";
import SnippetCard from "./SnippetCard";

const SnippetList = ({ snippets, onToggleFavorite, onEditSnippet }) => {
  return (
    <div className="snippet-list">
      {snippets.map((snippet) => (
        <SnippetCard
          key={snippet.id}
          snippet={snippet}
          onToggleFavorite={onToggleFavorite}
          onEdit={onEditSnippet}
        />
      ))}
    </div>
  );
};

export default SnippetList;
