<?php
$log = file_get_contents('writable/logs/log-2026-03-31.log');
echo substr($log, -1500);
