<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DeviceManager extends Controller
{
    public function index()
    {
        if (empty(session()->get('logged_in')) || session()->get('level') != 1) {
            return redirect()->to('Cpanel');
        }

        $db = \Config\Database::connect();

        // Check if device_scanners table exists, create if not
        if (!$db->tableExists('t_device_scanners')) {
            $db->query("CREATE TABLE t_device_scanners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_type VARCHAR(20) NOT NULL,
                slot_number INT NOT NULL,
                device_name VARCHAR(100) DEFAULT '',
                device_ip VARCHAR(50) DEFAULT '',
                device_status TINYINT DEFAULT 0,
                is_locked TINYINT DEFAULT 0,
                last_heartbeat DATETIME DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_slot (device_type, slot_number)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Seed 15 slots
            $types = ['rfid' => 5, 'barcode' => 5, 'camera' => 5];
            foreach ($types as $type => $count) {
                for ($i = 1; $i <= $count; $i++) {
                    $db->query("INSERT INTO t_device_scanners (device_type, slot_number) VALUES (?, ?)", [$type, $i]);
                }
            }
        }

        $devices = $db->query("SELECT * FROM t_device_scanners ORDER BY device_type, slot_number")->getResultArray();

        // Group by type
        $grouped = ['rfid' => [], 'barcode' => [], 'camera' => []];
        foreach ($devices as $d) {
            $grouped[$d['device_type']][] = $d;
        }

        $datanav = [
            'nama' => session()->get('nama'),
            'title' => 'Manajemen Perangkat Scanner',
            'nav' => 'DeviceManager'
        ];

        echo view('index/sidebar');
        echo view('func');
        echo view('index/navbar', $datanav);
        echo view('device/device_manager', ['devices' => $grouped]);
        echo view('index/footer');
    }

    public function update()
    {
        if (empty(session()->get('logged_in')) || session()->get('level') != 1) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');
        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');

        $allowed = ['device_name', 'device_ip', 'is_locked'];
        if (!in_array($field, $allowed)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Invalid field']);
        }

        $db->table('t_device_scanners')->where('id', $id)->update([$field => $value]);

        return $this->response->setJSON(['status' => true, 'message' => 'Updated']);
    }

    public function toggleLock()
    {
        if (empty(session()->get('logged_in')) || session()->get('level') != 1) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $device = $db->table('t_device_scanners')->where('id', $id)->get()->getRow();
        if (!$device) {
            return $this->response->setJSON(['status' => false, 'message' => 'Device not found']);
        }

        $newLock = $device->is_locked ? 0 : 1;
        $db->table('t_device_scanners')->where('id', $id)->update(['is_locked' => $newLock]);

        return $this->response->setJSON(['status' => true, 'locked' => $newLock]);
    }

    public function toggleStatus()
    {
        if (empty(session()->get('logged_in')) || session()->get('level') != 1) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $id = $this->request->getPost('id');

        $device = $db->table('t_device_scanners')->where('id', $id)->get()->getRow();
        if (!$device) {
            return $this->response->setJSON(['status' => false, 'message' => 'Device not found']);
        }

        $newStatus = $device->device_status ? 0 : 1;
        $db->table('t_device_scanners')->where('id', $id)->update([
            'device_status' => $newStatus,
            'last_heartbeat' => $newStatus ? date('Y-m-d H:i:s') : null
        ]);

        return $this->response->setJSON(['status' => true, 'device_status' => $newStatus]);
    }
}
