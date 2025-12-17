// src/api/client.js
import axios from "axios";
import { getStoredToken } from "../utils/tokenHelper";

const API_BASE = process.env.REACT_APP_API_BASE;

// Axios instance
const api = axios.create({
  baseURL: API_BASE,
  headers: {
    "Content-Type": "application/json",
  },
});

// 🔐 Request interceptor (JWT attach)
api.interceptors.request.use((config) => {
  const token = getStoredToken();
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

/* ---------------- BASIC METHODS ---------------- */

export const getData = async (url) => {
  const res = await api.get(`/api${url}`);
  return res.data.data;
};

export const postData = async (url, payload) => {
  const res = await api.post(`/api${url}`, { payload });
  return res.data.data;
};

export const putData = async (url, payload) => {
  const res = await api.put(`/api${url}`, { payload });
  return res.data.data;
};

export const deleteData = async (url) => {
  const res = await api.delete(`/api${url}`);
  return res.data.data;
};
