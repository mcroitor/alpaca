# docker-compose.yml

Write a sample `docker-compose.yml` file that sets up a web service with nginx + php-fpm + mysql, according to the following requirements:

1. The file must describe three services: `frontend`, `backend`, `database`
2. The `frontend` service is built from the context `./frontend`
3. The `frontend` service is available on port 80 of the host system
4. The `frontend` service gets environment variables from the `frontend.env` and `.env` files
5. The `frontend` service mounts the data volume to the `/var/www/html` directory
6. The `frontend` service depends on the `backend` service
7. The `frontend` service runs in the intranet network
8. The `backend` service is built from the context `./backend`
9. The `backend` service gets environment variables from the `backend.env` and `.env` files
10. The `backend` service mounts the data volume to the `/var/www/html` directory
11. The `backend` service depends on the `database` service
12. The `backend` service runs in the intranet network
13. The `database` service is based on the image `mysql:latest`
14. The `database` service mounts the database volume to the `/var/lib/mysql` directory
15. The `database` service gets environment variables from the `database.env` file
16. The `database` service runs in the intranet network
17. The file describes the data and database volumes
18. The file describes the intranet network
