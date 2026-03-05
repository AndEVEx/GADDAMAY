<?php

namespace App\Libraries;

/**
 * GOWA (Go WhatsApp) Gateway Service
 * 
 * Handles communication with go-whatsapp-web-multidevice server
 * REST API with X-Device-Id header for multi-device support
 */
class WaGatewayService
{
    private $baseUrl;
    private $authUser;
    private $authPass;
    private $deviceIds = [];
    private $senderNumbers = [];
    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->loadSettings();
    }

    /**
     * Load settings from wa_settings table
     */
    private function loadSettings()
    {
        try {
            $rows = $this->db->table('wa_settings')->get()->getResultArray();
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['key']] = $row['value'];
            }

            $this->baseUrl = rtrim($settings['gateway_url'] ?? 'http://localhost:3000', '/');
            $this->authUser = $settings['basic_auth_user'] ?? '';
            $this->authPass = $settings['basic_auth_pass'] ?? '';
            $this->deviceIds = [
                $settings['device_id_1'] ?? '',
                $settings['device_id_2'] ?? '',
            ];
            $this->senderNumbers = [
                $settings['sender_number_1'] ?? '',
                $settings['sender_number_2'] ?? '',
            ];
        } catch (\Exception $e) {
            // Settings table may not exist yet
            $this->baseUrl = 'http://localhost:3000';
        }
    }

    /**
     * Get base URL
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Check device status
     * @param int $senderIndex 0 = primary, 1 = backup
     */
    public function checkStatus($senderIndex = 0)
    {
        $deviceId = $this->deviceIds[$senderIndex] ?? '';

        if (empty($deviceId)) {
            return [
                'connected' => false,
                'logged_in' => false,
                'error' => 'Device ID not found or not configured',
            ];
        }

        try {
            $url = $this->baseUrl . '/user/info';
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'X-Device-Id: ' . $deviceId,
                ],
            ]);

            if (!empty($this->authUser)) {
                curl_setopt($ch, CURLOPT_USERPWD, $this->authUser . ':' . $this->authPass);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return [
                    'connected' => false,
                    'logged_in' => false,
                    'device_id' => $deviceId,
                    'error' => 'cURL error: ' . $error,
                ];
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && $data) {
                return [
                    'connected' => true,
                    'logged_in' => isset($data['results']) && !empty($data['results']),
                    'device_id' => $deviceId,
                    'data' => $data['results'] ?? null,
                    'error' => null,
                ];
            }

            return [
                'connected' => ($httpCode >= 200 && $httpCode < 500),
                'logged_in' => false,
                'device_id' => $deviceId,
                'error' => $data['message'] ?? "HTTP $httpCode",
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'logged_in' => false,
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send WhatsApp message
     * @param string $phone Phone number (628xxx)
     * @param string $message Message text
     * @param int $senderIndex 0 = primary, 1 = backup
     */
    public function sendMessage($phone, $message, $senderIndex = 0)
    {
        $deviceId = $this->deviceIds[$senderIndex] ?? '';

        if (empty($deviceId)) {
            return ['success' => false, 'error' => 'Device ID not configured'];
        }

        // Ensure phone format: 628xxx@s.whatsapp.net
        $jid = $phone;
        if (strpos($phone, '@') === false) {
            $jid = $phone . '@s.whatsapp.net';
        }

        try {
            $url = $this->baseUrl . '/send/message';
            $postData = json_encode([
                'phone' => $jid,
                'message' => $message,
            ]);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'X-Device-Id: ' . $deviceId,
                ],
            ]);

            if (!empty($this->authUser)) {
                curl_setopt($ch, CURLOPT_USERPWD, $this->authUser . ':' . $this->authPass);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ['success' => false, 'error' => 'cURL error: ' . $error];
            }

            $data = json_decode($response, true);

            return [
                'success' => ($httpCode === 200),
                'http_code' => $httpCode,
                'response' => $data,
                'error' => ($httpCode !== 200) ? ($data['message'] ?? "HTTP $httpCode") : null,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
