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
        $this->taskName = $task['task_name'] ?? 'A task';
        $this->rubric = $task['rubric'] ?? self::RUBRIC;
        $this->taskDescription = $task['task_description'] ?? '';
        $this->maxScore = $task['max_score'] ?? 100;
        $this->promptTemplate = !empty($template) ? $template : self::PROMPT_TEMPLATE;
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

        $from = [
            "{{task_name}}",
            "{{student_response}}",
            "{{rubric}}",
            "{{task_description}}",
            "{{max_score}}",
            "{{score_formatting}}"
        ];
        $to = [
            $this->taskName,
            $studentEssay,
            $this->rubric,
            $this->taskDescription,
            (string)$this->maxScore,
            "| criterion |       |"
        ];
        $prompt = str_replace($from, $to, $prompt);

        return $prompt;
    }
}
