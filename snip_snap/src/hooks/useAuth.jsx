import { useState, useEffect, useContext, createContext } from "react";
import {
  login,
  register,
  logout,
  getCurrentUser,
  checkTokenValidity,
} from "../services/authService";
import UserModel from "../models/UserModel";

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Initial user load
  useEffect(() => {
    const loadUser = async () => {
      try {
        const userData = await getCurrentUser();
        setUser(userData ? UserModel.fromJson(userData) : null);
      } catch (err) {
        console.error("Error loading user:", err.message);
        setError(err.message);
        setUser(null);
      } finally {
        setLoading(false);
      }
    };

    loadUser();
  }, []);

  // Periodic JWT validation check
  useEffect(() => {
    let intervalId;

    if (user) {
      // Set up interval for JWT check every 5 seconds
      intervalId = setInterval(async () => {
        try {
          const isValid = await checkTokenValidity();
          if (!isValid) {
            console.log("JWT token is no longer valid, logging out");
            // Token is invalid, log the user out
            setUser(null);
            // Clean up local storage/cookie
            localStorage.removeItem("token");
            sessionStorage.removeItem("token");
          }
        } catch (err) {
          console.error("Error checking token validity:", err);
          // On error, assume token is invalid and log out
          setUser(null);
          localStorage.removeItem("token");
          sessionStorage.removeItem("token");
        }
      }, 5000); // Check every 5 seconds
    }

    return () => {
      // Clean up interval on component unmount
      if (intervalId) {
        clearInterval(intervalId);
      }
    };
  }, [user]);

  const loginUser = async (credentials) => {
    setLoading(true);
    try {
      const userData = await login(credentials);
      setUser(UserModel.fromJson(userData));
      setError(null);
      return userData;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const registerUser = async (userData) => {
    setLoading(true);
    try {
      const newUser = await register(userData);
      setUser(UserModel.fromJson(newUser));
      setError(null);
      return newUser;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  const logoutUser = async () => {
    setLoading(true);
    try {
      await logout();
      setUser(null);
      setError(null);
      localStorage.removeItem("token");
      sessionStorage.removeItem("token");
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        error,
        isAuthenticated: !!user,
        login: loginUser,
        register: registerUser,
        logout: logoutUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};
