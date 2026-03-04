<?php

namespace App\Models;

use CodeIgniter\Model;

class WaMessageQueue_model extends Model
{
    protected $table = 'wa_message_queue';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_siswa',
        'phone_number',
        'recipient_name',
        'message',
        'message_type',
        'status',
        'scheduled_date',
        'week_start',
        'week_end',
        'sent_at',
        'error_message',
        'retry_count'
    ];

    /**
     * Get pending messages for today
     */
    public function getPendingForToday($limit = 50)
    {
        return $this->where('status', 'pending')
            ->where('scheduled_date', date('Y-m-d'))
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get all pending messages regardless of date
     */
    public function getAllPending($limit = 50)
    {
        return $this->where('status', 'pending')
            ->where('scheduled_date <=', date('Y-m-d'))
            ->orderBy('scheduled_date', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark message as sent
     */
    public function markAsSent($id)
    {
        return $this->update($id, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed($id, $errorMessage = null)
    {
        $data = $this->find($id);
        return $this->update($id, [
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => ($data['retry_count'] ?? 0) + 1
        ]);
    }

    /**
     * Mark message as processing
     */
    public function markAsProcessing($id)
    {
        return $this->update($id, [
            'status' => 'processing'
        ]);
    }

    /**
     * Check if weekly report already queued for this week
     */
    public function isWeekAlreadyQueued($weekStart, $weekEnd)
    {
        return $this->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->countAllResults() > 0;
    }

    /**
     * Get queue statistics
     */
    public function getStats()
    {
        $db = \Config\Database::connect();

        return [
            'pending' => $this->where('status', 'pending')->countAllResults(),
            'processing' => $this->where('status', 'processing')->countAllResults(),
            'sent' => $this->where('status', 'sent')->countAllResults(),
            'failed' => $this->where('status', 'failed')->countAllResults(),
            'today' => $this->where('scheduled_date', date('Y-m-d'))
                ->where('status', 'pending')
                ->countAllResults(),
        ];
    }

    /**
     * Reset failed messages for retry
     */
    public function resetFailedMessages($maxRetries = 3)
    {
        return $this->where('status', 'failed')
            ->where('retry_count <', $maxRetries)
            ->set('status', 'pending')
            ->update();
    }
}
