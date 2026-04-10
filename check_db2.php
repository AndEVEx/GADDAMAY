<?php
$conn = new mysqli('localhost', 'root', '', 'absen_smkn2_indramayu');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== GURU BK ===\n";
$res2 = $conn->query("SELECT t_ptk.id_ptk, t_ptk.nama_ptk, t_ptk.nomor_absensi, t_rombel.nm_rombel 
                      FROM t_ptk 
                      JOIN t_rombel ON t_rombel.id_guru_bk = t_ptk.id_ptk");
while ($row = $res2->fetch_assoc()) print_r($row);
