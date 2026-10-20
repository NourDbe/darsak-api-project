Darsak API

A RESTful API for an educational platform built with Laravel. Darsak API provides endpoints for managing courses, lessons, assignments, categories, comments, and user authentication.

GitHub Repository: "https://github.com/NourDbe/darsak-api-project" (https://github.com/NourDbe/darsak-api-project)

Table of Contents

- "Project Overview" (#project-overview)
- "Features" (#features)
- "Technologies" (#technologies)
- "Requirements" (#requirements)
- "Installation" (#installation)
- "Running the Project" (#running-the-project)
- "API Endpoints" (#api-endpoints)
- "Authentication" (#authentication)
- "Testing" (#testing)
- "Project Structure" (#project-structure)
- "Author" (#author)

Project Overview

Darsak API is a backend project designed to support an educational platform where users can access courses, lessons, assignments, categories, and comments.

The project focuses on RESTful API design, database relationships, request validation, authentication, authorization, and automated testing.

Features

- RESTful API endpoints for courses, lessons, and assignments.
- User authentication using Laravel Sanctum.
- Role-based access control for students and teachers.
- Course creation, updating, and deletion through protected routes.
- Request validation using Laravel Form Requests.
- Polymorphic relationships for images, categories, and comments.
- Course ownership validation.
- Course levels managed using a PHP Enum.
- API-friendly JSON error responses.
- Database migrations and seeders.
- Automated feature tests.

Technologies

- PHP
- Laravel
- Laravel Sanctum
- SQLite
- Eloquent ORM
- REST API
- PHPUnit

Requirements

Make sure the following tools are installed:

- PHP
- Composer
- Laravel-compatible PHP extensions
- Git

Installation

1. Clone the repository

git clone https://github.com/NourDbe/darsak-api-project.git

2. Enter the project directory

cd darsak-api-project

3. Install dependencies

composer install

4. Configure the environment

Copy the example environment file.

For Windows Command Prompt:

copy .env.example .env

For macOS or Linux:

cp .env.example .env

Generate the application key:

php artisan key:generate

5. Configure the database

This project can be configured to use SQLite.

Create the SQLite database file at:

database/database.sqlite

In your ".env" file, configure the database connection:

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

If your local environment requires an absolute path, use the full path to your SQLite database file.

6. Run migrations and seeders

php artisan migrate --seed

This creates the database tables and inserts the configured sample data.

Running the Project

Start the Laravel development server:

php artisan serve

The default local API base URL is:

http://127.0.0.1:8000/api

You can test the endpoints using Thunder Client, Postman, or another API testing tool.

API Endpoints

The following table summarizes the main API routes.

Method| Endpoint| Description
GET| "/api/courses"| List courses
GET| "/api/courses/{course}"| Get course details
POST| "/api/courses"| Create a course
PUT/PATCH| "/api/courses/{course}"| Update a course
DELETE| "/api/courses/{course}"| Delete a course
GET| "/api/courses/{course}/lessons"| List lessons for a course
GET| "/api/lessons/{lesson}"| Get lesson details
GET| "/api/courses/{course}/assignments"| List assignments for a course
GET| "/api/assignments/{assignment}"| Get assignment details
GET| "/api/categories/{category}"| Get category details
GET| "/api/comments/latest"| Retrieve the latest comments
POST| "/api/{type}/{id}/comments"| Add a comment to a supported resource

Note: Routes that create, update, or delete resources are protected by authentication. Authorization and validation rules apply according to the endpoint and user role.

To inspect the complete route list in your local project, run:

php artisan route:list --path=api

Authentication

The project uses Laravel Sanctum for API authentication.

Protected endpoints require a valid access token. In Thunder Client or Postman, configure the request with a Bearer Token:

Authorization: Bearer YOUR_ACCESS_TOKEN

Replace "YOUR_ACCESS_TOKEN" with a token generated for an authorized user in your local environment.

Unauthenticated requests to protected API routes receive a JSON "401 Unauthorized" response.

Testing

Run the automated test suite with:

php artisan test

The tests cover API behavior, including resource operations, validation, relationships, and access control.

Project Structure

Important application directories include:

app/
├── Contracts/
├── Enums/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Requests/
├── Models/
├── Providers/
├── Rules/
└── Services/

database/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
└── Feature/

Author

Nour Dbeka

GitHub: "NourDbe" (https://github.com/NourDbe)

Project repository: "Darsak API" (https://github.com/NourDbe/darsak-api-project)
