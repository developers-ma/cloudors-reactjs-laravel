<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\ConsultationType;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    /**
     * Charge l'application React et injecte toutes les données initiales.
     */
    public function index()
    {
        // Transforme les paramètres en un objet simple clé -> valeur
        $settings = Setting::pluck('value', 'key');

        // Récupère l'utilisateur authentifié
        $user = Auth::user();

        // Rassemble toutes les données nécessaires pour l'application
        $initialData = [
            'patients' => Patient::orderBy('name')->get(),
            'consultations' => Consultation::with('patient:id,name,dob')->latest('id')->get(),
            'invoices' => Invoice::with('patient:id,name')->latest('date')->get(),
            'appointments' => Appointment::orderBy('date', 'asc')->get(),
            'consultationTypes' => ConsultationType::all(),
            'settings' => $settings,
            'authUser' => $user, // <-- Ajout de l'utilisateur connecté
        ];

        // Retourne la vue principale 'app.blade.php' qui contient le point d'entrée de React
        return view('app', ['initialData' => $initialData]);
    }
}
