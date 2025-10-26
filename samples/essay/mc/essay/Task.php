<?php

namespace mc\essay;

class Task
{
    private const PROMPT_TEMPLATE = <<<EOT
# Essay Task: {{task_name}}


Please assess the student response based on the below rubric.
Provide scores for each criterion in the table format.

Table Format:

| Criterion | Score |
|-----------|-------|
{{score_formatting}}

Make sure the total score does not exceed {{max_score}} points.

## Task Description

{{task_description}}

## Rubric

{{rubric}}

## Student Response

{{student_response}}
EOT;
    private const RUBRIC = <<<EOT
| Criterion       | Max Score |
|-----------------|-----------|
| Clarity         | 5         |
| Coherence       | 5         |
| Relevance       | 5         |
| Grammar         | 5         |
EOT;

    private string $taskName;
    private string $promptTemplate;
    private string $rubric = self::RUBRIC;
    private string $taskDescription;
    private int $maxScore;

    public function __construct(array $task, string $template)
    {
        $this->taskName = $task['name'] ?? 'A task';
        $this->rubric = $task['rubric'] ?? self::RUBRIC;
        $this->taskDescription = $task['description'] ?? '';
        $this->maxScore = $task['max_score'] ?? 20;
        $this->promptTemplate = $template ?? self::PROMPT_TEMPLATE;
    }

    public function getTaskName(): string
    {
        return $this->taskName;
    }
    public function getRubric(): string
    {
        return $this->rubric;
    }
    public function getTaskDescription(): string
    {
        return $this->taskDescription;
    }
    public function getMaxScore(): int
    {
        return $this->maxScore;
    }

    public function buildPrompt(string $studentEssay): string
    {
        $prompt = $this->promptTemplate;
        $prompt = str_replace('{{task_name}}', $this->taskName, $prompt);
        $prompt = str_replace('{{student_response}}', $studentEssay, $prompt);

        $prompt = str_replace('{{rubric}}', $this->rubric, $prompt);

        $prompt = str_replace('{{task_description}}', $this->taskDescription, $prompt);
        $prompt = str_replace('{{max_score}}', (string)$this->maxScore, $prompt);

        return $prompt;
    }
}
