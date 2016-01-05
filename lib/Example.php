<?php

header('Content-type: text/html; charset=utf-8');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BoxClient.php';

$client = new BoxClient('0OY6z72xe7tBx18ai1mbU7lNh4xyEqgg');

// $drive = $client->listDrive();

// print_r(json_encode($drive));

$client->deleteFile(48666622353);
