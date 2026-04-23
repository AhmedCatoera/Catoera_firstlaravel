<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;

class Incident extends Model
{
    public const TYPE_FIRE = 'Fire Emergency';

    public const TYPE_MEDICAL = 'Medical Emergency';

    public const TYPE_ROAD_ACCIDENT = 'Road Accident';

    public const TYPE_HAZMAT = 'Hazardous Materials';

    public const TYPE_NATURAL_DISASTER = 'Natural Disaster';

    public const TYPE_FLOOD = 'Flood Rescue';

    public const TYPE_EARTHQUAKE = 'Earthquake Response';

    public const TYPE_SECURITY_THREAT = 'Security Threat';

    public const TYPE_SEARCH_RESCUE = 'Search and Rescue';

    public const TYPE_STRUCTURAL_COLLAPSE = 'Structural Collapse';

    public const TYPE_POWER_UTILITY = 'Power or Utility Failure';

    public const TYPE_OTHER = 'Other Incident';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_EN_ROUTE = 'en_route';

    public const STATUS_ON_SCENE = 'on_scene';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const SLA_PENDING_MINUTES = 15;

    protected $fillable = [
        'incident_code',
        'incident_type',
        'location',
        'latitude',
        'longitude',
        'description',
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
            'latitude' => 'float',
            'longitude' => 'float',
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

    public function activities(): HasMany
    {
        return $this->hasMany(IncidentActivity::class)->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class)->latest();
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

    public static function incidentTypeLabels(): array
    {
        return [
            self::TYPE_FIRE => self::TYPE_FIRE,
            self::TYPE_MEDICAL => self::TYPE_MEDICAL,
            self::TYPE_ROAD_ACCIDENT => self::TYPE_ROAD_ACCIDENT,
            self::TYPE_HAZMAT => self::TYPE_HAZMAT,
            self::TYPE_NATURAL_DISASTER => self::TYPE_NATURAL_DISASTER,
            self::TYPE_FLOOD => self::TYPE_FLOOD,
            self::TYPE_EARTHQUAKE => self::TYPE_EARTHQUAKE,
            self::TYPE_SECURITY_THREAT => self::TYPE_SECURITY_THREAT,
            self::TYPE_SEARCH_RESCUE => self::TYPE_SEARCH_RESCUE,
            self::TYPE_STRUCTURAL_COLLAPSE => self::TYPE_STRUCTURAL_COLLAPSE,
            self::TYPE_POWER_UTILITY => self::TYPE_POWER_UTILITY,
            self::TYPE_OTHER => self::TYPE_OTHER,
        ];
    }

    public function logActivity(string $event, ?string $details = null, ?int $userId = null, array $meta = []): void
    {
        $this->activities()->create([
            'user_id' => $userId ?? auth()->id(),
            'event' => $event,
            'details' => $details,
            'meta' => Arr::where($meta, fn ($value) => ! is_null($value)),
        ]);
    }

    public function minutesOpen(): ?int
    {
        if (! $this->date_reported) {
            return null;
        }

        return $this->date_reported->diffInMinutes(now());
    }

    public function isSlaBreached(): bool
    {
        if (in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED], true)) {
            return false;
        }

        $minutesOpen = $this->minutesOpen();

        return $minutesOpen !== null && $minutesOpen > self::SLA_PENDING_MINUTES;
    }

    public function priorityScore(): int
    {
        $statusWeight = match ($this->status) {
            self::STATUS_PENDING => 120,
            self::STATUS_ASSIGNED => 90,
            self::STATUS_EN_ROUTE => 60,
            self::STATUS_ON_SCENE => 30,
            self::STATUS_RESOLVED => 10,
            self::STATUS_CLOSED => 0,
            default => 0,
        };

        $ageWeight = min(200, (int) floor(($this->minutesOpen() ?? 0) / 5));
        $slaBoost = $this->isSlaBreached() ? 80 : 0;

        return $statusWeight + $ageWeight + $slaBoost;
    }

    public function scopePrioritizedQueue($query)
    {
        return $query
            ->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_CLOSED])
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'assigned' THEN 2
                    WHEN 'en_route' THEN 3
                    WHEN 'on_scene' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('date_reported');
    }
}
