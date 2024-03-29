<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use HasUlids;
    protected $fillable = ['niveau'];

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(Emploidutemp::class);
    }
}
