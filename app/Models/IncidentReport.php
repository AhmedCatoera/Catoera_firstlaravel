<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentReport extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'incident_id',
        'submitted_by',
        'resolution_details',
        'resolution_outcome',
        'operations_category',
        'response_effectiveness',
        'casualty_level',
        'property_damage_level',
        'actions_taken',
        'casualties',
        'damage_assessment',
        'follow_up_actions',
        'date_submitted',
    ];

    protected function casts(): array
    {
        return [
            'date_submitted' => 'datetime',
            'actions_taken' => 'array',
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

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class, 'report_id')->latest();
    }

    public static function outcomeLabels(): array
    {
        return [
            'resolved_on_site' => 'Resolved on Site',
            'stabilized_and_referred' => 'Stabilized and Referred',
            'escalated_to_specialized_unit' => 'Escalated to Specialized Unit',
            'false_alarm' => 'False Alarm / No Incident',
        ];
    }

    public static function operationsCategories(): array
    {
        return [
            'fire_suppression' => 'Fire Suppression',
            'medical_response' => 'Medical Response',
            'traffic_and_road_safety' => 'Traffic and Road Safety',
            'search_and_rescue' => 'Search and Rescue',
            'evacuation_and_relief' => 'Evacuation and Relief',
            'security_and_crowd_control' => 'Security and Crowd Control',
        ];
    }

    public static function effectivenessLabels(): array
    {
        return [
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'needs_improvement' => 'Needs Improvement',
        ];
    }

    public static function casualtyLevels(): array
    {
        return [
            'none' => 'None',
            'minor' => 'Minor Injuries',
            'serious' => 'Serious Injuries',
            'fatal' => 'Fatalities',
        ];
    }

    public static function damageLevels(): array
    {
        return [
            'none' => 'No Significant Damage',
            'low' => 'Low',
            'moderate' => 'Moderate',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    public static function actionChecklist(): array
    {
        return [
            'site_secured' => 'Site secured',
            'victims_treated' => 'Victims treated or triaged',
            'hazards_neutralized' => 'Immediate hazards neutralized',
            'coordinated_with_local_units' => 'Coordinated with local units',
            'public_advisory_issued' => 'Public advisory issued',
            'handover_completed' => 'Handover to receiving authority completed',
        ];
    }
}
