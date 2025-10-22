<?php

include_once __DIR__ . "/mc/essay/Task.php";
include_once __DIR__ . "/mc/essay/Assessor.php";
include_once __DIR__ . "/../../lib/autoload.php";

use mc\alpaca\OllamaClient;

// ollama server URL
$ollamaUrl = "http://127.0.0.1:11434";

// set ollama client and model
$client = new OllamaClient($ollamaUrl);
$models = $client->getModelsList();
$selectedModel = $models[1];
$client->setModelName($selectedModel);

// define the essay task
$essayTask = new \mc\essay\Task(
    "Definirea docker-compose.yml",
    [
        "Correct and complete task definition" => 100,
        "For each typo error" => -3,
        "For each missing element" => -10
    ],
    file_get_contents(__DIR__ . "/data/taskdef.md"),
    100
);

// create the essay assessor
$assessor = new \mc\essay\Assessor($client);

echo "[debug] Assessor model: {$selectedModel}\n";

$responses = [
    "student1" => file_get_contents(__DIR__ . "/data/student1.essay"),
    "student2" => file_get_contents(__DIR__ . "/data/student2.essay"),
];

// assess the essay
foreach ($responses as $key => $response) {
    $score = $assessor->assessEssay($essayTask, $response);
    echo "Essay {$key}, assessed with: {$score}\n";
}