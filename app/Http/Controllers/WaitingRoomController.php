<?php

namespace App\Http\Controllers;

use App\Models\ClinicStatus;
use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaitingRoomController extends Controller
{
   

    // Ajoute un patient à la salle d'attente
    public function add(Request $request)
    {
        $validated = $request->validate(['patient_id' => 'required|exists:patients,id']);

        $status = ClinicStatus::updateOrCreate(
            ['patient_id' => $validated['patient_id']],
            ['status' => 'waiting', 'arrival_time' => now()]
        );
        $status->load('patient');
        return response()->json($status, 201);
    }

    // Appelle un patient en consultation
    public function call(Request $request)
    {
        $validated = $request->validate(['patient_id' => 'required|exists:patients,id']);
        
        // Transaction pour s'assurer que tout se passe bien
        DB::transaction(function () use ($validated) {
            // S'il y a déjà un patient en consultation, on le remet en attente
            ClinicStatus::where('status', 'in_consultation')->update(['status' => 'waiting']);
            
            // On met le nouveau patient en consultation
            ClinicStatus::where('patient_id', $validated['patient_id'])->update(['status' => 'in_consultation']);
        });

        return $this->getCurrentStatus();
    }

    // Termine la consultation et retire le patient de la salle d'attente
    public function end(Request $request)
    {
        $validated = $request->validate(['patient_id' => 'required|exists:patients,id']);

        // On trouve la consultation la plus récente pour ce patient qui n'est pas terminée
        $consultation = Consultation::where('patient_id', $validated['patient_id'])
            ->where('is_completed', false)
            ->latest('date')
            ->first();

        if ($consultation) {
            $consultation->update(['is_completed' => true]);
        }

        ClinicStatus::where('patient_id', $validated['patient_id'])->delete();
        
        // On retourne le statut ET la consultation mise à jour
        return response()->json([
            'status' => $this->getCurrentStatus()->getData(),
            'updated_consultation' => $consultation
        ]);
    }
    
    // Annule la consultation et remet le patient en attente
    public function returnToWaiting(Request $request)
    {
        $validated = $request->validate(['patient_id' => 'required|exists:patients,id']);
        ClinicStatus::where('patient_id', $validated['patient_id'])
            ->update(['status' => 'waiting', 'arrival_time' => now()]);

        return $this->getCurrentStatus();
    }

    // Récupère l'état actuel de la salle d'attente
    private function getCurrentStatus()
    {
        $statuses = ClinicStatus::with('patient')->orderBy('arrival_time')->get();
        return response()->json([
            'waiting' => $statuses->where('status', 'waiting')->values(),
            'in_consultation' => $statuses->firstWhere('status', 'in_consultation'),
        ]);
    }

      public function getStatus()
    {
        return $this->getCurrentStatus();
    }
}