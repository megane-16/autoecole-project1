<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'ville',
        'sexe',
        'date_naissance',
        'categorie_permis',
        'team_id',
        'horaire_formation',
        'has_cni',
        'status',
        'validation_code',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'has_cni' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
