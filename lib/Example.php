<?php

header('Content-type: text/html; charset=utf-8');

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BoxClient.php';

$client = new BoxClient('4SlT3BFKasfM7q6BgMdUCSZXi9NcSP4j');

// $drive = $client->listDrive();

// print_r(json_encode($drive));

$filer = fopen("hello.txt", 'w+');

$file = $client->downloadFile($filer, 48572010845);

