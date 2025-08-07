<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

      public function show(Invoice $invoice)
    {
        // 'load' s'assure que les relations 'patient' et 'items' 
        // sont incluses dans la réponse JSON.
        $invoice->load(['patient', 'items']);
        
        return response()->json($invoice);
    }

public function store(Request $request)
{
    $validatedData = $request->validate([
        'patient_id' => 'required|exists:patients,id',
        'date' => 'required|date',
        'status' => 'required|string',
        'items' => 'required|array|min:1',
        'items.*.description' => 'required|string',
        'items.*.price' => 'required|numeric|min:0',
        'amount' => 'required|numeric|min:0',
    ]);

    // Utiliser une transaction pour assurer l'intégrité des données
    $invoice = DB::transaction(function () use ($validatedData) {
        // 1. Créez la facture avec un numéro temporaire
        $newInvoice = Invoice::create([
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date'],
            'status' => $validatedData['status'],
            'amount' => $validatedData['amount'],
            'invoice_number' => 'TEMP-' . uniqid(), // Numéro temporaire unique
        ]);

        // 2. Mettez à jour avec le numéro de facture final basé sur l'ID
        $newInvoice->invoice_number = 'FACT-' . now()->format('Ymd') . '-' . $newInvoice->id;
        $newInvoice->save();

        // 3. Ajoutez les lignes de la facture
        foreach ($validatedData['items'] as $item) {
            $newInvoice->items()->create([
                'description' => $item['description'],
                'price' => $item['price'],
            ]);
        }

        return $newInvoice;
    });

    // Charger les relations pour la réponse JSON
    $invoice->load('patient', 'items');

    return response()->json($invoice, 201);
}

    public function update(Request $request, Invoice $invoice)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'date' => 'required|date',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        $updatedInvoice = DB::transaction(function () use ($validatedData, $invoice) {
            $invoice->update([
                'patient_id' => $validatedData['patient_id'],
                'date' => $validatedData['date'],
                'status' => $validatedData['status'],
                'amount' => $validatedData['amount'],
            ]);

            // Supprimer les anciens items et recréer les nouveaux
            $invoice->items()->delete();

            foreach ($validatedData['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'price' => $item['price'],
                ]);
            }

            return $invoice;
        });

        $updatedInvoice->load('patient', 'items');

        return response()->json($updatedInvoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->json(null, 204); // 204 No Content
    }
}