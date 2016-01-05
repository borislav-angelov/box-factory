<?php

header('Content-type: text/html; charset=utf-8');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BoxClient.php';

$client = new BoxClient('XFHR9xaPbNGsQ09vXdkXZelSqLfa5liU');

// $drive = $client->listDrive();

// print_r(json_encode($drive));

$data = $client->uploadFile('C:\wamp\www\wordpress\wp-content\box-factory\lib\uploader.txt', 0);

print_r(json_encode($data));
