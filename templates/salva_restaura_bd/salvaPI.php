<?php

use Symfony\Component\Process\Process;


$dbName = 'GMS_RFID_2';
$process = new Process(array(
   'mysqldump',
   '--user=' .getenv('DB_USER'),
   '--password=' .getenv('DB_PASS'),
    $dbName,
));
$a = $process->run();
echo $a;

