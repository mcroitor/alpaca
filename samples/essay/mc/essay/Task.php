<?php

namespace mc\essay;

/**
 * Essay Task Management Class
 * 
 * This class represents an educational assignment task and handles
 * the generation of assessment prompts for language models. It manages
 * task metadata, rubrics, and template processing for automated essay evaluation.
 * 
 * The Task class uses a template system to generate structured prompts
 * that include task descriptions, evaluation rubrics, and student responses
 * for consistent LLM-based assessment.
 * 
 * @package mc\essay
 * @author Mihail Croitor <mcroitor@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @example
 * ```php
 * $taskData = [
 *     'task_name' => 'Docker Assignment',
 *     'task_description' => 'Create a docker-compose.yml file',
 *     'rubric' => '| Criterion | Max Score |\n|-----------|-----------|',
 *     'max_score' => 100
 * ];
 * $task = new Task($taskData, $customTemplate);
 * $prompt = $task->buildPrompt($studentEssay);
 * ```
 */
class Task
{
    /**
     * Default prompt template for essay assessment
     * 
     * This template defines the structure of the assessment prompt
     * sent to the language model. It includes placeholders for:
     * - {{task_name}}: Name of the assignment
     * - {{task_description}}: Detailed task description
     * - {{rubric}}: Evaluation criteria and scoring
     * - {{student_response}}: Student's submission
     * - {{max_score}}: Maximum possible score
     * - {{score_formatting}}: Score table format
     * 
     * @var string
     */
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

    /**
     * Default evaluation rubric for generic essay assessment
     * 
     * This provides a basic 4-criteria rubric for general essay evaluation.
     * Can be overridden with custom rubrics specific to assignment types.
     * 
     * @var string
     */
    private const RUBRIC = <<<EOT
| Criterion       | Max Score |
|-----------------|-----------|
| Clarity         | 5         |
| Coherence       | 5         |
| Relevance       | 5         |
| Grammar         | 5         |
EOT;

    /**
     * Name of the assignment task
     * 
     * @var string
     */
    private string $taskName;
    
    /**
     * Template string for generating assessment prompts
     * 
     * @var string
     */
    private string $promptTemplate;
    
    /**
     * Evaluation rubric in markdown table format
     * 
     * @var string
     */
    private string $rubric = self::RUBRIC;
    
    /**
     * Detailed description of the task requirements
     * 
     * @var string
     */
    private string $taskDescription;
    
    /**
     * Maximum possible score for the assignment
     * 
     * @var int
     */
    private int $maxScore;

    /**
     * Initialize a new Task instance
     * 
     * Creates a task object with configuration data and optional custom template.
     * If task data is incomplete, sensible defaults are used.
     * 
     * @param array $task Associative array containing task configuration:
     *                   - 'task_name' (string): Name of the assignment
     *                   - 'task_description' (string): Task description
     *                   - 'rubric' (string): Evaluation rubric in markdown
     *                   - 'max_score' (int): Maximum score
     * @param string $template Optional custom prompt template
     * 
     * @throws \InvalidArgumentException When task array is malformed
     */
    public function __construct(array $task, string $template)
    {
        $this->taskName = $task['task_name'] ?? 'A task';
        $this->rubric = $task['rubric'] ?? self::RUBRIC;
        $this->taskDescription = $task['task_description'] ?? '';
        $this->maxScore = $task['max_score'] ?? 100;
        $this->promptTemplate = !empty($template) ? $template : self::PROMPT_TEMPLATE;
    }

    /**
     * Get the task name
     * 
     * @return string The configured task name
     */
    public function getTaskName(): string
    {
        return $this->taskName;
    }
    
    /**
     * Get the evaluation rubric
     * 
     * @return string The rubric in markdown table format
     */
    public function getRubric(): string
    {
        return $this->rubric;
    }
    
    /**
     * Get the task description
     * 
     * @return string The detailed task description
     */
    public function getTaskDescription(): string
    {
        return $this->taskDescription;
    }
    
    /**
     * Get the maximum score
     * 
     * @return int The maximum possible score for this task
     */
    public function getMaxScore(): int
    {
        return $this->maxScore;
    }

    /**
     * Build assessment prompt for language model
     * 
     * This method processes the prompt template by replacing all placeholder
     * variables with actual task data and student submission. The resulting
     * prompt is ready to be sent to a language model for assessment.
     * 
     * Template variables that are replaced:
     * - {{task_name}}: Name of the assignment
     * - {{student_response}}: The student's essay/submission
     * - {{rubric}}: Evaluation criteria table
     * - {{task_description}}: Detailed task requirements
     * - {{max_score}}: Maximum possible score
     * - {{score_formatting}}: Example score table format
     * 
     * @param string $studentEssay The student's submission to be evaluated
     * 
     * @return string Complete assessment prompt ready for LLM
     * 
     * @throws \InvalidArgumentException When studentEssay is empty
     * 
     * @example
     * ```php
     * $prompt = $task->buildPrompt("Student's essay content here...");
     * // Returns formatted prompt with all variables replaced
     * ```
     */
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
