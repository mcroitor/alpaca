<?php

include_once __DIR__ . "/mc/essay/Task.php";
include_once __DIR__ . "/mc/essay/Assessor.php";
include_once __DIR__ . "/../../lib/autoload.php";


use mc\alpaca\OllamaClient;
use \mc\essay\Task;
use \mc\essay\Assessor;

/**
 * Prints usage information for the script.
 */
function printUsage(string $scriptName): void {
    echo "Usage: php {$scriptName} [options]\n";
    echo "Script to evaluate essay responses using language models via Ollama.\n";
    echo "Script options allows to override config settings.\n\n";
    echo "Options:\n";
    echo "  -h, --help           Show this help message\n";
    echo "  -c, --config FILE    Path to config file (default: config.json)\n";
    echo "  -s, --server URL     Ollama server URL\n";
    echo "  -i, --input DIR      Path to input directory\n";
    echo "  -o, --output DIR     Path to output directory\n";
    echo "  -r, --rubric FILE    Path to rubric file\n";
    echo "  -t, --taskdef FILE   Path to task definition file\n";
    echo "  -m, --models LIST    Comma-separated list of models to use\n";
}

/**
 * Checks if a path is absolute.
 * @param string $path Path to check
 * @return bool True if path is absolute, false otherwise
 */
function is_absolute_path(string $path): bool {
    return (DIRECTORY_SEPARATOR === '\\') 
        ? (preg_match('/^[a-zA-Z]:/', $path) || substr($path, 0, 2) === '\\\\')
        : (substr($path, 0, 1) === '/');
}

/**
 * Loads essay responses from .essay files in a folder.
 * @param string $folderPath Path to folder containing .essay files
 * @return array Array of essays keyed by filename
 */
function loadEssayResponses(string $folderPath): array {
    $responses = [];
    $files = glob("{$folderPath}/*.essay");
    foreach ($files as $file) {
        $key = basename($file, ".essay");
        $responses[$key] = file_get_contents($file);
    }
    return $responses;
}

function loadTask(string $configFile): Task {
    $taskConfig = json_decode(file_get_contents($configFile), true);
    return new Task(
        [
            "task_name" => $taskConfig['task_name'] ?? "A task",
            "task_description" => file_get_contents($taskConfig['task_file']),
            "rubric" => file_get_contents($taskConfig['rubric_file']),
        ]
    );
}


$logger = \mc\Logger::stdout();

// Parse command line arguments using getopt
$shortopts = "hc:s:i:o:r:t:m:";
$longopts = [
    "help",
    "config:",
    "server:",
    "input:",
    "output:", 
    "rubric:",
    "taskdef:",
    "models:"
];

$options = getopt($shortopts, $longopts);

// Handle help option
if (isset($options['h']) || isset($options['help'])) {
    printUsage(basename($argv[0]));
    exit(0);
}

// Parse configuration overrides from command line
$cliConfig = [];
if (isset($options['c']) || isset($options['config'])) {
    $cliConfig['config_file'] = $options['config'] ?? $options['c'];
}
if (isset($options['s']) || isset($options['server'])) {
    $cliConfig['ollama_server'] = $options['server'] ?? $options['s'];
}
if (isset($options['i']) || isset($options['input'])) {
    $cliConfig['input_directory'] = $options['input'] ?? $options['i'];
}
if (isset($options['o']) || isset($options['output'])) {
    $cliConfig['output_directory'] = $options['output'] ?? $options['o'];
}
if (isset($options['r']) || isset($options['rubric'])) {
    $cliConfig['rubric_file'] = $options['rubric'] ?? $options['r'];
}
if (isset($options['t']) || isset($options['taskdef'])) {
    $cliConfig['task_definition_file'] = $options['taskdef'] ?? $options['t'];
}
if (isset($options['m']) || isset($options['models'])) {
    $modelsString = $options['models'] ?? $options['m'];
    $cliConfig['models'] = array_map('trim', explode(',', $modelsString));
}

// Load configuration file
$configFile = $cliConfig['config_file'] ?? __DIR__ . "/config.json";
$config = [];

if (file_exists($configFile)) {
    $configData = json_decode(file_get_contents($configFile), true);
    if ($configData === null) {
        $logger->error("Invalid JSON in configuration file '{$configFile}'");
        exit(1);
    }
    $config = $configData;
} elseif (isset($cliConfig['config_file'])) {
    // Only show error if user explicitly specified a config file that doesn't exist
    $logger->error("Configuration file '{$configFile}' not found");
    exit(1);
}

