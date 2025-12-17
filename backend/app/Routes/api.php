<?php
// backend/app/Routes/api.php

// Public Routes
Route::get('csrf-token', 'AuthController@csrf');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('refresh-token', 'AuthController@refresh');

// Protected Routes
// Middleware: AuthMiddleware

// Doctors
Route::get('doctors', 'DoctorController@index', ['AuthMiddleware']);
Route::get('doctors/{id}', 'DoctorController@show', ['AuthMiddleware']);
Route::post('doctors', 'DoctorController@store', ['AuthMiddleware']);
Route::put('doctors/{id}', 'DoctorController@update', ['AuthMiddleware']);
Route::delete('doctors/{id}', 'DoctorController@delete', ['AuthMiddleware']);


// User & Role Management
Route::get('users', 'UserController@index', ['AuthMiddleware']);
Route::post('users', 'UserController@store', ['AuthMiddleware']);

// Patient Management
Route::get('patients', 'PatientController@index', ['AuthMiddleware']);
Route::get('patients/{id}', 'PatientController@show', ['AuthMiddleware']);
Route::post('patients', 'PatientController@store', ['AuthMiddleware']);
Route::put('patients/{id}', 'PatientController@update', ['AuthMiddleware']);
Route::delete('patients/{id}', 'PatientController@delete', ['AuthMiddleware']);

// Appointment Management
Route::get('appointments', 'AppointmentController@index', ['AuthMiddleware']);
Route::post('appointments', 'AppointmentController@store', ['AuthMiddleware']);
Route::put('appointments/{id}', 'AppointmentController@update', ['AuthMiddleware']);

// Prescription
Route::get('prescriptions', 'PrescriptionController@index', ['AuthMiddleware']);
Route::post('prescriptions', 'PrescriptionController@store', ['AuthMiddleware']);

// Dashboard
Route::get('dashboard', 'DashboardController@index', ['AuthMiddleware']);

// Billing
Route::get('billing', 'BillingController@index', ['AuthMiddleware']);

// Staff

Route::get('staff', 'StaffController@index', ['AuthMiddleware']);
Route::get('staff/{id}', 'StaffController@show', ['AuthMiddleware']);
Route::post('staff', 'StaffController@store', ['AuthMiddleware']);
Route::put('staff/{id}', 'StaffController@update', ['AuthMiddleware']);
Route::delete('staff/{id}', 'StaffController@delete', ['AuthMiddleware']);


// Settings
Route::post('logout', 'AuthController@logout', ['AuthMiddleware']);
Route::post('change-password', 'AuthController@changePassword', ['AuthMiddleware']);

// Inventory Routes
Route::get('medicines', 'InventoryController@index', ['AuthMiddleware']);
Route::get('medicines/{id}', 'InventoryController@show', ['AuthMiddleware']);
Route::post('medicines', 'InventoryController@store', ['AuthMiddleware']);
Route::put('medicines/{id}', 'InventoryController@update', ['AuthMiddleware']);
Route::delete('medicines/{id}', 'InventoryController@delete', ['AuthMiddleware']);

