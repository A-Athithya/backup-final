import axios from "axios";

const API_BASE = "http://localhost/Hcare%20php%20int/backend/public";

const api = axios.create({
  baseURL: API_BASE,
  timeout: 10000,
  headers: { "Content-Type": "application/json" },
  withCredentials: true
});

// Response Interceptor for Error Handling (Simple)
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login or handle unauth
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

/* ================= GET ================= */
export const getData = async (endpoint) => {
  const res = await api.get(endpoint);
  return res.data;
};

/* ================= POST ================= */
export const postData = async (endpoint, payload) => {
  const res = await api.post(endpoint, payload);
  return res.data;
};

/* ================= PUT ================= */
export const putData = async (endpoint, payload) => {
  const res = await api.put(endpoint, payload);
  return res.data;
};

/* ================= DELETE ================= */
export const deleteData = async (endpoint) => {
  const res = await api.delete(endpoint);
  return res.data;
};

// Exporting helpers for compatibility if needed elsewhere
export const setCsrfToken = () => { };

export default api;
