<?php

namespace mc\essay;

use mc\alpaca\OllamaClient;
use \mc\alpaca\OllamaResponse;

class Assessor
{
    private OllamaClient $llmClient;

    public function __construct(OllamaClient $llmClient)
    {
        $this->llmClient = $llmClient;
    }

    public function assessEssay(\mc\essay\Task $task, string $studentEssay): string
    {
        $prompt = $task->buildPrompt($studentEssay);
        $response = $this->llmClient->generate($prompt);

        $responseObj = OllamaResponse::fromJson($response);
        return $responseObj->response;
    }
}
