// src/features/auth/authSlice.js
import { createSlice } from "@reduxjs/toolkit";

// Load tokens
const storedUser = localStorage.getItem("hcare_user");
const storedAccessToken = localStorage.getItem("hcare_access_token");
const storedRefreshToken = localStorage.getItem("hcare_refresh_token");

let preloadedUser = null;
if (storedUser) {
  try {
    preloadedUser = JSON.parse(storedUser);
  } catch (e) {
    console.error("Failed to parse stored user", e);
  }
}

const initialState = {
  user: preloadedUser, // Load from storage
  accessToken: storedAccessToken,
  refreshToken: storedRefreshToken,
  loading: false,
  error: null,
  tokenExpiresAt: null, // Hard to persist expiry accurately without timestamp
  csrfToken: null // CSRF usually not persisted long term but can be
};

const authSlice = createSlice({
  name: "auth",
  initialState,
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
      state.tokenExpiresAt = Date.now() + (action.payload.expiresIn * 1000);
      state.error = null;

      // Persistence
      localStorage.setItem("hcare_user", JSON.stringify(action.payload.user));
      localStorage.setItem("hcare_access_token", action.payload.accessToken);
      localStorage.setItem("hcare_refresh_token", action.payload.refreshToken);
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

      // Clear persistence
      localStorage.removeItem("hcare_user");
      localStorage.removeItem("hcare_access_token");
      localStorage.removeItem("hcare_refresh_token");
    },
  },
});

export const {
  loginStart,
  loginSuccess,
  loginFailure,
  logout,
} = authSlice.actions;

export default authSlice.reducer;
