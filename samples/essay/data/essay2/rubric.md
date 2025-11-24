## Grading Rubric

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