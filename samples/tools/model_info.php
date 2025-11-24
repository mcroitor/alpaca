<?php

include_once __DIR__ . "/../../lib/autoload.php";

use mc\alpaca\OllamaClient;

$logger = \mc\Logger::stdout();

$file_name = str_replace(".php", "", basename(__FILE__));

if($argc < 2) {
    $logger->error("Usage: php {$file_name} <model_name>");
    exit(1);
}

$modelName = $argv[1];

$ollamaUrl = "http://localhost:11434";
$client = new OllamaClient($ollamaUrl);

$status = $client->getModelInfo($modelName);
$logger->info("Status of model '{$modelName}': " . json_encode($status));
