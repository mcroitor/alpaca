<?php

include_once __DIR__ . "/../../lib/autoload.php";

use mc\alpaca\OllamaClient;

$logger = \mc\Logger::stdout();

$ollamaUrl = "http://localhost:11434";
$client = new OllamaClient($ollamaUrl);
$models = $client->getModelsList();
$logger->info("Available models: " . implode(", ", $models));
