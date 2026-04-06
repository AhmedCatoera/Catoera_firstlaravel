<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_STAFF = 'staff';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_leader_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')->withTimestamps();
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Team IDs this user leads (assigned as team leader on teams table).
     */
    public function ledTeamIds(): \Illuminate\Support\Collection
    {
        return $this->ledTeams()->pluck('id');
    }

    /**
     * All team IDs the user is associated with (leader or member).
     */
    public function associatedTeamIds(): \Illuminate\Support\Collection
    {
        return $this->ledTeamIds()
            ->merge($this->teams()->pluck('teams.id'))
            ->unique()
            ->values();
    }

    public function isLeaderOfAssignedTeam(Incident $incident): bool
    {
        if (! $incident->relationLoaded('assignment')) {
            $incident->load('assignment.team');
        }

        if (! $incident->assignment || ! $incident->assignment->team) {
            return false;
        }

        return (int) $incident->assignment->team->team_leader_id === (int) $this->id;
    }

    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_STAFF => 'Staff',
        ];
    }
}
