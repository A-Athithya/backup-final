import { call, put, takeLatest } from "redux-saga/effects";
import { getData, putData } from "../../api/client";
import {
  fetchProfileStart,
  fetchProfileSuccess,
  fetchProfileFailure,
  updateProfileStart,
  updateProfileSuccess,
  updateProfileFailure,
} from "./profileSlice";

function* fetchProfileWorker(action) {
  try {
    const { id, role, email } = action.payload || {};

    if (!role || !email) {
      throw new Error("Missing login user information");
    }

    let endpoint = "";

    switch (role) {
      case "admin":
        endpoint = `/users/${id}`;
        break;

      case "doctor":
        endpoint = `/doctors?email=${encodeURIComponent(email)}`;
        break;

      case "patient":
        endpoint = `/patients?email=${encodeURIComponent(email)}`;
        break;

      case "nurse":
        endpoint = `/nurses?email=${encodeURIComponent(email)}`;
        break;

      case "pharmacist":
        endpoint = `/pharmacists?email=${encodeURIComponent(email)}`;
        break;

      case "receptionist":
        endpoint = `/receptionists?email=${encodeURIComponent(email)}`;
        break;

      default:
        throw new Error(`Unsupported role type: ${role}`);
    }

    const response = yield call(getData, endpoint);

    // json-server returns array for filtered calls
    const profile = Array.isArray(response) ? response[0] : response;

    if (!profile) {
      throw new Error("Profile not found");
    }

    yield put(fetchProfileSuccess(profile));
  } catch (error) {
    yield put(fetchProfileFailure(error.message));
  }
}

function* updateProfileWorker(action) {
  try {
    const { id, role, data } = action.payload;

    if (!id || !role) {
      throw new Error("Missing profile ID or role");
    }

    // Determine table name based on role
    let tableName = "";
    switch (role) {
      case "doctor": tableName = "doctors"; break;
      case "patient": tableName = "patients"; break;
      case "nurse": tableName = "nurses"; break;
      case "pharmacist": tableName = "pharmacists"; break;
      case "receptionist": tableName = "receptionists"; break;
      case "admin": tableName = "users"; break;
      default: throw new Error("Unknown role");
    }

    const endpoint = `/${tableName}/${id}`;

    // putData handles encryption
    const response = yield call(putData, endpoint, data);

    yield put(updateProfileSuccess(response));

  } catch (error) {
    yield put(updateProfileFailure(error.message));
  }
}

export default function* profileSaga() {
  yield takeLatest(fetchProfileStart.type, fetchProfileWorker);
  yield takeLatest(updateProfileStart.type, updateProfileWorker);
}
