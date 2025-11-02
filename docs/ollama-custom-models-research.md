# Ollama Custom Models Research

## Research Report for feat/003a

**Date:** October 29, 2025  
**Author:** Mihail Croitor <mcroitor@gmail.com>  
**Status:** In Progress

## 1. Ollama API Endpoints Investigation

### Available Endpoints

#### 1.1 Model Management Endpoints

```http
GET /api/tags
```

- **Purpose:** List all available models
- **Response:** JSON with models array containing name, model, modified_at, size, etc.

```http
POST /api/show
```

- **Purpose:** Get detailed information about a specific model
- **Body:** `{"model": "model_name"}`
- **Response:** Model details including parameters, template, system prompt

```http
POST /api/create
```

- **Purpose:** Create a new model from a Modelfile
- **Body:** `{"name": "new_model", "modelfile": "content"}`
- **Response:** Stream of creation progress

```http
DELETE /api/delete
```

- **Purpose:** Delete a model
- **Body:** `{"model": "model_name"}`

#### 1.2 Generation Endpoints

```http
POST /api/generate
```

- **Purpose:** Generate text with a model
- **Body:** `{"model": "model_name", "prompt": "text", "stream": false}`

```http
POST /api/chat
```

- **Purpose:** Chat interface with conversation history

```http
POST /api/generate
```

- **Purpose:** Generate text with a model
- **Body:** `{"model": "model_name", "prompt": "text", "stream": false}`

```http
POST /api/chat
```

- **Purpose:** Chat interface with conversation history
- **Body:** `{"model": "model_name", "messages": [...]}`

### API Testing Results

Current installation has models:

- cogito:14b (and others - need to get full list)

## 2. Modelfile Format Research

### 2.1 Modelfile Commands

The Modelfile is a text file that defines how to create a custom model. Key commands:

```dockerfile
FROM <base_model>
```

- Specifies the base model to build from
- Can be an existing model name or path to GGUF file

```dockerfile
SYSTEM <system_prompt>
```

- Sets the system prompt that guides model behavior
- Critical for creating domain-specific models

```dockerfile
TEMPLATE <template>
```

- Defines the prompt template format
- Controls how user input is formatted before sending to model

```dockerfile
PARAMETER <parameter> <value>
```

- Sets model parameters like temperature, top_p, etc.
- Examples: `PARAMETER temperature 0.7`

```dockerfile
ADAPTER <path>
```

- Load LoRA adapters (if supported)

### 2.2 Example Modelfile

```dockerfile
FROM llama3.2:latest

SYSTEM """
You are an expert essay evaluator with deep knowledge in education. 
You provide detailed, constructive feedback on student essays based on specific rubrics.
Always be fair, encouraging, and provide actionable suggestions for improvement.
"""

TEMPLATE """
### Instruction:
{{ .System }}

### Task:
{{ .Prompt }}

### Response:
"""

PARAMETER temperature 0.3
PARAMETER top_p 0.9
PARAMETER stop "###"
```

## 3. Custom Model Creation Capabilities

### 3.1 Supported Approaches

1. **System Prompt Customization**
   - ✅ Fully supported via SYSTEM command
   - Most practical for domain-specific tasks
   - No additional training required

2. **Template Customization**
   - ✅ Supported via TEMPLATE command
   - Controls input/output formatting
   - Good for specific interaction patterns

3. **Parameter Tuning**
   - ✅ Supported via PARAMETER command
   - Adjust temperature, top_p, repeat_penalty, etc.
   - Fine-tune model behavior without training

4. **Base Model Selection**
   - ✅ Supported via FROM command
   - Can use any available model as base
   - Allows model size/capability selection

### 3.2 Limitations Discovered

1. **No Built-in Fine-tuning**
   - ❌ Ollama doesn't support training on custom datasets
   - ❌ No gradient updates or weight modifications
   - ❌ Cannot create truly "trained" models

2. **LoRA Adapter Support**
   - ⚠️ ADAPTER command exists but needs verification
   - May require external training pipeline
   - Limited documentation available

