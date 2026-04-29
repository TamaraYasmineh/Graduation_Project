<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalTest extends Model
{
  protected $fillable = [
        'medical_record_id',
        'file_path',
        'file_type',
        'test_type',
        'notes',
    ];

    //  Medical Record
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    //  uploader polymorphic
    public function uploadable()
    {
        return $this->morphTo();
    }

}
