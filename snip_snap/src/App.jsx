import React from "react";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { AuthProvider, useAuth } from "./hooks/useAuth.jsx";
import Login from "./components/Auth/Login";
import Register from "./components/Auth/Register";
import Dashboard from "./components/Dashboard/Dashboard";
import SnippetDetail from "./components/Dashboard/SnippetDetail";
import "./App.css";
import "./components/css/Dashboard.css";
import "./components/css/SnippetDetail.css";

// Private route component that redirects to login if not authenticated
const PrivateRoute = ({ element }) => {
  const { isAuthenticated, loading } = useAuth();

  // Show loading indicator while checking authentication
  if (loading) {
    return <div className="loading-screen">Loading...</div>;
  }

  return isAuthenticated ? element : <Navigate to="/" replace />;
};

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          {/* Public routes */}
          <Route path="/" element={<Login />} />
          <Route path="/register" element={<Register />} />

          {/* Protected routes */}
          <Route
            path="/dashboard"
            element={<PrivateRoute element={<Dashboard />} />}
          />
          <Route
            path="/snippet/new"
            element={<PrivateRoute element={<SnippetDetail />} />}
          />
          <Route
            path="/snippet/:id"
            element={<PrivateRoute element={<SnippetDetail />} />}
          />

          {/* Fallback route */}
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  );
}

export default App;
