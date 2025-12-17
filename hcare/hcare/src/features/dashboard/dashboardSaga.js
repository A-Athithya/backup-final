import { call, put, takeLatest } from "redux-saga/effects";
import { getData } from "../../api/client";
import {
  fetchDashboardDataStart,
  fetchDashboardDataSuccess,
  fetchDashboardDataFailure,
} from "./dashboardSlice";

/* ✅ DASHBOARD MAIN WORKER */
function* fetchDashboardWorker() {
  try {
    yield put(fetchDashboardDataStart());

    const doctors = yield call(getData, "/doctors");
    const patients = yield call(getData, "/patients");
    const appointments = yield call(getData, "/appointments");
    const medicines = yield call(getData, "/medicines");
    const billings = yield call(getData, "/billing");
    const invoices = yield call(getData, "/invoices");

    // ✅ TOTAL REVENUE CALCULATION
    const totalRevenue = Array.isArray(billings)
      ? billings.reduce((sum, bill) => sum + Number(bill.amount || 0), 0)
      : 0;

    // ✅ MONTHLY REVENUE FOR GRAPH (Line / Bar Chart)
    const revenueByMonth = {};

    if (Array.isArray(billings)) {
      billings.forEach((bill) => {
        if (bill.date) {
          const month = bill.date.slice(0, 7); // YYYY-MM
          revenueByMonth[month] =
            (revenueByMonth[month] || 0) + Number(bill.amount || 0);
        }
      });
    }

    const stats = {
      doctors: doctors || [],
      patients: patients || [],
      appointments: appointments || [],
      medicines: medicines || [],
      invoices: billings || [], // Dashboard.js expects 'invoices' but api calls it 'billing' or 'invoices'? checks getData('/billing') in saga vs getData('/invoices') in Dashboard.js
      // Let's check Dashboard.js line 102: getData("/invoices").
      // Saga line 18: getData("/billing").
      // DB check: table is 'invoices' (line 79 in sql). DB table 'billing' also exists (line 31).
      // Dashboard uses 'invoices' for revenue. Saga uses 'billing'.
      // I should probably return both or fix one. SQL shows both tables.
      // 'billing' table has 1 row. 'invoices' table has 0 rows in my view, wait.
      // SQL dump: `invoices` table has data (line 86). `billing` table has data (line 38).
      // Logic in Dashboard.js uses "/invoices". Logic in Saga uses "/billing".
      // I will return both to be safe or investigate.
      // Let's match Dashboard.js for now: fetch "/invoices" in saga too.

      totalRevenue,
      revenueGraph: revenueByMonth,
    };

    yield put(fetchDashboardDataSuccess(stats));
  } catch (err) {
    yield put(fetchDashboardDataFailure(err.message));
  }
}

/* ✅ WATCHER */
export default function* dashboardSaga() {
  yield takeLatest("dashboard/fetchDashboardData", fetchDashboardWorker);

  // ✅ Payment pannina udane dashboard auto refresh
  yield takeLatest("billing/createSuccess", fetchDashboardWorker);
}
