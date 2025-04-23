<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Http\Resources\BillingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(title="Billing Service API", version="1.0")
 */
class BillingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/billings",
     *     summary="List all billings",
     *     tags={"Billings"},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return BillingResource::collection(Billing::all());
    }

    /**
     * @OA\Post(
     *     path="/api/billings",
     *     summary="Create a new billing",
     *     tags={"Billings"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="appointment_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", example=100.00),
     *             @OA\Property(property="status", type="string", example="unpaid"),
     *             @OA\Property(property="description", type="string", example="Consultation fee")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Billing created")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/billings/{id}",
     *     summary="Get billing by ID",
     *     tags={"Billings"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Billing not found")
     * )
     */
    public function show($id)
    {
        $billing = Billing::findOrFail($id);
        return new BillingResource($billing);
    }

    /**
     * @OA\Put(
     *     path="/api/billings/{id}",
     *     summary="Update a billing",
     *     tags={"Billings"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="appointment_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", example=100.00),
     *             @OA\Property(property="status", type="string", example="paid"),
     *             @OA\Property(property="description", type="string", example="Consultation fee")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Billing updated"),
     *     @OA\Response(response=404, description="Billing not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/billings/{id}",
     *     summary="Delete a billing",
     *     tags={"Billings"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Billing deleted"),
     *     @OA\Response(response=404, description="Billing not found")
     * )
     */
    public function destroy($id)
    {
        $billing = Billing::findOrFail($id);
        $billing->delete();
        return response()->json(null, 204);
    }
}
