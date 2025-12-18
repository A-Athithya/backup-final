// src/features/auth/authSlice.js
import { createSlice } from "@reduxjs/toolkit";

// Simple persistence: check localStorage directly
const storedUser = localStorage.getItem("hcare_user");
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
  accessToken: null,
  refreshToken: null,
  loading: false,
  error: null,
  tokenExpiresAt: null,
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

      // Simple persistence
      localStorage.setItem("hcare_user", JSON.stringify(action.payload.user));
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
