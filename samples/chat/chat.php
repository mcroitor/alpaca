<?php
/**
 * Sample script for chat using Ollama client
 * as a parameter of script a model name can be provided
 */

include_once __DIR__ . "/../../lib/autoload.php";
use mc\alpaca\OllamaClient;
use mc\alpaca\OllamaResponse;

$modelName = $argv[1] ?? "llama3.2:latest";

// set logger
$logger = \mc\Logger::stdout();

// ollama server URL
$ollamaUrl = "http://127.0.0.1:11434";
// set ollama client and model
$client = new OllamaClient($ollamaUrl);
$models = $client->getModelsList();

if (!in_array($modelName, $models)) {
    $logger->error("Model {$modelName} not found. Available models: " . implode(", ", $models) . "\n");
    exit(1);
}

$client->setModelName($modelName);
$logger->debug("Using model: {$modelName}\n");


do{
    // read from stdin
    $logger->info("Enter prompt (or 'exit' to quit): ");
    $prompt = trim(fgets(STDIN));
    $responseJson = $client->generate($prompt);
    $response = OllamaResponse::fromJson($responseJson);
    
    $logger->info("======================\n");
    $logger->info("Response:\n{$response->response}\n");
}while(!($prompt === "exit" || $prompt === "bye"));