# Dockerfile

## Task Definition

Write a `Dockerfile` for a Python application image that will run a web server on port `8000`. The image should include the following directives:

1. The build argument `PYTHON_VERSION` with a default value of `'3.9'` is defined.
2. The base image is `python` with the tag `'${PYTHON_VERSION}-alpine'`.
3. An environment variable `DSN` is set to `'sqlite:///data/app.db'`.
4. An environment variable `APP_DIR` is set to `'/app'`.
5. The working directory is set to `APP_DIR`.
6. A volume is mounted to the directory `'/data'` in the container.
7. The file `requirements.txt` is copied to the directory `APP_DIR` from the directory `'./src'`.
8. Dependencies are installed from the `requirements.txt` file using `pip install --no-cache-dir -r requirements.txt`.
9. Files are copied from the directory `'./src'` to the working directory `'/app'`.
10. Port `8000` is exposed in the container.
11. The entry point for the container is `"python /app/app.py"`.

## Solution sample

```Dockerfile
ARG PYTHON_VERSION=3.9
FROM python:${PYTHON_VERSION}-alpine
ENV DSN=sqlite:///data/app.db
ENV APP_DIR=/app
WORKDIR ${APP_DIR}
VOLUME /data
COPY ./src/requirements.txt ${APP_DIR}/
RUN pip install --no-cache-dir -r requirements.txt
COPY ./src/ ${APP_DIR}/
EXPOSE 8000
ENTRYPOINT ["python", "/app/app.py"]
```

## Evaluation

Max points: **100**.

### Grading Rubric

| # | Requirement | Excellent (9-10 pts) | Good (7-8 pts) | Satisfactory (4-6 pts) | Inadequate (0-3 pts) | Max Points |
|---|-------------|---------------------|----------------|------------------------|---------------------|------------|
| 1 | Build Argument | `ARG PYTHON_VERSION=3.9` exactly as specified | `ARG PYTHON_VERSION` with correct default value | `ARG` directive present but incorrect syntax or value | Missing `ARG` directive | 9 |
| 2 | Base Image | `FROM python:${PYTHON_VERSION}-alpine` using variable correctly | `FROM python:3.9-alpine` with hardcoded version | `FROM python` with incorrect tag or syntax | Wrong base image or missing `FROM` | 9 |
| 3 | DSN Environment | `ENV DSN=sqlite:///data/app.db` exactly as specified | `ENV DSN` with correct database path | `ENV DSN` present but incorrect path | Missing `ENV DSN` directive | 9 |
| 4 | APP_DIR Environment | `ENV APP_DIR=/app` exactly as specified | `ENV APP_DIR` with correct path | `ENV APP_DIR` present but incorrect path | Missing `ENV APP_DIR` directive | 9 |
| 5 | Working Directory | `WORKDIR ${APP_DIR}` using variable | `WORKDIR /app` with hardcoded path | `WORKDIR` present but incorrect path | Missing `WORKDIR` directive | 9 |
| 6 | Volume Mount | `VOLUME /data` exactly as specified | `VOLUME ["/data"]` in array format | `VOLUME` present but incorrect path | Missing `VOLUME` directive | 9 |
| 7 | Copy Requirements | `COPY ./src/requirements.txt ${APP_DIR}/` with correct paths and variable | `COPY ./src/requirements.txt /app/` with hardcoded path | `COPY` requirements.txt but incorrect source or destination | Missing requirements.txt copy | 9 |
| 8 | Install Dependencies | `RUN pip install --no-cache-dir -r requirements.txt` exactly as specified | `RUN pip install -r requirements.txt` without --no-cache-dir | `RUN pip install` with requirements.txt but other syntax issues | Missing dependency installation or wrong command | 10 |
| 9 | Copy Application | `COPY ./src/ ${APP_DIR}/` with correct paths and variable | `COPY ./src/ /app/` with hardcoded path | `COPY` from ./src but incorrect destination | Missing application files copy | 9 |
| 10 | Expose Port | `EXPOSE 8000` exactly as specified | `EXPOSE 8000` correctly implemented | Port exposed but incorrect number | Missing `EXPOSE` directive | 9 |
| 11 | Entry Point | `ENTRYPOINT ["python", "/app/app.py"]` or `ENTRYPOINT python /app/app.py` | Correct entry point with minor syntax variations | Entry point present but incorrect path or command | Missing or completely incorrect `ENTRYPOINT` | 9 |
| **TOTAL** | | | | | | **100** |

### Grading Guidelines

#### Quality Levels:
- **Excellent (9-10 points)**: Perfect implementation following best practices
- **Good (7-8 points)**: Correct implementation with minor improvements possible  
- **Satisfactory (4-6 points)**: Partially correct, shows understanding but has issues
- **Inadequate (0-3 points)**: Missing, incorrect, or non-functional

#### Common Point Deductions:
- **Hardcoded values instead of variables**: -1 to -2 points
- **Missing --no-cache-dir in pip install**: -1 point
- **Incorrect file paths**: -2 to -4 points
- **Wrong syntax but correct intent**: -2 to -3 points
- **Completely missing directive**: 0 points for that requirement

### Evaluator Guidelines

- **Partial Credit**: Award partial points when requirements are attempted but not fully correct (e.g., 5 points for partially correct implementations)
- **Innovation Bonus**: Consider additional bonus points for creative solutions that exceed requirements (multi-stage builds, security improvements, etc.)
- **Documentation**: Small bonus points for helpful comments explaining choices
- **Dockerfile Best Practices**: Consider bonus points for following Docker best practices (layer optimization, security, etc.)
- **Functionality**: Test the Dockerfile by building the image when possible
- **Consistency**: Use this rubric consistently across all student submissions
