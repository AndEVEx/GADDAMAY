-- Migration v2: AbsensiSMK2 System Overhaul
-- Run this SQL after deploying the code changes

-- 1. Ensure wa_settings table exists
CREATE TABLE IF NOT EXISTS wa_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE,
    value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Add WA kill switch setting (default ON)
INSERT INTO wa_settings (`key`, value) VALUES ('wa_enabled', '1')
ON DUPLICATE KEY UPDATE value = value;

-- 3. Add channel JID settings for per-tingkat routing
INSERT INTO wa_settings (`key`, value) VALUES ('channel_jid_1', '')
ON DUPLICATE KEY UPDATE value = value;

INSERT INTO wa_settings (`key`, value) VALUES ('channel_jid_2', '')
ON DUPLICATE KEY UPDATE value = value;

-- 4. Add channel message template setting
INSERT INTO wa_settings (`key`, value) VALUES ('channel_message_template', '')
ON DUPLICATE KEY UPDATE value = value;

-- 5. Add batas_absen_pagi setting (08:00:00)
INSERT INTO wa_settings (`key`, value) VALUES ('batas_absen_pagi', '08:00:00')
ON DUPLICATE KEY UPDATE value = value;

-- 6. Add sender_index column to wa_message_queue if not exists
ALTER TABLE wa_message_queue ADD COLUMN IF NOT EXISTS sender_index TINYINT DEFAULT 0 AFTER week_end;

-- 7. Ensure t_siswa has password column
ALTER TABLE t_siswa ADD COLUMN IF NOT EXISTS password VARCHAR(255) DEFAULT NULL AFTER file;

-- 8. Ensure t_murid_monitoring table exists
CREATE TABLE IF NOT EXISTS t_murid_monitoring (
    id_monitoring INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    id_rombel INT NOT NULL,
    alasan TEXT,
    current_step TINYINT DEFAULT 1,
    status ENUM('active', 'completed', 'escalated') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_siswa (id_siswa),
    INDEX idx_rombel (id_rombel),
    INDEX idx_status (status)
);

-- 9. Ensure t_monitoring_progress table exists
CREATE TABLE IF NOT EXISTS t_monitoring_progress (
    id_progress INT AUTO_INCREMENT PRIMARY KEY,
    id_monitoring INT NOT NULL,
    step TINYINT NOT NULL COMMENT '1=BK, 2=Orangtua, 3=Homevisit, 4=Keputusan',
    is_done TINYINT DEFAULT 0,
    done_by INT,
    done_at DATETIME,
    file_bukti VARCHAR(255),
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_monitoring (id_monitoring),
    FOREIGN KEY (id_monitoring) REFERENCES t_murid_monitoring(id_monitoring) ON DELETE CASCADE
);

-- Done!
SELECT 'Migration v2 completed successfully' AS status;