// Merge CLI arguments with config file (CLI arguments override config file)
$config = array_merge($config, $cliConfig);

// ollama server URL
$ollamaUrl = $config['ollama_server'] ?? "http://127.0.0.1:11434";

// set ollama client and model
$client = new OllamaClient($ollamaUrl);

try {
    $availableModels = $client->getModelsList();
} catch (Exception $e) {
    $logger->error("Cannot connect to Ollama server at '{$ollamaUrl}': " . $e->getMessage());
    exit(1);
}

$models = $config['models'] ?? $availableModels;
$models = array_intersect($models, $availableModels);

if (empty($models)) {
    $logger->error("No valid models available. Available models: " . implode(", ", $availableModels));
    exit(1);
}

$logger->info("Using models: " . implode(", ", $models));

// output folder
$output_folder = isset($config['output_directory']) ? 
    (is_absolute_path($config['output_directory']) ? $config['output_directory'] : __DIR__ . "/" . $config['output_directory']) : 
    __DIR__ . "/data/output";
if (!is_dir($output_folder)) {
    mkdir($output_folder, 0777, true);
}
$logger->info("Output directory: {$output_folder}");

// load task definition
if (!file_exists($config['task_config'])) {
    $logger->error("Task config file not found: " . $config['task_config']);
    exit(1);
}
$taskConfigContent = file_get_contents($config['task_config']);
if ($taskConfigContent === false) {
    $logger->error("Failed to read task config file: " . $config['task_config']);
    exit(1);
}
$taskConfig = json_decode($taskConfigContent, true);
if ($taskConfig === null && json_last_error() !== JSON_ERROR_NONE) {
    $logger->error("Invalid JSON in task config file: " . $config['task_config'] . ". Error: " . json_last_error_msg());
    exit(1);
}

// define the essay task
$essayTask = new \mc\essay\Task(
    [
        "task_name" => $config['task_name'] ?? "A task",
        "task_description" => $taskConfig['task_description'] ?? "",
        "rubric" => $taskConfig['rubric'] ?? "",
        "max_score" => 100
    ]);

$template = file_get_contents(__DIR__ . "/templates/prompt.template");

$logger->info("Essay task prompt:");
// $logger->info($essayTask->buildPrompt(""));

$logger->info("Loading essay responses...");
$input_folder = isset($config['input_directory']) ? 
    (is_absolute_path($config['input_directory']) ? $config['input_directory'] : __DIR__ . "/" . $config['input_directory']) : 
    __DIR__ . "/data/input";

if (!is_dir($input_folder)) {
    $logger->error("Input directory '{$input_folder}' not found");
    exit(1);
}
$logger->info("Input directory: {$input_folder}");

$responses = loadEssayResponses($input_folder);
$logger->info("Loaded " . count($responses) . " essay responses.");

$logger->info("Starting essay assessments...");
foreach ($models as $model) {
    $logger->info("Assessing with model: {$model}");
    $client->setModelName($model);

    // create the essay assessor
    $assessor = new \mc\essay\Assessor($client, $template);
    $model_name = str_replace([":", ".", "/"], "_", $model);
    if (!is_dir("{$output_folder}/{$model_name}")) {
        mkdir("{$output_folder}/{$model_name}", 0777, true);
    }

    // assess the essay
    foreach ($responses as $key => $response) {
        if (!is_dir("{$output_folder}/{$model_name}/{$key}")) {
            mkdir("{$output_folder}/{$model_name}/{$key}", 0777, true);
        }
        // count essay files in student folder
        $essayFiles = glob("{$output_folder}/{$model_name}/{$key}/essay_eval_*.md");
        $essayCount = count($essayFiles);
        $id = $essayCount + 1;

        $logger->info("Student {$key}");
        $logger->info("----------------------");
        $logger->info("Assessed with:");

        $score = $assessor->assessEssay($essayTask, $response);
        // create student folder if not exists
        file_put_contents("{$output_folder}/{$model_name}/{$key}/essay_eval_{$id}.md", $score);
    }
    $logger->info("======================");
}
$logger->info("Essay assessments completed.");