3. **Context Window**
   - ⚠️ Limited by base model's context window
   - Cannot extend context length significantly
   - Important for RAG applications

## 4. Fine-tuning and Training Options

### 4.1 Direct Fine-tuning

- **Status:** ❌ Not supported in Ollama
- **Alternative:** Use external tools (Hugging Face, etc.) then convert to GGUF

### 4.2 LoRA Adapters

- **Status:** ⚠️ Partially supported
- **Requires:** External training pipeline
- **Process:** Train LoRA → Convert → Load with ADAPTER command

### 4.3 RAG (Retrieval-Augmented Generation)

- **Status:** ✅ Fully feasible
- **Implementation:** Enhance prompts with retrieved context
- **Advantage:** No model modification required

### 4.4 Few-Shot Learning

- **Status:** ✅ Fully supported
- **Implementation:** Include examples in system prompt or templates
- **Advantage:** Immediate improvement without training

## 5. Implementation Recommendations

### 5.1 Immediate Possibilities (High Priority)

1. **Custom System Prompts**

   ```php
   // Easy to implement, high impact
   $modelfile = "FROM llama3.2:latest\nSYSTEM $customPrompt";
   $client->createModel('essay-evaluator', $modelfile);
   ```

2. **Template Customization**

   ```php
   // Standardize input/output format
   $template = "### Essay: {{.Prompt}}\n### Evaluation:\n";
   ```

3. **Parameter Optimization**

   ```php
   // Fine-tune for consistent evaluation
   // Lower temperature for more consistent scoring
   ```

### 5.2 Medium-term Goals

1. **RAG Integration**
   - Implement document retrieval system
   - Enhance prompts with relevant context
   - Store domain-specific knowledge base

2. **Few-Shot Templates**
   - Create example galleries
   - Dynamic example selection
   - Context-aware prompting

### 5.3 Advanced Features

1. **External Training Pipeline**
   - Use Hugging Face for actual fine-tuning
   - Convert models to GGUF format
   - Import into Ollama

2. **LoRA Adapter Management**
   - Research adapter compatibility
   - Create adapter training pipeline
   - Develop adapter switching system

## 6. Technical Implementation Plan

### 6.1 Phase 1: Basic Custom Models

```php
class ModelfileBuilder {
    public function setBase(string $model): self
    public function setSystem(string $prompt): self
    public function setTemplate(string $template): self
    public function setParameter(string $key, $value): self
    public function build(): string
}

class CustomModelManager {
    public function createModel(string $name, string $modelfile): bool
    public function deleteModel(string $name): bool
    public function listCustomModels(): array
}
```

### 6.2 Phase 2: RAG Enhancement

```php
class RAGProcessor {
    public function retrieveContext(string $query): array
    public function enhancePrompt(string $prompt, array $context): string
}
```

### 6.3 Phase 3: Advanced Training

```php
interface ModelTrainer {
    public function train(array $data): string
    public function createAdapter(array $data): string
}
```

## 7. Next Steps

1. ✅ Complete API endpoint testing
2. 🔄 Test model creation with simple Modelfile
3. ⏳ Verify LoRA adapter support
4. ⏳ Create proof-of-concept custom model
5. ⏳ Document limitations and workarounds

## 8. Conclusions

**What Ollama CAN do for custom models:**

- ✅ Custom system prompts (high impact)
- ✅ Template customization
- ✅ Parameter tuning
- ✅ Model composition from existing models
- ✅ Perfect for RAG applications
- ✅ Excellent for few-shot learning

**What Ollama CANNOT do:**

- ❌ True fine-tuning on custom datasets
- ❌ Weight updates or gradient-based training
- ❌ Creating entirely new model architectures

**Recommended Approach:**

Focus on **system prompt engineering** + **RAG** + **few-shot learning** as the primary custom model strategy. This provides significant customization capabilities without requiring actual model training.

---

*This research document will be updated as we continue testing and implementation.*
