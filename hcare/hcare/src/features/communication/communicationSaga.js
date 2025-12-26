// src/features/communication/communicationSaga.js
import { call, put, takeLatest, all } from "redux-saga/effects";
import { getData, postData, putData, deleteData } from "../../api/client";
import {
  fetchCommunicationsRequest,
  fetchCommunicationsSuccess,
  fetchCommunicationsFailure,
  fetchPatientsRequest,
  fetchPatientsSuccess,
  fetchPatientsFailure,
  fetchDoctorsRequest,
  fetchDoctorsSuccess,
  fetchDoctorsFailure,
  fetchPrescriptionsRequest,
  fetchPrescriptionsSuccess,
  fetchPrescriptionsFailure,
  createCommunicationRequest,
  createCommunicationSuccess,
  createCommunicationFailure,
  updateCommunicationRequest,
  updateCommunicationSuccess,
  updateCommunicationFailure,
  deleteCommunicationRequest,
  deleteCommunicationSuccess,
  deleteCommunicationFailure,
} from "./communicationSlice";

/* ------------------- COMMUNICATIONS ------------------- */
function* fetchCommunicationsSaga(action) {
  try {
    // Backend API uses /communication/history for listing
    const res = yield call(getData, "/communication/history");
    const list = Array.isArray(res) ? res : res ? [res] : [];
    yield put(fetchCommunicationsSuccess(list));
  } catch (err) {
    yield put(fetchCommunicationsFailure(err.message));
  }
}

function* createCommunicationSaga(action) {
  try {
    const { patientId, doctorId, query, senderId, senderRole } = action.payload;

    // 1. Find latest appointment to attach this note to
    const appointments = yield call(getData, `/patients/${patientId}/appointments`);

    // Filter by doctor if selected
    const relAppointment = (appointments || [])
      .filter(a => String(a.doctorId) === String(doctorId)) // Check if doctorId matches
      .sort((a, b) => new Date(b.appointment_date) - new Date(a.appointment_date))[0]; // Latest

    if (!relAppointment) {
      throw new Error("No appointment found with this doctor.");
    }

    // 2. Post Note
    const payload = {
      appointment_id: relAppointment.id,
      content: query,
      sender_id: senderId,
      sender_role: senderRole
    };

    const res = yield call(postData, "/communication/notes", payload);
    yield put(createCommunicationSuccess(res));

    // Refresh list
    yield put(fetchCommunicationsRequest());

  } catch (err) {
    yield put(createCommunicationFailure(err.message));
  }
}

function* updateCommunicationSaga(action) {
  // Not implemented in backend yet
}

function* deleteCommunicationSaga(action) {
  // Not implemented in backend yet
}

/* ------------------- PATIENTS ------------------- */
function* fetchPatientsSaga() {
  try {
    const res = yield call(getData, "/patients");
    yield put(fetchPatientsSuccess(Array.isArray(res) ? res : [res]));
  } catch (err) {
    yield put(fetchPatientsFailure(err.message));
  }
}

/* ------------------- DOCTORS ------------------- */
function* fetchDoctorsSaga() {
  try {
    const res = yield call(getData, "/doctors");
    yield put(fetchDoctorsSuccess(Array.isArray(res) ? res : [res]));
  } catch (err) {
    yield put(fetchDoctorsFailure(err.message));
  }
}

/* ------------------- PRESCRIPTIONS ------------------- */
function* fetchPrescriptionsSaga() {
  try {
    const res = yield call(getData, "/prescriptions");
    yield put(fetchPrescriptionsSuccess(Array.isArray(res) ? res : [res]));
  } catch (err) {
    yield put(fetchPrescriptionsFailure(err.message));
  }
}

/* ------------------- ROOT ------------------- */
export default function* communicationSaga() {
  yield all([
    takeLatest(fetchCommunicationsRequest.type, fetchCommunicationsSaga),
    takeLatest(createCommunicationRequest.type, createCommunicationSaga),
    takeLatest(updateCommunicationRequest.type, updateCommunicationSaga),
    takeLatest(deleteCommunicationRequest.type, deleteCommunicationSaga),
    takeLatest(fetchPatientsRequest.type, fetchPatientsSaga),
    takeLatest(fetchDoctorsRequest.type, fetchDoctorsSaga),
    takeLatest(fetchPrescriptionsRequest.type, fetchPrescriptionsSaga),
  ]);
}
