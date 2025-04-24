<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        return PatientResource::collection(Patient::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients|max:255',
            'phone' => 'nullable|string|max:20',
            'identity_number' => 'required|string|unique:patients|max:50',
            'address' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);
        return new PatientResource($patient);
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return new PatientResource($patient);
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'identity_number' => 'required|string|unique:patients,identity_number,' . $id . '|max:50',
            'address' => 'nullable|string',
        ]);

        $patient->update($validated);
        return new PatientResource($patient);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return response()->json(null, 204);
    }
}
