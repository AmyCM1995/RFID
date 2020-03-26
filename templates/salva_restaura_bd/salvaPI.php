<?php

use Symfony\Component\Process\Process;

$process = Process::fromShellCommandline('echo "$name"');
$process->run(null, ['name' => 'Amy']);


/*$dbName = 'GMS_RFID_2';
$process = new Process(array(
   'mysqldump',
   '--user=' .getenv('DB_USER'),
   '--password=' .getenv('DB_PASS'),
    $dbName,
));
$process->run();*/

