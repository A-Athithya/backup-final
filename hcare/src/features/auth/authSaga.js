import { call, put, takeLatest } from "redux-saga/effects";
import { postData } from "../../api/client";
import { loginStart, loginSuccess, loginFailure } from "./authSlice";

function* loginSaga(action) {
  try {
    const res = yield call(postData, "/api/login", action.payload);

    yield put(
      loginSuccess({
        user: res.user,
        token: res.token,
      })
    );
  } catch (e) {
    yield put(loginFailure("Invalid credentials"));
  }
}

export default function* authSaga() {
  yield takeLatest(loginStart.type, loginSaga);
}
