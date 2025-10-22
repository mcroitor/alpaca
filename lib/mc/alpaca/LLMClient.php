<?php

namespace mc\alpaca;

interface LLMClient {
    // LLMClient implementation
    public function __construct(string $apiUrl, string $apiKey = "");
    public function getApiKey(): string;
    public function getApiUrl(): string;
    public function generate(string $prompt): string;
}