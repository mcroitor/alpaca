<?php

include_once __DIR__ . "/mc/essay/Task.php";
include_once __DIR__ . "/mc/essay/Assessor.php";
include_once __DIR__ . "/../../lib/autoload.php";

/**
 * Loads essay responses from .essay files in a folder.
 * @param string $folderPath Path to folder containing .essay files
 * @return array Array of essays keyed by filename
 */
function loadEssayResponses($folderPath) {
    $responses = [];
    $files = glob($folderPath . "/*.essay");
    foreach ($files as $file) {
        $key = basename($file, ".essay");
        $responses[$key] = file_get_contents($file);
    }
    return $responses;
}

use mc\alpaca\OllamaClient;

$config = json_decode(file_get_contents(__DIR__ . "/config.json"), true);

$logger = \mc\Logger::stdout();

// ollama server URL
$ollamaUrl = $config['ollama_server'] ?? "http://127.0.0.1:11434";

// set ollama client and model
$client = new OllamaClient($ollamaUrl);
$availableModels = $client->getModelsList();
$models = $config['models'] ?? $availableModels;
$models = array_intersect($models, $availableModels);
$logger->info("Using models: " . implode(", ", $models));

// output folder
$output_folder = isset($config['output_directory']) ? __DIR__ . "/" . $config['output_directory'] : __DIR__ . "/data/output";
if (!is_dir($output_folder)) {
    mkdir($output_folder, 0777, true);
}

// load grading scale / rubric
if (isset($config['rubric_file'])) {
    $rubricContent = file_get_contents(__DIR__ . "/" . $config['rubric_file']);
} else {
    $rubricContent = [];
}

// task definition
$taskDefinitionFile = isset($config['task_definition_file']) ? __DIR__ . "/" . $config['task_definition_file'] : __DIR__ . "/data/taskdef.md";
$taskDefinitionContent = file_get_contents($taskDefinitionFile);

// define the essay task
$essayTask = new \mc\essay\Task(
    [
        "task_name" => $config['task_name'] ?? "A task",
        "task_description" => $taskDefinitionContent,
        "rubric" => $rubricContent,
        "max_score" => 100
    ],
    file_get_contents(__DIR__ . "/templates/prompt.template")
);

$logger->info("Loading essay responses...");
$responses = loadEssayResponses(__DIR__ . "/data/input");
$logger->info("Loaded " . count($responses) . " essay responses.");

$logger->info("Starting essay assessments...");
foreach ($models as $model) {
    $logger->info("Assessing with model: {$model}");
    $client->setModelName($model);

    // create the essay assessor
    $assessor = new \mc\essay\Assessor($client);

    // assess the essay
    foreach ($responses as $key => $response) {
        $score = $assessor->assessEssay($essayTask, $response);
        $logger->info("Student {$key}");
        $logger->info("----------------------");
        $logger->info("Assessed with: {$score}");
        $model_name = str_replace([":", ".", "/"], "_", $model);
        file_put_contents("{$output_folder}/essay_{$key}_{$model_name}.md", $score);
    }
    $logger->info("======================");
}
$logger->info("Essay assessments completed.");