# Essay Evaluation Tool

An automated student work assessment system using language models via Ollama. The system allows evaluating essays, programming assignments, and other text-based work based on predefined criteria.

## Description

This tool uses large language models (LLM) for automatic assessment of student work. The system analyzes student responses, compares them with evaluation criteria, and assigns scores for each criterion.

## Project Structure

```text
samples/essay/
├── config.json              # Main configuration
├── eval_essay.php           # Main evaluation script
├── readme.md               # This documentation
├── mc/                     # Classes for working with assignments
│   └── essay/
│       ├── Assessor.php    # Assessment class
│       └── Task.php        # Task class
├── templates/              # Prompt templates
│   └── prompt.template     # Template for LLM
└── data/                   # Assignment data
```

## Requirements

- **PHP 7.4+** with JSON and cURL support
- **Ollama server** running locally or remotely
- **Installed models** in Ollama (e.g., llama2, gemma, cogito)

## Installation and Setup

### 1. Ollama Installation

```bash
# Download and install Ollama from the official website
# https://ollama.ai

# Start Ollama server
ollama serve

# Download necessary models
ollama pull llama2
ollama pull gemma:7b
```

### 2. Configuration Setup

Edit the `config.json` file:

```json
{
    "models": ["llama2", "gemma:7b"],
    "task_name": "Assignment Name",
    "task_definition_file": "data/essay2/task.md",
    "rubric_file": "data/essay2/rubric.md",
    "input_directory": "data/essay2/input",
    "output_directory": "data/essay2/output",
    "ollama_server": "http://127.0.0.1:11434"
}
```

### 3. Data Preparation

1. **Create assignment file** (`task.md`) with assignment description and evaluation criteria
2. **Add student work** to the `input/` folder with `.essay` extension
3. **Ensure** that the `output/` folder exists for saving results

## Usage

### Run with default configuration

```bash
php eval_essay.php
```

### Run with command line parameters

```bash
# Basic parameters
php eval_essay.php --config custom_config.json
php eval_essay.php --input ./my_essays --output ./results
php eval_essay.php --models "llama2,gemma:7b"

# Full list of parameters
php eval_essay.php --help
```

### Available Parameters

| Parameter | Short Form | Description |
|-----------|------------|-------------|
| `--help` | `-h` | Show help |
| `--config FILE` | `-c FILE` | Path to configuration file |
| `--input DIR` | `-i DIR` | Input files folder |
| `--output DIR` | `-o DIR` | Results folder |
| `--rubric FILE` | `-r FILE` | Evaluation criteria file |
| `--taskdef FILE` | `-t FILE` | Task definition file |
| `--models LIST` | `-m LIST` | Comma-separated list of models |

## Input File Formats

### Assignment File (task.md)

```markdown
# Assignment Title

## Task Definition
Assignment description for students...

## Solution sample
Example of correct solution...

## Evaluation
Evaluation criteria in rubric format...
```

### Student Work (.essay)

Simple text files with student responses.

## Assignment Examples

### Essay1 - Docker Compose

Assignment to create a `docker-compose.yml` file with evaluation across 18 criteria.

### Essay2 - Dockerfile

Assignment to create a `Dockerfile` with detailed quality-level rubric.

## Template Configuration

The `templates/prompt.template` file contains the prompt template for the language model:

```plaintext
# Essay Task: {{task_name}}

Assess the Student Response based on the below rubric...

## Task Description
{{task_description}}

## Evaluation rubric  
{{rubric}}

## Student Response
{{student_response}}
```

Available variables:

- `{{task_name}}` - assignment name
- `{{task_description}}` - assignment description
- `{{rubric}}` - evaluation criteria
- `{{student_response}}` - student response
- `{{max_score}}` - maximum score

## Architecture

### Main Classes

- **`\mc\essay\Task`** - represents an assignment with evaluation criteria
- **`\mc\essay\Assessor`** - performs evaluation using LLM

### Evaluation Process

1. Load configuration and command line parameters
2. Connect to Ollama server and get list of models
3. Load assignment definition and evaluation criteria
4. Read student work from input folder
5. For each model and each work:
   - Generate prompt based on template
   - Send request to language model
   - Save result to output folder

## Best Practices

### Creating Assignments

- Use clear and specific evaluation criteria
- Include examples of correct solutions
- Define quality levels for each criterion

### Model Selection

- Test different models for your type of assignments
- Larger models usually provide more accurate evaluations
- Use multiple models to compare results

### Results Validation

- Check evaluation results on a sample of work
- Compare with teacher evaluations
- Adjust prompts and criteria as needed

## Troubleshooting

### Common Problems

**Ollama connection error:**

```bash
Error: Cannot connect to Ollama server at 'http://127.0.0.1:11434'
```

- Make sure Ollama server is running
- Check server URL in configuration

**Model not found:**

```bash
Error: No valid models available
```

- Download models: `ollama pull model_name`
- Check model name spelling

**Files not found:**

- Check file paths in configuration
- Make sure assignment files and student work exist

## Extending Functionality

The system is easily extensible for new types of assignments:

1. Create a new folder in `data/`
2. Add `task.md` file with assignment description
3. Create `input/` and `output/` folders
4. Update configuration
5. Modify prompt template if necessary

## License

This project is part of Alpaca PHP Client and is distributed under the same license.
