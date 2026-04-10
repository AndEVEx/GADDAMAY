<?php
$conn = new mysqli('localhost', 'root', '', 'absen_smkn2_indramayu');
$res = $conn->query("SELECT nisn, COUNT(*) as c FROM t_siswa WHERE nisn != '' GROUP BY nisn HAVING c > 1");
if ($res) {
    while($row = $res->fetch_assoc()) print_r($row);
}
$res = $conn->query("SELECT no_induk, COUNT(*) as c FROM t_siswa WHERE no_induk != '' GROUP BY no_induk HAVING c > 1");
if ($res) {
    while($row = $res->fetch_assoc()) print_r($row);
}
