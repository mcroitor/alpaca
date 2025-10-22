<?php
/**
 * Simple client for Ollama
 */
namespace mc\alpaca;

use mc\alpaca\LLMClient;

class OllamaClient implements LLMClient {
    private string $apiKey;
    private string $apiUrl;
    private string $modelName = "llama3.2:latest";

    public function __construct(string $apiUrl, string $modelName = "llama3.2:latest", string $apiKey = "") {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
        $this->modelName = $modelName;
    }

    public function getApiKey(): string {
        return $this->apiKey;
    }

    public function getApiUrl(): string {
        return $this->apiUrl;
    }

    public function getModelName(): string {
        return $this->modelName;
    }

    public function setModelName(string $modelName): void {
        $this->modelName = $modelName;
    }

    private static function getHttpClient(string $uri, string &$buffer): \mc\http {
        $http = new \mc\http($uri);
        $http->set_encoder("json_encode");
        // $http->set_option(CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        $http->set_write_function(function ($curl, $data) use (&$buffer): int {
            $buffer .= $data;
            return strlen($data);
        });
        return $http;
    }

    public function getModelsList(): array {
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

    public function getModelInfo(string $modelName): array{
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

    public function prompt(string $endpoint, array $data)
    {
        $url = "{$this->apiUrl}/{$endpoint}";
        $buffer = "";
        $http = self::getHttpClient($url, $buffer);
        $response = $http->post(
            $data
        );

        return $buffer;
    }
    public function generate(string $prompt): string
    {
        $data = [
            "model" => $this->modelName,
            "prompt" => $prompt,
            "stream" => false
        ];
        return $this->prompt("api/generate", $data);
    }
}