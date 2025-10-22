<?php

namespace mc\essay;

class Task {
    private string $taskName;
    private array $rubric = [];
    private string $taskDescription;
    private int $maxScore;

    public function __construct(string $taskName, array $rubric, string $taskDescription, int $maxScore) {
        $this->taskName = $taskName;
        $this->rubric = $rubric;
        $this->taskDescription = $taskDescription;
        $this->maxScore = $maxScore;
    }

    public function getTaskName(): string {
        return $this->taskName;
    }
    public function getRubric(): array {
        return $this->rubric;
    }
    public function getTaskDescription(): string {
        return $this->taskDescription;
    }
    public function getMaxScore(): int {
        return $this->maxScore;
    }

    public function buildPrompt(string $studentEssay): string {
        $prompt = "Essay Task: {$this->taskName}\n";
        $prompt .= "Description: {$this->taskDescription}\n\n";
        $prompt .= "Rubric:\n";
        foreach ($this->rubric as $criterion => $weight) {
            $prompt .= "- {$criterion}: {$weight} points\n";
        }
        $prompt .= "\nStudent Essay:\n{$studentEssay}\n\n";
        $prompt .= "Please assess the essay based on the above rubric and provide scores for each criterion along with feedback.\n\n";
        $prompt .= "Provide also a total score out of {$this->maxScore}.";
        return $prompt;
    }

    public function parseAssessment(string $response): array {
        $data = json_decode($response, true);
        $scores = $data['scores'] ?? [];
        $feedback = $data['feedback'] ?? "";
        return [
            "scores" => $scores,
            "feedback" => $feedback
        ];
    }
}