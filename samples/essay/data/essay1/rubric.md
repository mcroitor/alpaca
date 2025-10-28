# Rubric for Docker Compose File Creation

| Criterion                                                                                  | Max Score |
| ------------------------------------------------------------------------------------------ | --------- |
| The file must describe three services: `frontend`, `backend`, `database`                   | 15        |
| The `frontend` service is built from the context `./frontend`                              | 5         |
| The `frontend` service is available on port 80 of the host system                          | 5         |
| The `frontend` service gets environment variables from the `frontend.env` and `.env` files | 5         |
| The `frontend` service mounts the data volume to the `/var/www/html` directory             | 5         |
| The `frontend` service depends on the `backend` service                                    | 5         |
| The `frontend` service runs in the `intranet` network                                      | 5         |
| The `backend` service is built from the context `./backend`                                | 5         |
| The `backend` service gets environment variables from the `backend.env` and `.env` files   | 5         |
| The `backend` service mounts the data volume to the `/var/www/html` directory              | 5         |
| The `backend` service depends on the `database` service                                    | 5         |
| The `backend` service runs in the `intranet` network                                       | 5         |
| The `database` service is based on the image `mysql:latest`                                | 5         |
| The `database` service mounts the database volume to the `/var/lib/mysql` directory        | 5         |
| The `database` service gets environment variables from the `database.env` file             | 5         |
| The `database` service runs in the `intranet` network                                      | 5         |
| The file describes the data and database volumes                                           | 5         |
| The file describes the `intranet` network                                                  | 5         |
