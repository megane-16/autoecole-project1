<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property int $instructor_id
 * @property int|null $student_id
 * @property int|null $vehicle_id
 * @property string|null $title
 * @property string $type
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property string $status
 * @property string|null $pickup_location
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 * @property-read User $instructor
 * @property-read User|null $student
 */
class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'instructor_id',
        'student_id',
        'vehicle_id',
        'title',
        'type',
        'starts_at',
        'ends_at',
        'status',
        'pickup_location',
        'notes',
        'media_url',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->type) {
            'conduite' => 'Leçon de conduite',
            'code' => 'Cours de code',
            'examen_blanc' => 'Examen blanc',
            'evaluation' => 'Évaluation initiale',
            default => ucfirst($this->type),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'planifie' => 'gold',
            'en_cours' => 'blue',
            'termine' => 'green',
            'annule' => 'red',
            default => 'gold',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
            default => ucfirst($this->status),
        };
    }
}
