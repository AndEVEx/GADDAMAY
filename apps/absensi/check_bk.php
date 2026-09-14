<?php
$conn = new mysqli('localhost', 'root', '', 'absen_smkn2_indramayu');
if ($conn->connect_error) die("Connection failed");

$res = $conn->query("SELECT DISTINCT id_guru_bk FROM t_rombel");
echo "id_guru_bk in rombel:\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
$res = $conn->query("SELECT id_ptk, nama_ptk, level FROM t_ptk WHERE id_ptk IN (SELECT DISTINCT id_guru_bk FROM t_rombel)");
echo "Matched Guru BK in t_ptk:\n";
while ($row = $res->fetch_assoc()) {
    print_r($row);
}
