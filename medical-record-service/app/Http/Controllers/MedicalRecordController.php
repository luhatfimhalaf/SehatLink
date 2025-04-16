<?php
namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Http\Resources\MedicalRecordResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Info(title="Medical Record Service API", version="1.0")
 */
class MedicalRecordController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/medical-records",
     *     summary="List all medical records",
     *     tags={"Medical Records"},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return MedicalRecordResource::collection(MedicalRecord::all());
    }

    /**
     * @OA\Post(
     *     path="/api/medical-records",
     *     summary="Create a new medical record",
     *     tags={"Medical Records"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="diagnosis", type="string", example="Flu"),
     *             @OA\Property(property="prescription", type="string", example="Paracetamol"),
     *             @OA\Property(property="notes", type="string", example="Rest for 2 days"),
     *             @OA\Property(property="visit_date", type="string", format="date", example="2025-04-13")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Medical record created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'diagnosis' => 'required|string|max:255',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'visit_date' => 'required|date',
        ]);

        $response = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($response->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        $record = MedicalRecord::create($validated);
        return new MedicalRecordResource($record);
    }

    /**
     * @OA\Get(
     *     path="/api/medical-records/{id}",
     *     summary="Get medical record by ID",
     *     tags={"Medical Records"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Medical record not found")
     * )
     */
    public function show($id)
    {
        $record = MedicalRecord::findOrFail($id);
        return new MedicalRecordResource($record);
    }

    /**
     * @OA\Put(
     *     path="/api/medical-records/{id}",
     *     summary="Update a medical record",
     *     tags={"Medical Records"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="patient_id", type="integer", example=1),
     *             @OA\Property(property="diagnosis", type="string", example="Flu"),
     *             @OA\Property(property="prescription", type="string", example="Paracetamol"),
     *             @OA\Property(property="notes", type="string", example="Rest for 2 days"),
     *             @OA\Property(property="visit_date", type="string", format="date", example="2025-04-13")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Medical record updated"),
     *     @OA\Response(response=404, description="Medical record not found")
     * )
     */
    public function update(Request $request, $id)
    {
        $record = MedicalRecord::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'diagnosis' => 'required|string|max:255',
            'prescription' => 'nullable|string',
            'notes' => 'nullable|string',
            'visit_date' => 'required|date',
        ]);

        $response = Http::get("http://localhost:8000/api/patients/{$validated['patient_id']}");
        if ($response->failed()) {
            return response()->json(['error' => 'Invalid patient'], 400);
        }

        $record->update($validated);
        return new MedicalRecordResource($record);
    }

    /**
     * @OA\Delete(
     *     path="/api/medical-records/{id}",
     *     summary="Delete a medical record",
     *     tags={"Medical Records"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Medical record deleted"),
     *     @OA\Response(response=404, description="Medical record not found")
     * )
     */
    public function destroy($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->delete();
        return response()->json(null, 204);
    }
}