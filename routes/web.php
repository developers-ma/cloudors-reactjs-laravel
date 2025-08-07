<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ConsultationTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\WaitingRoomController;

Route::prefix('action')->group(function () {
    // Patients
    Route::post('/patients', [PatientController::class, 'store']);
    Route::put('/patients/{patient}', [PatientController::class, 'update']);
    Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);
    Route::post('/consultation-and-invoice', [ConsultationController::class, 'storeWithInvoice']);

    // Rendez-vous
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    
    // Salle d'attente
    Route::post('/waiting-room/add', [WaitingRoomController::class, 'add']);
    Route::post('/waiting-room/call', [WaitingRoomController::class, 'call']);
    Route::post('/waiting-room/end', [WaitingRoomController::class, 'end']);
    Route::post('/waiting-room/return', [WaitingRoomController::class, 'returnToWaiting']);
    Route::get('/waiting-room/status', [WaitingRoomController::class, 'getStatus']);

    // Consultations
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::put('/consultations/{consultation}', [ConsultationController::class, 'update']);
    Route::delete('/consultations/{consultation}', [ConsultationController::class, 'destroy']);
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show']);

    // Tarifs (Types de consultation)
    Route::post('/consultation-types', [ConsultationTypeController::class, 'store']);
    Route::delete('/consultation-types/{consultationType}', [ConsultationTypeController::class, 'destroy']);
    
    // Factures
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']); // Nouvelle route
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']); // Nouvelle route
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);

    // Paramètres généraux (Cabinet & Facturation)
    Route::put('/settings', [SettingController::class, 'update']);

    // Utilisateurs & Profil
    Route::get('/users', [UserController::class, 'index']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
});


// La route "catch-all" qui charge l'application React
Route::get('/{any?}', [AppController::class, 'index'])->where('any', '.*');