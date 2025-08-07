<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    /**
     * Store a newly created consultation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'prescriptions' => 'nullable|array',
            'documents' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consultation = Consultation::create($validator->validated());

        // Eager load the patient relationship to include it in the response
        $consultation->load('patient:id,name,dob');

        return response()->json($consultation, 201);
    }

    /**
     * Update the specified consultation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Consultation  $consultation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Consultation $consultation)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'prescriptions' => 'nullable|array',
            'documents' => 'nullable|array',
            'is_completed' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consultation->update($validator->validated());
        
        $consultation->load('patient:id,name,dob');

        return response()->json($consultation);
    }

    /**
     * Remove the specified consultation from storage.
     *
     * @param  \App\Models\Consultation  $consultation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Consultation $consultation)
    {
        $consultation->delete();

        return response()->json(null, 204);
    }

    public function storeWithInvoice(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'reason' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $result = DB::transaction(function () use ($validatedData) {
                // 1. Créer la consultation
                $newConsultation = Consultation::create([
                    'patient_id' => $validatedData['patient_id'],
                    'date' => now(),
                    'reason' => $validatedData['reason'],
                    'assessment' => 'Facturation initiale', // Diagnostic par défaut
                    'is_completed' => false, // Marquer comme terminée
                ]);

                // 2. Créer la facture
                $newInvoice = Invoice::create([
                    'patient_id' => $validatedData['patient_id'],
                    'date' => now(),
                    'status' => 'En attente', // On suppose qu'elle est payée sur place
                    'amount' => $validatedData['amount'],
                    'invoice_number' => 'FACT-' . date('Ymd') . '-' . (Invoice::count() + 1),
                ]);

                // 3. Ajouter les lignes à la facture
                foreach ($validatedData['items'] as $item) {
                    $newInvoice->items()->create([
                        'description' => $item['description'],
                        'price' => $item['price'],
                    ]);
                }

                // Charger les relations pour la réponse
                $newConsultation->load('patient');
                $newInvoice->load(['patient', 'items']);

                return ['newConsultation' => $newConsultation, 'newInvoice' => $newInvoice];
            });

            return response()->json($result, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Une erreur est survenue lors de la création.', 'error' => $e->getMessage()], 500);
        }
    }
}
