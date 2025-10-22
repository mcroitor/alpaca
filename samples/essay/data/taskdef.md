# Definirea docker-compose.yml

Scrieți un exemplu de fișier docker-compose.yml pentru a construi un serviciu web nginx + php-fpm + mysql, care să îndeplinească următoarele cerințe:

1. Fișierul trebuie să descrie trei servicii: frontend, backend, database
2. Serviciul frontend este construit din contextul de construire ./frontend
3. Serviciul frontend este accesibil pe portul 80 al sistemului gazdă
4. Serviciul frontend primește variabilele de mediu din fișierele frontend.env și .env
5. Serviciul frontend montează volumul data în directorul /var/www/html
6. Serviciul frontend depinde de serviciul backend
7. Serviciul frontend rulează în rețeaua intranet
8. Serviciul backend este construit din contextul de construire ./backend
9. Serviciul backend primește variabilele de mediu din fișierele backend.env și .env
10. Serviciul backend montează volumul data în directorul /var/www/html
11. Serviciul backend depinde de serviciul database
12. Serviciul backend rulează în rețeaua intranet
13. Serviciul database se bazează pe imaginea mysql:latest
14. Serviciul database montează volumul database în directorul /var/lib/mysql
15. Serviciul database primește variabilele de mediu din fișierul database.env
16. Serviciul database rulează în rețeaua intranet
17. De asemenea, fișierul descrie volumele data și database
18. De asemenea, fișierul descrie rețeaua intranet
