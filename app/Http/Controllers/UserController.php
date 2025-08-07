<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        // Récupère l'utilisateur actuellement connecté
        $user = Auth::user();

        // Si personne n'est connecté, renvoyer une erreur
        if (!$user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Valider les données reçues de React
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Mettre à jour l'utilisateur et sauvegarder
        $user->update($validatedData);

        // Renvoyer l'utilisateur mis à jour à React
        return response()->json($user);
    }
}