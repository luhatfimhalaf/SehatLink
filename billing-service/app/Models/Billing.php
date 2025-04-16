<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'patient_id',
        'appointment_id',
        'amount',
        'status',
        'description'];
}
