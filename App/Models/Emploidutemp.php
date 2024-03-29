<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emploidutemp extends Model
{
    use HasUlids;
    protected $fillable = [
        'Cours',
        'DebutCours',
        'FinCours',
        'salle_id',
        'prof_id',
        'classe_id',
    ];

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class, 'prof_id', 'id');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }
}
