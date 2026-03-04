<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\WaMessageQueue_model;
use App\Libraries\WaGatewayService;

date_default_timezone_set('Asia/Jakarta');

/**
 * WhatsApp Queue Worker Controller
 * 
 * Processes pending messages from the queue and sends them via GOWA gateway
 * Supports round-robin between 2 sender numbers
 * 
 * Call via cron: /WaQueueWorker/process
 */
class WaQueueWorker extends Controller
{
    protected $waQueue;
    protected $waGateway;
    protected $db;

    public function __construct()
    {
        $this->waQueue = new WaMessageQueue_model();
        $this->waGateway = new WaGatewayService();
        $this->db = \Config\Database::connect();
    }

    /**
     * Process pending messages
     * 
     * This should be called by a cron job every minute or so
     * Example cron: * * * * * curl -s http://localhost/smkn2_indramayu_absen/smkn2_indramayu/public/WaQueueWorker/process
     * 
     * @param int $limit Maximum messages to process in one run
     */
    public function process($limit = 10)
    {
        // Get pending messages scheduled for today or earlier
        $today = date('Y-m-d');

        $messages = $this->db->table('wa_message_queue')
            ->where('status', 'pending')
            ->where('scheduled_date <=', $today)
            ->orderBy('scheduled_date', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        if (empty($messages)) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'No pending messages to process',
                'processed' => 0,
            ]);
        }

        $senderCount = $this->waGateway->getSenderCount();
        $delay = $this->waGateway->getMessageDelay();
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($messages as $index => $msg) {
            // Round-robin sender selection
            $senderIndex = $index % $senderCount;

            // Mark as processing
            $this->waQueue->update($msg['id'], [
                'status' => 'processing',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Send message
            $result = $this->waGateway->sendMessage(
                $msg['phone_number'],
                $msg['message'],
                $senderIndex
            );

            if ($result['success']) {
                // Mark as sent
                $this->waQueue->update($msg['id'], [
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                    'message_id' => $result['message_id'] ?? null,
                    'sender_index' => $senderIndex,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $successCount++;
                $results[] = [
                    'id' => $msg['id'],
                    'phone' => $msg['phone_number'],
                    'status' => 'sent',
                    'sender' => $senderIndex + 1,
                ];
            } else {
                // Mark as failed, increment retry count
                $retryCount = ($msg['retry_count'] ?? 0) + 1;
                $newStatus = $retryCount >= 3 ? 'failed' : 'pending'; // Retry up to 3 times

                $this->waQueue->update($msg['id'], [
                    'status' => $newStatus,
                    'error_message' => $result['error'] ?? 'Unknown error',
                    'retry_count' => $retryCount,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $failCount++;
                $results[] = [
                    'id' => $msg['id'],
                    'phone' => $msg['phone_number'],
                    'status' => $newStatus,
                    'error' => $result['error'] ?? 'Unknown error',
                    'retry' => $retryCount,
                ];
            }

            // Apply delay between messages (except for last one)
            if ($index < count($messages) - 1 && $delay > 0) {
                sleep($delay);
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => "Processed " . count($messages) . " messages",
            'success' => $successCount,
            'failed' => $failCount,
            'results' => $results,
        ]);
    }

    /**
     * Process messages in background (async-like)
     * 
     * This version processes one message at a time and returns quickly
     * Call repeatedly from a scheduler
     */
    public function processOne()
    {
        $today = date('Y-m-d');

        // Get one pending message
        $msg = $this->db->table('wa_message_queue')
            ->where('status', 'pending')
            ->where('scheduled_date <=', $today)
            ->orderBy('scheduled_date', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$msg) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'No pending messages',
                'sent' => false,
            ]);
        }

        // Determine sender (based on message ID for consistent distribution)
        $senderCount = $this->waGateway->getSenderCount();
        $senderIndex = $msg['id'] % $senderCount;

        // Mark as processing
        $this->waQueue->update($msg['id'], [
            'status' => 'processing',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Send message
        $result = $this->waGateway->sendMessage(
            $msg['phone_number'],
            $msg['message'],
            $senderIndex
        );

        if ($result['success']) {
            $this->waQueue->update($msg['id'], [
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
                'message_id' => $result['message_id'] ?? null,
                'sender_index' => $senderIndex,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Message sent successfully',
                'sent' => true,
                'phone' => $msg['phone_number'],
                'sender' => $senderIndex + 1,
            ]);
        } else {
            $retryCount = ($msg['retry_count'] ?? 0) + 1;
            $newStatus = $retryCount >= 3 ? 'failed' : 'pending';

            $this->waQueue->update($msg['id'], [
                'status' => $newStatus,
                'error_message' => $result['error'] ?? 'Unknown error',
                'retry_count' => $retryCount,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status' => false,
                'message' => 'Failed to send message',
                'sent' => false,
                'error' => $result['error'] ?? 'Unknown error',
                'retry_count' => $retryCount,
            ]);
        }
    }

    /**
     * Get queue statistics
     */
    public function stats()
    {
        $stats = [
            'pending' => $this->waQueue->where('status', 'pending')->countAllResults(),
            'processing' => $this->waQueue->where('status', 'processing')->countAllResults(),
            'sent' => $this->waQueue->where('status', 'sent')->countAllResults(),
            'failed' => $this->waQueue->where('status', 'failed')->countAllResults(),
        ];

        $stats['total'] = array_sum($stats);

        // Today's stats
        $today = date('Y-m-d');
        $stats['today_pending'] = $this->waQueue
            ->where('status', 'pending')
            ->where('scheduled_date', $today)
            ->countAllResults();
        $stats['today_sent'] = $this->waQueue
            ->where('status', 'sent')
            ->where('DATE(sent_at)', $today)
            ->countAllResults();

        return $this->response->setJSON([
            'status' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Check gateway status for both senders
     */
    public function gatewayStatus()
    {
        $status1 = $this->waGateway->checkStatus(0);
        $status2 = $this->waGateway->checkStatus(1);

        return $this->response->setJSON([
            'status' => true,
            'gateway_url' => $this->waGateway->getBaseUrl(),
            'sender_1' => [
                'connected' => $status1['connected'] ?? false,
                'logged_in' => $status1['logged_in'] ?? false,
                'device_id' => $status1['device_id'] ?? null,
                'error' => $status1['error'] ?? null,
            ],
            'sender_2' => [
                'connected' => $status2['connected'] ?? false,
                'logged_in' => $status2['logged_in'] ?? false,
                'device_id' => $status2['device_id'] ?? null,
                'error' => $status2['error'] ?? null,
            ],
        ]);
    }

    /**
     * Test send a message (for debugging)
     */
    public function testSend()
    {
        $request = service('request');
        $phone = $request->getVar('phone');
        $message = $request->getVar('message') ?? 'Test message from SMKN 2 Indramayu';
        $sender = (int) ($request->getVar('sender') ?? 0);

        if (empty($phone)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Phone number required. Usage: ?phone=628xxx&message=Hello&sender=0',
            ]);
        }

        $result = $this->waGateway->sendMessage($phone, $message, $sender);

        return $this->response->setJSON([
            'status' => $result['success'],
            'phone' => $phone,
            'message' => $message,
            'sender' => $sender + 1,
            'result' => $result,
        ]);
    }

    /**
     * Retry failed messages
     */
    public function retryFailed()
    {
        $updated = $this->waQueue
            ->where('status', 'failed')
            ->where('retry_count <', 3)
            ->set([
                'status' => 'pending',
                'updated_at' => date('Y-m-d H:i:s'),
            ])
            ->update();

        $count = $this->db->affectedRows();

        return $this->response->setJSON([
            'status' => true,
            'message' => "$count failed messages queued for retry",
        ]);
    }

    /**
     * Reset stuck processing messages
     * 
     * Messages that have been in 'processing' status for too long
     */
    public function resetStuck()
    {
        $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));

        $this->db->table('wa_message_queue')
            ->where('status', 'processing')
            ->where('updated_at <', $fiveMinutesAgo)
            ->update([
                'status' => 'pending',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $count = $this->db->affectedRows();

        return $this->response->setJSON([
            'status' => true,
            'message' => "$count stuck messages reset to pending",
        ]);
    }
}
