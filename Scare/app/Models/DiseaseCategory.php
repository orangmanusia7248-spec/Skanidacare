<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'name', 'description'])]
class DiseaseCategory extends Model
{
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
