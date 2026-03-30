<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = [
        'incident_id',
        'team_id',
        'assigned_date',
        'arrival_time',
        'completion_time',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'datetime',
            'arrival_time' => 'datetime',
            'completion_time' => 'datetime',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
