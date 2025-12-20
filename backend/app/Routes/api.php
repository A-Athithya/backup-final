<?php
// backend/app/Routes/api.php

// Public Routes
Route::get('csrf-token', 'AuthController@csrf');
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::post('refresh-token', 'AuthController@refresh');
Route::post('csrf-regenerate', 'AuthController@regenerateCsrf', ['AuthMiddleware']);

// Protected Routes
// Middleware: AuthMiddleware

// Doctors
Route::get('doctors', 'DoctorController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::get('doctors/{id}', 'DoctorController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::post('doctors', 'DoctorController@store', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::put('doctors/{id}', 'DoctorController@update', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::delete('doctors/{id}', 'DoctorController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin']);


// User & Role Management
Route::get('users', 'UserController@index', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::get('users/{id}', 'UserController@show', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::post('users', 'UserController@store', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::put('users/{id}', 'UserController@update', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::delete('users/{id}', 'UserController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin']);

// Profile
Route::get('profile', 'UserController@getProfile', ['AuthMiddleware']);
Route::put('profile', 'UserController@updateProfile', ['AuthMiddleware']);

// Patient Management
Route::get('patients', 'PatientController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::get('patients/{id}', 'PatientController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::post('patients', 'PatientController@store', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::put('patients/{id}', 'PatientController@update', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);
Route::delete('patients/{id}', 'PatientController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::get('patients/{id}/appointments', 'PatientController@appointments', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse']);

// Appointment Management
Route::get('appointments', 'AppointmentController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Patient']);
Route::get('appointments/upcoming', 'AppointmentController@upcoming', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Patient']);
Route::get('appointments/calendar', 'AppointmentController@calendar', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Receptionist,Patient']);
Route::get('appointments/{id}', 'AppointmentController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Patient']);
Route::get('appointments/{id}/tooltip', 'AppointmentController@tooltip', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Receptionist']);
Route::post('appointments', 'AppointmentController@store', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Patient']);
Route::put('appointments/{id}', 'AppointmentController@update', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Patient']);
Route::delete('appointments/{id}', 'AppointmentController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Patient']);

// Prescription
Route::get('prescriptions', 'PrescriptionController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Pharmacist']);
Route::get('prescriptions/{id}', 'PrescriptionController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Nurse,Pharmacist']);
Route::post('prescriptions', 'PrescriptionController@store', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::patch('prescriptions/{id}/status', 'PrescriptionController@updateStatus', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::get('prescriptions/status/{status}', 'PrescriptionController@getByStatus', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::delete('prescriptions/{id}', 'PrescriptionController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);

// Dashboard
Route::get('dashboard', 'DashboardController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::get('dashboard/analytics', 'DashboardController@getAnalytics', ['AuthMiddleware', 'RoleMiddleware:Admin']);

// Communication
Route::post('communication/notes', 'CommunicationController@storeNote', ['AuthMiddleware', 'RoleMiddleware:Provider,Nurse']);
Route::get('communication/notes/appointment/{id}', 'CommunicationController@getNotesByAppointment', ['AuthMiddleware', 'RoleMiddleware:Provider,Nurse']);
Route::get('communication/history', 'CommunicationController@getHistory', ['AuthMiddleware', 'RoleMiddleware:Provider,Nurse']);

// Billing
Route::get('billing', 'BillingController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::get('billing/summary', 'BillingController@summary', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::get('billing/{id}', 'BillingController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::post('billing', 'BillingController@store', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider']);
Route::patch('billing/{id}/status', 'BillingController@updateStatus', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::get('patients/{id}/billing', 'BillingController@patientInvoices', ['AuthMiddleware', 'RoleMiddleware:Admin,Provider,Patient']);

// Staff
Route::get('staff', 'StaffController@index', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::get('staff/{id}', 'StaffController@show', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::post('staff', 'StaffController@store', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::put('staff/{id}', 'StaffController@update', ['AuthMiddleware', 'RoleMiddleware:Admin']);
Route::delete('staff/{id}', 'StaffController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin']);


// Settings
Route::post('logout', 'AuthController@logout', ['AuthMiddleware']);
Route::post('change-password', 'AuthController@changePassword', ['AuthMiddleware']);

// Inventory Routes
Route::get('medicines', 'InventoryController@index', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::get('medicines/{id}', 'InventoryController@show', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::post('medicines', 'InventoryController@store', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::put('medicines/{id}', 'InventoryController@update', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);
Route::delete('medicines/{id}', 'InventoryController@delete', ['AuthMiddleware', 'RoleMiddleware:Admin,Pharmacist']);

