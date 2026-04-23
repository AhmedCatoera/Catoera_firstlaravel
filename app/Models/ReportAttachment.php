<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAttachment extends Model
{
    protected $fillable = [
        'report_id',
        'uploaded_by',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class, 'report_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
