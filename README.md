# Laravel API Project with Authentication and Weather Integration

A Laravel-based REST API that provides authentication, user management, weather data integration, and API monitoring. This project serves as the backend for the Vue.js Admin Dashboard.

## Features

- 🔐 Complete Authentication System
  - Login
  - Registration
  - Password Management
  - Token-based Authentication (Sanctum)
  - Secure Password Reset

- 🌍 Multi-language Support
  - English (en)
  - Spanish (es)
  - Language Switching Middleware
  - Localized Validation Messages

- 🌤️ Weather API Integration
  - OpenWeatherMap API Integration
  - Weather Data Retrieval
  - Error Handling
  - Response Caching

- 📊 API Monitoring & Analytics
  - Request/Response Logging
  - Performance Metrics
  - Error Tracking
  - Usage Statistics

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- OpenWeatherMap API key

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/your-repo.git
cd orion-example
```

2. Install PHP dependencies:
```bash
composer install
```

3. Create environment file:
```bash
cp .env.example .env
```

4. Configure your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8080
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Weather API Configuration
WEATHER_API_URL=https://api.openweathermap.org/data/2.5
WEATHER_API_KEY=your_api_key_here

# Telescope Configuration
TELESCOPE_ENABLED=true
TELESCOPE_DRIVER=database

# API Documentation
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_UI_DOC_EXPANSION=list
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

## API Documentation

This project uses Swagger/OpenAPI for API documentation. You can access the documentation at:

- 📚 Swagger UI: `http://localhost:8000/api/documentation`
- 📄 JSON format: `http://localhost:8000/docs/api-docs.json`

### Generating Documentation

The documentation is automatically generated when changes are made to the API annotations. To manually regenerate:

```bash
php artisan l5-swagger:generate
```

### Available Endpoints

The documentation includes detailed information about:
- Authentication endpoints (register, login, logout)
- Weather data endpoints
- Language switching
- API monitoring and analytics
- Crawler functionality

Each endpoint includes:
- Required parameters
- Request/response examples
- Authentication requirements
- Possible error responses

## API Monitoring with Telescope

Laravel Telescope provides deep insights into your API's behavior and performance.

### Accessing Telescope

Visit `http://localhost:8000/telescope` to access the dashboard.

### Available Monitors

Telescope provides monitoring for:
- 🔍 **Requests**: HTTP requests and responses
- ⚠️ **Exceptions**: Application errors
- 📝 **Logs**: Application logs
- 🗃️ **Database**: Query monitoring
- 📨 **Mail**: Email operations
- 🔔 **Notifications**: System notifications
- 🚀 **Jobs**: Queue processing
- 📦 **Cache**: Cache operations
- ⏰ **Schedule**: Task scheduling
- 🔑 **Redis**: Redis operations

### Telescope Data Management

To prevent Telescope from using too much storage:

```bash
# Clear all entries
php artisan telescope:clear

# Prune old entries
php artisan telescope:prune --hours=48
```

### Security

By default, Telescope is only accessible in the local environment. To modify access, update `app/Providers/TelescopeServiceProvider.php`:

```php
protected function gate()
{
    Gate::define('viewTelescope', function ($user) {
        return in_array($user->email, [
            'admin@example.com'
        ]);
    });
}
```

## API Crawler

The API includes a crawler that tracks all API requests and responses. The crawler logs:
- Request method and URL
- IP address and User Agent
- Request headers and parameters
- Response status code
- Execution time
- User ID (if authenticated)
- Timestamp

### Analyzing API Usage

To analyze API usage, use the following command:
```bash
# Analyze last 24 hours
php artisan api:analyze

# Analyze last 7 days
php artisan api:analyze --days=7
```

The analysis includes:
- Endpoint usage count
- Average response time
- Error rates
- Most active users
- Peak usage times

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

