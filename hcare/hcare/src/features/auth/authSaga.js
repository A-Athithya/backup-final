import { call, put, takeLatest } from "redux-saga/effects";
import { postData } from "../../api/client";
import {
  loginStart,
  loginSuccess,
  loginFailure,
  logout
} from "./authSlice";

function* loginSaga(action) {
  try {
    const { email, password, role } = action.payload;

    // Call Server /login endpoint directly
    // postData handles encryption of the payload
    const response = yield call(postData, "/login", { email, password, role });

    if (response.error) {
      throw new Error(response.error);
    }

    const { accessToken, user, expiresIn } = response;

    // Dispatch Success
    yield put(loginSuccess({
      user,
      accessToken,
      expiresIn
    }));

  } catch (err) {
    console.error("Saga: Login Error:", err);
    yield put(loginFailure(err.message || "Login failed"));
  }
}

// Fire and forget logout request
function* logoutSaga() {
  try {
    yield call(postData, "/logout", {});
  } catch (err) {
    console.error("Saga: Logout API failed (ignoring)", err);
  }
}

export default function* authRootSaga() {
  yield takeLatest(loginStart.type, loginSaga);
  yield takeLatest(logout.type, logoutSaga);
}
