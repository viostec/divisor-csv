<?php

require "vendor/autoload.php";

if ($argc != 3) {
    echo "Usage: php divisor-php.php [csv] [num-linhas].\n";
    exit(1);
}

$csv = $argv[1];
$numLinhas = $argv[2];

if(!file_exists($csv)) {
    echo "CSV {$csv} não encontrado.\n";
    exit(1);
}

$csvReader = \League\Csv\Reader::createFromPath($csv, 'r');
$csvReader->setDelimiter(";");

$posicaoInicial = strrpos($csv, "/") + 1;
$nameCsv = rtrim(substr($csv, $posicaoInicial), ".csv");

//get the first row, usually the CSV header
$headers = $csvReader->fetchOne();

$page = 1;
$temLinhas = true;
while($temLinhas) {
    $offset = 1 + (($page-1)*$numLinhas);
    $res = $csvReader->setOffset($offset)->setLimit($numLinhas)->fetchAll();
    if(count($res) > 0) {
        echo count($res) . " linhas encontradas na página {$page} com offset {$offset}\n";
        $csvWriter = \League\Csv\Writer::createFromPath(__DIR__ . "/output/{$nameCsv}-{$page}.csv", 'w+');
        $csvWriter->setDelimiter(";");
        $csvWriter->insertOne($headers);

        $csvWriter->insertAll($res);
        $page++;
    } else {
        $temLinhas = false;
    }
}