<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReport extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'incident_id',
        'submitted_by',
        'resolution_details',
        'casualties',
        'damage_assessment',
        'date_submitted',
    ];

    protected function casts(): array
    {
        return [
            'date_submitted' => 'datetime',
        ];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
