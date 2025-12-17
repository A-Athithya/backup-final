import { call, put, takeLatest } from "redux-saga/effects";
import { postData } from "../../api/client";
import {
  loginStart,
  loginSuccess,
  loginFailure,
} from "./authSlice";

function* loginSaga(action) {
  try {
    const { email, password, role } = action.payload;
    console.log("Saga: Logging in...", email);

    // Call Server /login endpoint directly
    // postData handles encryption of the payload
    const response = yield call(postData, "/login", { email, password, role });

    console.log("Saga: Login Response:", response);

    if (response.error) {
      throw new Error(response.error);
    }

    const { accessToken, refreshToken, user, expiresIn } = response;

    // Dispatch Success
    yield put(loginSuccess({
      user,
      accessToken,
      refreshToken,
      expiresIn
    }));

  } catch (err) {
    console.error("Saga: Login Error:", err);
    yield put(loginFailure(err.message || "Login failed"));
  }
}

export default function* authRootSaga() {
  yield takeLatest(loginStart.type, loginSaga);
}
