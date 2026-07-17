<?php
$conn = new mysqli('localhost', 'root', '', 'absen_smkn2_indramayu');
if ($conn->connect_error) die("Connection failed");

// Fetch 1 Walas
$res = $conn->query("
    SELECT p.id_ptk, p.nama_ptk, r.nm_rombel 
    FROM t_rombel r 
    JOIN t_ptk p ON r.id_walikelas = p.id_ptk 
    LIMIT 1
");
$walas = $res->fetch_assoc();

// Fetch 1 Guru BK
$res = $conn->query("
    SELECT p.id_ptk, p.nama_ptk, r.nm_rombel 
    FROM t_rombel r 
    JOIN t_ptk p ON r.id_guru_bk = p.id_ptk 
    LIMIT 1
");
$bk = $res->fetch_assoc();

print_r(['walas' => $walas, 'bk' => $bk]);

// Reset their passwords to '123456' so the user can login
$hashedPass = password_hash('123456', PASSWORD_DEFAULT);
if ($walas) {
    $conn->query("UPDATE t_ptk SET password = '{$hashedPass}' WHERE id_ptk = {$walas['id_ptk']}");
}
if ($bk) {
    $conn->query("UPDATE t_ptk SET password = '{$hashedPass}' WHERE id_ptk = {$bk['id_ptk']}");
}
echo "Passwords for the above accounts have been reset to 123456 for testing.\n";
