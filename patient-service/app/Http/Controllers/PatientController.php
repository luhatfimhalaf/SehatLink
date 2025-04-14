<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="Patient Service API", version="1.0")
 */
class PatientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/patients",
     *     summary="List all patients",
     *     tags={"Patients"},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return PatientResource::collection(Patient::all());
    }

    /**
     * @OA\Post(
     *     path="/api/patients",
     *     summary="Create a new patient",
     *     tags={"Patients"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="identity_number", type="string", example="ID123456"),
     *             @OA\Property(property="address", type="string", example="123 Main St")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Patient created")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/patients/{id}",
     *     summary="Get patient by ID",
     *     tags={"Patients"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Patient not found")
     * )
     */
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return new PatientResource($patient);
    }

    /**
     * @OA\Put(
     *     path="/api/patients/{id}",
     *     summary="Update a patient",
     *     tags={"Patients"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="identity_number", type="string", example="ID123456"),
     *             @OA\Property(property="address", type="string", example="123 Main St")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Patient updated"),
     *     @OA\Response(response=404, description="Patient not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/patients/{id}",
     *     summary="Delete a patient",
     *     tags={"Patients"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Patient deleted"),
     *     @OA\Response(response=404, description="Patient not found")
     * )
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        return response()->json(null, 204);
    }
}