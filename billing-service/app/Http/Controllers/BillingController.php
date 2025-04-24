<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Http\Resources\BillingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillingController extends Controller
{
    public function index()
    {
        return BillingResource::collection(Billing::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'appointment_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:unpaid,paid,failed',
            'description' => 'nullable|string',
        ]);

        // Validasi pasien
        $patientResponse = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($patientResponse->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        // Validasi appointment (jika ada)
        if (!empty($validated['appointment_id'])) {
            $appointmentResponse = Http::get("http://localhost:8001/api/appointments/{$validated['appointment_id']}");
            if ($appointmentResponse->failed()) {
                return response()->json(['error' => 'Invalid appointment'], 400);
            }
        }

        $billing = Billing::create($validated);
        return new BillingResource($billing);
    }

    public function show($id)
    {
        $billing = Billing::findOrFail($id);
        return new BillingResource($billing);
    }

    public function update(Request $request, $id)
    {
        $billing = Billing::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'appointment_id' => 'nullable|integer',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:unpaid,paid,failed',
            'description' => 'nullable|string',
        ]);

        // Validasi pasien
        $patientResponse = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($patientResponse->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        // Validasi appointment (jika ada)
        if (!empty($validated['appointment_id'])) {
            $appointmentResponse = Http::get("http://localhost:8001/api/appointments/{$validated['appointment_id']}");
            if ($appointmentResponse->failed()) {
                return response()->json(['error' => 'Invalid appointment'], 400);
            }
        }

        $billing->update($validated);
        return new BillingResource($billing);
    }

    public function destroy($id)
    {
        $billing = Billing::findOrFail($id);
        $billing->delete();
        return response()->json(null, 204);
    }
}
