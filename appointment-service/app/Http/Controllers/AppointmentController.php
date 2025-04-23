<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(title="Appointment Service API", version="1.0")
 */
class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/appointments",
     *     summary="List all appointments",
     *     tags={"Appointments"},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return AppointmentResource::collection(Appointment::all());
    }

    /**
     * @OA\Post(
     *     path="/api/appointments",
     *     summary="Create a new appointment",
     *     tags={"Appointments"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="doctor_name", type="string", example="Dr. Smith"),
     *             @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-04-15T10:00:00Z"),
     *             @OA\Property(property="status", type="string", example="scheduled"),
     *             @OA\Property(property="notes", type="string", example="Routine checkup")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Appointment created")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/appointments/{id}",
     *     summary="Get appointment by ID",
     *     tags={"Appointments"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        return new AppointmentResource($appointment);
    }

    /**
     * @OA\Put(
     *     path="/api/appointments/{id}",
     *     summary="Update an appointment",
     *     tags={"Appointments"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="doctor_name", type="string", example="Dr. Smith"),
     *             @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-04-15T10:00:00Z"),
     *             @OA\Property(property="status", type="string", example="confirmed"),
     *             @OA\Property(property="notes", type="string", example="Updated checkup")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Appointment updated"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/appointments/{id}",
     *     summary="Delete an appointment",
     *     tags={"Appointments"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Appointment deleted"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }
}
