<?php

require_once __DIR__ . "/../Middleware/AuthMiddleware.php";
require_once __DIR__ . "/../Middleware/CsrfMiddleware.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// remove /backend/public
$uri = str_replace("/backend/public", "", $uri);

// ---------------- AUTH ----------------
if ($uri === "/api/login" && $method === "POST") {
    require_once "../Controllers/AuthController.php";
    AuthController::login();
    exit;
}

// ---------------- PATIENTS ----------------
if ($uri === "/api/patients" && $method === "GET") {
    AuthMiddleware::check();
    PatientController::index();
    exit;
}

if ($uri === "/api/patients" && $method === "POST") {
    CsrfMiddleware::check();
    AuthMiddleware::check();
    PatientController::store();
    exit;
}

if (preg_match("#^/api/patients/(\d+)$#", $uri, $m)) {
    AuthMiddleware::check();
    if ($method === "PUT") PatientController::update($m[1]);
    if ($method === "DELETE") PatientController::delete($m[1]);
    exit;
}

// ---------------- APPOINTMENTS ----------------
if ($uri === "/api/appointments" && $method === "GET") {
    AuthMiddleware::check();
    AppointmentController::index();
    exit;
}

if ($uri === "/api/appointments" && $method === "POST") {
    CsrfMiddleware::check();
    AuthMiddleware::check();
    AppointmentController::store();
    exit;
}

if (preg_match("#^/api/appointments/(\d+)$#", $uri, $m)) {
    AuthMiddleware::check();
    AppointmentController::update($m[1]);
    exit;
}

// ---------------- BILLING ----------------
if ($uri === "/api/billing" && $method === "GET") {
    AuthMiddleware::check();
    BillingController::index();
    exit;
}

if ($uri === "/api/billing" && $method === "POST") {
    CsrfMiddleware::check();
    AuthMiddleware::check();
    BillingController::store();
    exit;
}

// COMMUNICATION
if ($uri === "/api/communications" && $method === "GET") CommunicationController::index();
if ($uri === "/api/communications" && $method === "POST") CommunicationController::store();
if (preg_match("#^/api/communications/(\d+)$#", $uri, $m)) {
  if ($method === "PUT") CommunicationController::update($m[1]);
  if ($method === "DELETE") CommunicationController::delete($m[1]);
}

// NOTIFICATION
if ($uri === "/api/notifications" && $method === "GET") NotificationController::index();
if ($uri === "/api/notifications" && $method === "POST") NotificationController::store();
if (preg_match("#^/api/notifications/(\d+)$#", $uri, $m) && $method === "PUT")
  NotificationController::update($m[1]);

// INVENTORY
if ($uri === "/api/medicines" && $method === "GET") InventoryController::index();
if ($uri === "/api/medicines" && $method === "POST") InventoryController::store();
if (preg_match("#^/api/medicines/(\d+)$#", $uri, $m)) {
  if ($method === "PUT") InventoryController::update($m[1]);
  if ($method === "DELETE") InventoryController::delete($m[1]);
}

http_response_code(404);
echo json_encode(["error" => "Route not found"]);
