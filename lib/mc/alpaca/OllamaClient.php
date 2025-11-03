<?php

namespace mc\alpaca;

use mc\alpaca\LLMClient;

/**
 * Ollama LLM Client Implementation
 * 
 * This class provides a concrete implementation of the LLMClient interface
 * for interacting with Ollama language models. It supports model management,
 * text generation, and various configuration options.
 * 
 * Ollama is a tool for running large language models locally, and this client
 * provides a PHP interface to communicate with the Ollama API server.
 * 
 * @package mc\alpaca
 * @author Mihail Croitor <mcroitor@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @example
 * ```php
 * $client = new OllamaClient('http://localhost:11434', 'llama3.2:latest');
 * $response = $client->generate('What is artificial intelligence?');
 * echo $response;
 * ```
 */
class OllamaClient implements LLMClient
{

    /**
     * API key for authentication (currently not used by Ollama)
     * 
     * @var string
     */
    private string $apiKey;

    /**
     * Base URL for the Ollama API server
     * 
     * @var string
     */
    private string $apiUrl;

    /**
     * Name of the language model to use for generation
     * 
     * @var string
     */
    private string $modelName = "llama3.2:latest";

    /**
     * Initialize the Ollama client
     * 
     * @param string $apiUrl The base URL for the Ollama API (e.g., 'http://localhost:11434')
     * @param string $modelName The name of the model to use (default: 'llama3.2:latest')
     * @param string $apiKey Optional API key (not currently used by Ollama, default: empty)
     * 
     * @throws \InvalidArgumentException When apiUrl is invalid
     */
    public function __construct(string $apiUrl, string $modelName = "llama3.2:latest", string $apiKey = "")
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->modelName = $modelName;
    }

    /**
     * Get the API key
     * 
     * @return string The configured API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the API URL
     * 
     * @return string The configured API URL
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Get the current model name
     * 
     * @return string The name of the currently selected model
     */
    public function getModelName(): string
    {
        return $this->modelName;
    }

    /**
     * Set the model name to use for generation
     * 
     * @param string $modelName The name of the model to use
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException When modelName is empty
     */
    public function setModelName(string $modelName): void
    {
        $this->modelName = $modelName;
    }

    /**
     * Create and configure an HTTP client for API requests
     * 
     * This is a factory method that creates a properly configured HTTP client
     * with JSON encoding and a custom write function for handling responses.
     * 
     * @param string $uri The URI for the HTTP request
     * @param string &$buffer Reference to buffer for storing response data
     * 
     * @return \mc\http Configured HTTP client instance
     */
    private static function getHttpClient(string $uri, string &$buffer, bool $stream = false): \mc\http
    {
        $http = new \mc\http($uri);
        $http->set_encoder("json_encode");
        $http->set_write_function(function ($curl, $data) use (&$buffer, $stream): int {
            if ($stream) {
                $object = json_decode($data);
                if ($object->response) {
                    $buffer .= $object->response;
                    echo $object->response;
                    flush();
                }
            } else {
                $buffer .= $data;
            }
            return strlen($data);
        });
        return $http;
    }

    /**
     * Get list of available models from Ollama server
     * 
     * Queries the Ollama API to retrieve a list of all models
     * that are currently available on the server.
     * 
     * @return array Array of model names
     * 
     * @throws \RuntimeException When the API request fails
     */
    public function getModelsList(): array
    {
        $models = [];

        $url = "{$this->apiUrl}/api/tags";
        $buffer = "";
        $http = self::getHttpClient($url, $buffer);

        $response = $http->get();
        $data = json_decode($buffer, true);

        foreach ($data['models'] as $model) {
            $models[] = $model['name'];
        }
        return $models;
    }

    /**
     * Get detailed information about a specific model
     * 
     * Retrieves comprehensive information about a model including
     * its parameters, template, and other metadata.
     * 
     * @param string $modelName The name of the model to query
     * 
     * @return array Associative array containing model information
     * 
     * @throws \RuntimeException When the API request fails
     * @throws \InvalidArgumentException When modelName is empty
     */
    public function getModelInfo(string $modelName): array
    {
        $url = "{$this->apiUrl}/api/show";
        $buffer = "";
        $http = self::getHttpClient($url, $buffer);
        $response = $http->post(
            ["model" => $modelName],
            [CURLOPT_HTTPHEADER => ['Content-Type:application/json']]
        );
        if ($response) {
            $data = json_decode($buffer, true);
            return $data;
        }
        return [];
    }

    /**
     * Send a generic prompt request to a specific Ollama endpoint
     * 
     * This is a low-level method for sending custom requests to
     * different Ollama API endpoints with arbitrary data.
     * 
     * @param string $endpoint The API endpoint to call (without base URL)
     * @param array $data The data to send in the request body
     * 
     * @return string Raw response from the API
     * 
     * @throws \RuntimeException When the API request fails
     */
    public function prompt(string $endpoint, array $data)
    {
        $url = "{$this->apiUrl}/{$endpoint}";
        $buffer = "";
        $stream = $data['stream'] ?? false;
        $http = self::getHttpClient($url, $buffer, $stream);
        $response = $http->post(
            $data
        );

        return $buffer;
    }

    /**
     * Generate text response from the language model
     * 
     * Sends a prompt to the configured model and returns the generated response.
     * This is the main method for text generation.
     * 
     * @param string $prompt The input prompt to send to the model
     * @param array $options Optional parameters to customize generation
     *                      (e.g., temperature, max_tokens, etc.)
     * 
     * @return string Raw JSON response from the Ollama API
     * 
     * @throws \RuntimeException When the API request fails
     * @throws \InvalidArgumentException When prompt is empty
     * 
     * @example
     * ```php
     * $response = $client->generate('Explain quantum computing', [
     *     'temperature' => 0.7,
     *     'max_tokens' => 100
     * ]);
     * ```
     */
    public function generate(string $prompt, array $options = []): string
    {
        $data = [
            "model" => $this->modelName,
            "prompt" => $prompt,
            "stream" => false
        ];
        foreach ($options as $key => $value) {
            $data[$key] = $value;
        }
        return $this->prompt("api/generate", $data);
    }
}
