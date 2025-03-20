# SnipSnap - Code Snippet Manager

SnipSnap is a modern web application that helps developers store, organize, and retrieve their code snippets. Built with a Laravel backend and React frontend, this app combines powerful features with a clean, intuitive interface.

## What I Built

This project consists of two main components:
- `snip_snap_server`: Laravel-based REST API backend
- `snip_snap`: React-based frontend built with Vite

### Backend Features

The Laravel backend follows SOLID principles with a clean architecture:

- **Authentication**: JWT-based authentication for secure API access
- **CRUD Operations**: Create, read, update, and delete code snippets
- **Search & Filtering**: Advanced snippet search by title, description, code, or tags
- **Favorites**: Mark snippets as favorites for quick access
- **Tags**: Organize snippets with custom tags

### Architecture Highlights

We've implemented a clean, maintainable architecture following SOLID principles:

- **Repository Pattern**: Separates data access logic from business logic
- **Service Layer**: Contains business logic in dedicated service classes
- **DTOs (Data Transfer Objects)**: For clean data passing between layers
- **Form Requests**: Validation logic separated from controllers
- **Dependency Injection**: Used throughout for better testability

## Getting Started

### Prerequisites
- PHP 8.0+ (with Composer)
- MySQL 5.7+
- Node.js 14+

### Backend Setup

1. Navigate to the backend directory:
   ```
   cd snip_snap_server
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Create a database:
   ```
   php artisan db:create
   ```

4. Run migrations:
   ```
   php artisan migrate
   ```

5. Generate JWT secret:
   ```
   php artisan jwt:secret
   ```

6. Start the server:
   ```
   php artisan serve
   ```

The API will be available at `http://localhost:8000`.

## API Testing with Postman

You can test the API endpoints using Postman. Here are the key routes and how to use them:

### 1. Test API Connection
- **Method**: GET
- **URL**: http://localhost:8000/api/test
- **Headers**: None required
- **Expected Response**:
  ```json
  {
      "message": "API is working!"
  }
  ```

### 2. Register User
- **Method**: POST
- **URL**: http://localhost:8000/api/auth/register
- **Headers**: 
  - Content-Type: application/json
- **Body** (raw JSON):
  ```json
  {
      "username": "testuser",
      "email": "test@example.com",
      "password": "password123"
  }
  ```
- **Expected Response**: JWT token and user information

### 3. Login
- **Method**: POST
- **URL**: http://localhost:8000/api/auth/login
- **Headers**: 
  - Content-Type: application/json
- **Body** (raw JSON):
  ```json
  {
      "email": "test@example.com",
      "password": "password123"
  }
  ```
- **Expected Response**: JWT token for use in authenticated requests

### 4. Create or Update Snippet (New Feature!)
- **Method**: POST
- **URL**: http://localhost:8000/api/snippets/create-or-update/{id?}
  - Leave out the ID to create a new snippet
  - Include an ID to update an existing snippet
- **Headers**: 
  - Content-Type: application/json
  - Authorization: Bearer {your_jwt_token}
- **Body** (raw JSON):
  ```json
  {
      "title": "Hello World in JavaScript",
      "description": "Simple console log example",
      "code": "console.log('Hello, World!');",
      "language": "javascript",
      "is_favorite": false,
      "tags": ["javascript", "beginner"]
  }
  ```
- **Expected Response**: The created or updated snippet

### 5. Get All Snippets (with optional filtering)
- **Method**: GET
- **URL**: http://localhost:8000/api/snippets
  - Optional query parameters: search, language, is_favorite, tag, sort, direction, per_page
- **Headers**: 
  - Authorization: Bearer {your_jwt_token}
- **Expected Response**: List of snippets with pagination

### 6. Toggle Favorite
- **Method**: POST
- **URL**: http://localhost:8000/api/snippets/{snippet_id}/favorite
- **Headers**: 
  - Authorization: Bearer {your_jwt_token}
- **Expected Response**: Updated favorite status

### Important Note on Authorization

When sending requests that require authentication, make sure to:
1. Include the Authorization header with your token
2. Format it as: `Bearer {token}` (note the space between "Bearer" and the token)

Example:
```
Authorization: Bearer eyJ0e...uM
```


If you have any questions or suggestions about SnipSnap, feel free to reach out!
