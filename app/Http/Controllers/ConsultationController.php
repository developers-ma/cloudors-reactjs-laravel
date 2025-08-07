<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}
