<?php
$db = new mysqli('localhost', 'root', '', 'absen_smkn2');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$res = $db->query("DESCRIBE t_ptk");
while($row = $res->fetch_assoc()){ 
    echo $row['Field'] . "\n"; 
}
