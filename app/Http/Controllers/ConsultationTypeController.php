<?php

namespace App\Http\Controllers;

use App\Models\ConsultationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultationTypeController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:consultation_types,name',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consultationType = ConsultationType::create($validator->validated());
        return response()->json($consultationType, 201);
    }

    public function destroy(ConsultationType $consultationType)
    {
        $consultationType->delete();
        return response()->json(null, 204);
    }
}