<?php

include_once __DIR__ . "/mc/essay/Task.php";
include_once __DIR__ . "/mc/essay/Assessor.php";
include_once __DIR__ . "/../../lib/autoload.php";

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

use mc\alpaca\OllamaClient;

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
} else if (isset($cliConfig['config_file'])) {
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

// load grading scale / rubric
if (isset($config['rubric_file'])) {
    $rubricFile = is_absolute_path($config['rubric_file']) ? $config['rubric_file'] : __DIR__ . "/" . $config['rubric_file'];
    if (file_exists($rubricFile)) {
        $rubricContent = file_get_contents($rubricFile);
        $logger->info("Using rubric file: {$rubricFile}");
    } else {
        $logger->warn("Rubric file '{$rubricFile}' not found");
        $rubricContent = "";
    }
} else {
    $rubricContent = "";
    $logger->info("No rubric file specified");
}

// task definition
$taskDefinitionFile = "";
if (isset($config['task_definition_file'])) {
    $taskDefinitionFile = is_absolute_path($config['task_definition_file']) ?
        $config['task_definition_file'] :
        __DIR__ . "/" . $config['task_definition_file'];
} else {
    $logger->error("No task definition file specified in configuration");
    exit(1);
}

if (!file_exists($taskDefinitionFile)) {
    $logger->error("Task definition file '{$taskDefinitionFile}' not found");
    exit(1);
}
$taskDefinitionContent = file_get_contents($taskDefinitionFile);
$logger->info("Using task definition file: {$taskDefinitionFile}");

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

$logger->info("Essay task prompt:");
$logger->info($essayTask->buildPrompt(""));

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