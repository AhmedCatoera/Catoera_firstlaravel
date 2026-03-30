<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Incident extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_EN_ROUTE = 'en_route';

    public const STATUS_ON_SCENE = 'on_scene';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'incident_code',
        'incident_type',
        'location',
        'description',
        'severity_level',
        'date_reported',
        'status',
        'notes',
        'en_route_at',
        'on_scene_at',
        'resolved_at',
        'closed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_reported' => 'datetime',
            'en_route_at' => 'datetime',
            'on_scene_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Incident $incident): void {
            $code = 'INC-'.str_pad((string) $incident->id, 6, '0', STR_PAD_LEFT);
            $incident->forceFill(['incident_code' => $code])->saveQuietly();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ASSIGNED => 'Assigned',
            self::STATUS_EN_ROUTE => 'En Route',
            self::STATUS_ON_SCENE => 'On Scene',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_CLOSED => 'Closed',
        ];
    }
}
