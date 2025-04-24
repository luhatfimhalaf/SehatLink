<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AppointmentController extends Controller
{
    public function index()
    {
        return AppointmentResource::collection(Appointment::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'doctor_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'status' => 'nullable|string|in:scheduled,confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        // Validasi pasien
        $response = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($response->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        $appointment = Appointment::create($validated);
        return new AppointmentResource($appointment);
    }

    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        return new AppointmentResource($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'doctor_name' => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'status' => 'nullable|string|in:scheduled,confirmed,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        // Validasi pasien
        $response = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($response->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        $appointment->update($validated);
        return new AppointmentResource($appointment);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }
}
