# Docker Compose Assignment - Analytical Rubric

**Total Points: 100**

## Main Evaluation Table

| Criteria                           | Excellent (A)                                                                                                       | Good (B)                                                              | Satisfactory (C)                                        | Needs Improvement (D)                                       | Inadequate (F)                                                | Max Points |
| ---------------------------------- | ------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------- | ------------------------------------------------------- | ----------------------------------------------------------- | ------------------------------------------------------------- | ---------- |
| **File Structure & Syntax**        | 13-15 pts: Valid YAML syntax, proper indentation, correct docker-compose version, well-organized structure          | 10-12 pts: Valid YAML with minor formatting issues, correct structure | 7-9 pts: Valid YAML but some structural inconsistencies | 4-6 pts: Some YAML syntax errors that don't prevent parsing | 0-3 pts: Major syntax errors, invalid YAML, missing structure | **15**     |
| **Frontend Service Configuration** | 13-15 pts: All 5 requirements perfectly implemented (build context, port 80, env files, volume mount, dependencies) | 10-12 pts: 4 requirements correct, 1 minor issue                      | 7-9 pts: 3 requirements correct, 2 minor issues         | 4-6 pts: 2-3 requirements correct with issues               | 0-3 pts: Major configuration errors or missing elements       | **15**     |
| **Backend Service Configuration**  | 13-15 pts: All 5 requirements perfectly implemented (build context, env files, volume mount, dependencies, network) | 10-12 pts: 4 requirements correct, 1 minor issue                      | 7-9 pts: 3 requirements correct, 2 minor issues         | 4-6 pts: 2-3 requirements correct with issues               | 0-3 pts: Major configuration errors or missing elements       | **15**     |
| **Database Service Configuration** | 13-15 pts: All 5 requirements perfectly implemented (MySQL image, volume mount, env files, network, configuration)  | 10-12 pts: 4 requirements correct, 1 minor issue                      | 7-9 pts: 3 requirements correct, 2 minor issues         | 4-6 pts: 2-3 requirements correct with issues               | 0-3 pts: Major configuration errors or missing elements       | **15**     |
| **Volumes Configuration**          | 11-12 pts: Both data and database volumes properly defined and used                                                 | 8-10 pts: Both volumes defined with minor usage issues                | 6-7 pts: One volume correct, other with errors          | 3-5 pts: Volumes defined but used incorrectly               | 0-2 pts: Volumes missing or incorrectly defined               | **12**     |
| **Network Configuration**          | 12-13 pts: Intranet network properly defined, all services connected correctly                                      | 9-11 pts: Network defined, minor connection issues                    | 6-8 pts: Network defined with configuration errors      | 3-5 pts: Network partially configured                       | 0-2 pts: Network missing or incorrect                         | **13**     |
| **Requirements Compliance**        | 13-15 pts: All 18 requirements fully satisfied                                                                      | 10-12 pts: 16-17 requirements satisfied                               | 7-9 pts: 14-15 requirements satisfied                   | 4-6 pts: 10-13 requirements satisfied                       | 0-3 pts: Fewer than 10 requirements satisfied                 | **15**     |

## Detailed Requirements Checklist (100 points total)

| Requirement #          | Description                                               | Points  | ✓   |
| ---------------------- | --------------------------------------------------------- | ------- | --- |
| 1                      | Three services defined: frontend, backend, database       | 3       | □   |
| 2                      | Frontend service built from ./frontend context            | 3       | □   |
| 3                      | Frontend service accessible on port 80                    | 3       | □   |
| 4                      | Frontend service uses frontend.env and .env files         | 3       | □   |
| 5                      | Frontend service mounts data volume to /var/www/html      | 3       | □   |
| 6                      | Frontend service depends on backend service               | 3       | □   |
| 7                      | Frontend service runs in intranet network                 | 3       | □   |
| 8                      | Backend service built from ./backend context              | 3       | □   |
| 9                      | Backend service uses backend.env and .env files           | 3       | □   |
| 10                     | Backend service mounts data volume to /var/www/html       | 3       | □   |
| 11                     | Backend service depends on database service               | 3       | □   |
| 12                     | Backend service runs in intranet network                  | 3       | □   |
| 13                     | Database service based on mysql:latest image              | 3       | □   |
| 14                     | Database service mounts database volume to /var/lib/mysql | 3       | □   |
| 15                     | Database service uses database.env file                   | 3       | □   |
| 16                     | Database service runs in intranet network                 | 3       | □   |
| 17                     | Data and database volumes are defined                     | 10      | □   |
| 18                     | Intranet network is defined                               | 10      | □   |
| **Syntax & Structure** | Valid YAML, proper organization                           | 15      | □   |
| **TOTAL**              |                                                           | **100** |     |

## Common Deductions Reference

| Error Type                        | Point Deduction | Examples                                      |
| --------------------------------- | --------------- | --------------------------------------------- |
| **Missing Volume Definitions**    | -2 each         | Data or database volumes not defined          |
| **Wrong Port Mappings**           | -3              | Incorrect port configuration                  |
| **Missing Environment Files**     | -2 each         | frontend.env, backend.env, .env, database.env |
| **Incorrect Dependencies**        | -3 each         | Wrong or missing depends_on configurations    |
| **Network Configuration Errors**  | -5              | Missing or incorrect intranet network setup   |
| **YAML Syntax Errors**            | -2 to -10       | Depending on severity and impact              |
| **Missing Service Configuration** | -5 each         | Incomplete service definitions                |

## Evaluator Guidelines

- **Partial Credit**: Award partial points when requirements are attempted but not fully correct
- **Innovation Bonus**: Consider additional bonus points for creative solutions that exceed requirements  
- **Documentation**: Small bonus points for helpful comments explaining choices
- **Functionality**: Test the docker-compose file for actual functionality when possible
- **Consistency**: Use this rubric consistently across all student submissions
