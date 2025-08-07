<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Met à jour un ensemble de paramètres.
     */
    public function update(Request $request)
    {
        // Valide les clés attendues
        $validatedData = $request->validate([
         'cabinet_name' => 'nullable|string|max:255',
            'cabinet_address' => 'nullable|string|max:255',
            'cabinet_phone' => 'nullable|string|max:255',
            'default_vat_rate' => 'nullable|numeric|min:0', // Champ de la facturation
            'iban' => 'nullable|string|max:34',  
        ]);

        // Met à jour ou crée chaque paramètre dans la base de données
        foreach ($validatedData as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Renvoie tous les paramètres à jour pour synchroniser le frontend
        $settings = Setting::pluck('value', 'key');
        return response()->json($settings);
    }
}
