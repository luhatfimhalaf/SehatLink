<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'diagnosis' => $this->diagnosis,
            'prescription' => $this->prescription,
            'notes' => $this->notes,
            'visit_date' => $this->visit_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}