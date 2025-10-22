<?php

namespace mc\alpaca;

class OllamaResponse {
    public string $model;
    public string $created_at;
    public string $response;
    public bool $done;
    public array $context;
    public int $total_duration;
    public int $load_duration;
    public int $prompt_eval_count;
    public int $prompt_eval_duration;
    public int $eval_count;
    public int $eval_duration;

    public static function fromJson(string $json): OllamaResponse {
        $data = json_decode($json, true);
        $response = new OllamaResponse();
        $response->model = $data['model'] ?? '';
        $response->created_at = $data['created_at'] ?? '';
        $response->response = $data['response'] ?? '';
        $response->done = $data['done'] ?? false;
        $response->context = $data['context'] ?? [];
        $response->total_duration = $data['total_duration'] ?? 0;
        $response->load_duration = $data['load_duration'] ?? 0;
        $response->prompt_eval_count = $data['prompt_eval_count'] ?? 0;
        $response->prompt_eval_duration = $data['prompt_eval_duration'] ?? 0;
        $response->eval_count = $data['eval_count'] ?? 0;
        $response->eval_duration = $data['eval_duration'] ?? 0;
        return $response;
    }
}