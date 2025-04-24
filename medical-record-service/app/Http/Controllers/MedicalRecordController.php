<?php
namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Http\Resources\MedicalRecordResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MedicalRecordController extends Controller
{
    public function index()
    {
        return MedicalRecordResource::collection(MedicalRecord::all());
    }

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

    public function show($id)
    {
        $record = MedicalRecord::findOrFail($id);
        return new MedicalRecordResource($record);
    }

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

    public function destroy($id)
    {
        $record = MedicalRecord::findOrFail($id);
        $record->delete();
        return response()->json(null, 204);
    }
}
