<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HL7MessageLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hl7_message_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'raw_message',
        'headers',
        'message_type',
        'message_control_id',
        'status',
        'parsed_data',
        'mapped_data',
        'error_message',
        'received_at',
        'processed_at',
        'completed_at',
        'admission_id',
        'patient_mrn',
        'patient_name',
        'processing_time_ms',
        'source_system',
        'destination_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'headers' => 'array',
        'parsed_data' => 'array',
        'mapped_data' => 'array',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'processing_time_ms' => 'integer',
    ];

    /**
     * Get the admission that was created from this message
     */
    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class, 'admission_id');
    }

    /**
     * Get the patient related to this message
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_mrn', 'mrn');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by message type
     */
    public function scopeWithMessageType($query, $messageType)
    {
        return $query->where('message_type', $messageType);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeWithDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('received_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by patient MRN
     */
    public function scopeWithPatientMrn($query, $mrn)
    {
        return $query->where('patient_mrn', $mrn);
    }

    /**
     * Scope for recent messages
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('received_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for failed messages
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for successful messages
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'processed');
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            'received' => 'info',
            'parsed' => 'warning',
            'mapped' => 'warning',
            'processed' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'received' => 'Received',
            'parsed' => 'Parsed',
            'mapped' => 'Mapped',
            'processed' => 'Processed',
            'failed' => 'Failed',
            default => 'Unknown',
        };
    }

    /**
     * Get processing time in a readable format
     */
    public function getProcessingTimeDisplayAttribute()
    {
        if (!$this->processing_time_ms) {
            return 'N/A';
        }

        if ($this->processing_time_ms < 1000) {
            return $this->processing_time_ms . ' ms';
        }

        return number_format($this->processing_time_ms / 1000, 2) . ' s';
    }

    /**
     * Calculate and update processing time
     */
    public function calculateProcessingTime()
    {
        if ($this->received_at && $this->completed_at) {
            $this->processing_time_ms = $this->received_at->diffInMilliseconds($this->completed_at);
            $this->save();
        }
    }

    /**
     * Get message summary for display
     */
    public function getMessageSummaryAttribute()
    {
        $summary = [
            'ID' => $this->message_id,
            'Type' => $this->message_type,
            'Status' => $this->status_display,
            'Patient' => $this->patient_name ?: 'Unknown',
            'MRN' => $this->patient_mrn ?: 'Unknown',
            'Received' => $this->received_at ? $this->received_at->format('Y-m-d H:i:s') : 'Unknown',
        ];

        if ($this->error_message) {
            $summary['Error'] = $this->error_message;
        }

        return $summary;
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStatistics($days = 30)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_messages' => self::where('received_at', '>=', $startDate)->count(),
            'successful_messages' => self::where('received_at', '>=', $startDate)
                ->where('status', 'processed')->count(),
            'failed_messages' => self::where('received_at', '>=', $startDate)
                ->where('status', 'failed')->count(),
            'pending_messages' => self::where('received_at', '>=', $startDate)
                ->whereIn('status', ['received', 'parsed', 'mapped'])->count(),
            'average_processing_time' => self::where('received_at', '>=', $startDate)
                ->where('status', 'processed')
                ->whereNotNull('processing_time_ms')
                ->avg('processing_time_ms'),
            'messages_by_day' => self::where('received_at', '>=', $startDate)
                ->selectRaw('DATE(received_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray(),
            'messages_by_status' => self::where('received_at', '>=', $startDate)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }

    /**
     * Get recent errors for monitoring
     */
    public static function getRecentErrors($limit = 10)
    {
        return self::where('status', 'failed')
            ->orderBy('received_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old log entries
     */
    public static function cleanupOldLogs($days = 90)
    {
        $cutoffDate = now()->subDays($days);
        
        return self::where('received_at', '<', $cutoffDate)
            ->where('status', 'processed')
            ->delete();
    }

    /**
     * Get message by control ID
     */
    public static function findByControlId($controlId)
    {
        return self::where('message_control_id', $controlId)->first();
    }

    /**
     * Check if message with control ID already exists
     */
    public static function existsByControlId($controlId)
    {
        return self::where('message_control_id', $controlId)->exists();
    }

    /**
     * Get admission success rate
     */
    public static function getAdmissionSuccessRate($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $totalMessages = self::where('received_at', '>=', $startDate)->count();
        $successfulAdmissions = self::where('received_at', '>=', $startDate)
            ->where('status', 'processed')
            ->whereNotNull('admission_id')
            ->count();
        
        return $totalMessages > 0 ? ($successfulAdmissions / $totalMessages) * 100 : 0;
    }

    /**
     * Get message processing performance metrics
     */
    public static function getPerformanceMetrics($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $metrics = self::where('received_at', '>=', $startDate)
            ->where('status', 'processed')
            ->whereNotNull('processing_time_ms')
            ->selectRaw('
                AVG(processing_time_ms) as avg_time,
                MIN(processing_time_ms) as min_time,
                MAX(processing_time_ms) as max_time,
                COUNT(*) as total_processed
            ')
            ->first();
        
        return [
            'average_processing_time' => $metrics->avg_time ? round($metrics->avg_time, 2) : 0,
            'min_processing_time' => $metrics->min_time ?: 0,
            'max_processing_time' => $metrics->max_time ?: 0,
            'total_processed' => $metrics->total_processed ?: 0,
        ];
    }
} 