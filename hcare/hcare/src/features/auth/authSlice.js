// src/features/auth/authSlice.js
import { createSlice } from "@reduxjs/toolkit";
import { storeToken, storeRefreshToken, removeStoredToken, removeRefreshToken, getStoredToken, getRefreshToken, verifyToken } from "../../utils/tokenHelper";

const initialState = {
  user: null,
  accessToken: null,  // Short-lived (15 min) - stored in memory
  refreshToken: null, // Long-lived (7 days) - stored in localStorage
  loading: false,
  error: null,
  tokenExpiresAt: null, // Timestamp when access token expires
};

// Initialize state from stored tokens if available
const initializeAuthState = () => {
  const storedRefreshToken = getRefreshToken();

  if (storedRefreshToken) {
    const decoded = verifyToken(storedRefreshToken);
    if (decoded && decoded.type === 'refresh') {
      // Valid refresh token exists - will get new access token via refresh
      return {
        ...initialState,
        refreshToken: storedRefreshToken,
        user: decoded,
      };
    } else {
      // Invalid or expired refresh token
      removeRefreshToken();
      removeStoredToken();
    }
  }
  return initialState;
};

const authSlice = createSlice({
  name: "auth",
  initialState: initializeAuthState(),
  reducers: {
    loginStart(state, action) {
      state.loading = true;
      state.error = null;
    },

    loginSuccess(state, action) {
      state.loading = false;
      state.user = action.payload.user;
      state.accessToken = action.payload.accessToken;
      state.refreshToken = action.payload.refreshToken;
      state.tokenExpiresAt = Date.now() + (action.payload.expiresIn * 1000); // Convert to milliseconds
      state.error = null;

      // Store refresh token in localStorage (encrypted)
      storeRefreshToken(action.payload.refreshToken);
      // Access token stays in memory (Redux state) for security
    },

    loginFailure(state, action) {
      state.loading = false;
      state.user = null;
      state.accessToken = null;
      state.refreshToken = null;
      state.tokenExpiresAt = null;
      state.error = action.payload || "Login failed";
    },

    logout(state) {
      state.user = null;
      state.accessToken = null;
      state.refreshToken = null;
      state.tokenExpiresAt = null;
      state.loading = false;
      state.error = null;

      // Remove stored tokens
      removeRefreshToken();
      removeStoredToken();
    },

    // New action to update access token after refresh
    refreshTokenSuccess(state, action) {
      state.accessToken = action.payload.accessToken;
      state.refreshToken = action.payload.refreshToken;
      state.tokenExpiresAt = Date.now() + (action.payload.expiresIn * 1000);

      // Update stored refresh token (token rotation)
      storeRefreshToken(action.payload.refreshToken);
    },

    refreshTokenFailure(state) {
      // Refresh failed - logout user
      state.user = null;
      state.accessToken = null;
      state.refreshToken = null;
      state.tokenExpiresAt = null;
      state.error = "Session expired. Please login again.";

      removeRefreshToken();
      removeStoredToken();
    },

    // Check token validity on app start
    checkAuthStatus(state) {
      const storedRefreshToken = getRefreshToken();
      if (storedRefreshToken) {
        const decoded = verifyToken(storedRefreshToken);
        if (decoded && decoded.type === 'refresh') {
          state.user = decoded;
          state.refreshToken = storedRefreshToken;
          // Access token will be obtained via automatic refresh
        } else {
          // Token expired or invalid
          state.user = null;
          state.accessToken = null;
          state.refreshToken = null;
          removeRefreshToken();
          removeStoredToken();
        }
      }
    },
  },
});

export const {
  loginStart,
  loginSuccess,
  loginFailure,
  logout,
  refreshTokenSuccess,
  refreshTokenFailure,
  checkAuthStatus,
} = authSlice.actions;

export default authSlice.reducer;
