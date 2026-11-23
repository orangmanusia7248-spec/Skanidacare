<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'medical_record_number',
    'date_of_birth',
    'gender',
    'address',
    'phone',
    'disease_category_id',
    'diagnosis_date',
    'notes',
])]
class Patient extends Model
{
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'diagnosis_date' => 'date',
        ];
    }

    public function diseaseCategory(): BelongsTo
    {
        return $this->belongsTo(DiseaseCategory::class);
    }
}
