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
     * Name of the assignment task
     * 
     * @var string
     */
    private string $taskName;
    
    /**
     * Evaluation rubric in markdown table format
     * 
     * @var string
     */
    private string $rubric;
    
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
     * Evaluation guidelines for assessors
     * 
     * @var string
     */
    private string $evaluationGuidelines;

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
     *                   - 'evaluation_guidelines' (string): Guidelines for evaluators
     * 
     * @throws \InvalidArgumentException When task array is malformed
     */
    public function __construct(array $task)
    {
        $this->taskName = $task['task_name'] ?? 'A task';
        $this->taskDescription = $task['task_description'] ?? '';
        $this->rubric = $task['rubric'] ?? '';
        $this->maxScore = $task['max_score'] ?? 100;
        $this->evaluationGuidelines = $task['evaluation_guidelines'] ?? 'Follow the rubric strictly and provide constructive feedback.';
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
     * Get evaluation guidelines
     */
    public function getEvaluationGuidelines(): string
    {
        return $this->evaluationGuidelines;
    }

    /**
     * Build assessment prompt for language model
     * 
     * This method processes the prompt template by replacing all placeholder
     * variables with actual task data and student submission. The resulting
     * prompt is ready to be sent to a language model for assessment.
     * 
     * @param string $studentEssay The student's submission to be evaluated
     * @param string $template The prompt template with placeholders
     * 
     * @return string Complete assessment prompt ready for LLM
     * 
     * @throws \InvalidArgumentException When studentEssay is empty
     */
    public function buildPrompt(string $studentEssay, string $template): string
    {
        $prompt = $template;

        $from = [
            "{{task_name}}",
            "{{student_response}}",
            "{{rubric}}",
            "{{task_description}}",
            "{{max_score}}"
        ];
        $to = [
            $this->taskName,
            $studentEssay,
            $this->rubric,
            $this->taskDescription,
            (string)$this->maxScore
        ];
        $prompt = str_replace($from, $to, $prompt);

        return $prompt;
    }
}
