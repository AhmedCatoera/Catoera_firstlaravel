<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        'caller_name',
        'caller_phone',
        'caller_relation',
        'verification_status',
        'verification_sources',
        'verification_notes',
        'confidence_score',
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
            'verification_sources' => 'array',
            'confidence_score' => 'integer',
        ];
    }

    public static function verificationStatusLabels(): array
    {
        return [
            'unverified' => 'Unverified',
            'pending_callback' => 'Pending Callback',
            'partially_verified' => 'Partially Verified',
            'verified' => 'Verified',
            'false_report' => 'False Report',
        ];
    }

    public static function verificationSourceLabels(): array
    {
        return [
            'caller' => 'Caller',
            'callback' => 'Callback confirmed',
            'cctv' => 'CCTV',
            'security' => 'Security/Guard',
            'police' => 'Police',
            'unit_on_scene' => 'Unit on scene',
            'other' => 'Other',
        ];
    }

    public static function callerRelationLabels(): array
    {
        return [
            'witness' => 'Witness',
            'victim' => 'Victim',
            'relative' => 'Relative',
            'bystander' => 'Bystander',
            'anonymous' => 'Anonymous',
            'unknown' => 'Unknown',
        ];
    }

    public function canViewCallerPii(?User $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $user->isDispatcher();
    }

    public function maskedCallerPhone(): ?string
    {
        if (! $this->caller_phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $this->caller_phone);
        if (! $digits) {
            return 'Withheld';
        }
        if (strlen($digits) <= 4) {
            return str_repeat('*', max(0, strlen($digits) - 1)).substr($digits, -1);
        }

        return substr($digits, 0, 2).str_repeat('*', max(0, strlen($digits) - 4)).substr($digits, -2);
    }

    public function maskedCallerName(): ?string
    {
        if (! $this->caller_name) {
            return null;
        }

        $name = trim((string) $this->caller_name);
        if ($name === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $first = $parts[0] ?? '';
        $last = $parts[1] ?? '';
        if ($first && $last) {
            return Str::title($first).' '.Str::upper(Str::substr($last, 0, 1)).'.';
        }

        return Str::title($first);
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
