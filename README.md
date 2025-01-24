# Laravel API Project with Authentication and Weather Integration

A Laravel-based REST API that provides authentication, user management, and weather data integration. This project serves as the backend for the Vue.js Admin Dashboard.

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

- 🧪 Comprehensive Testing
  - Authentication Tests
  - Language Tests
  - Weather API Tests
  - PHPUnit Configuration

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- OpenWeatherMap API key

## Installation

1. Clone the repository:
```bash
git clone https://github.com/davisa0876/laravelProjectAPI.git
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
DB_DATABASE=contenthubDB
DB_USERNAME=UserProjet
DB_PASSWORD=contentUser1Laravel!23

# Weather API Configuration
WEATHER_API_URL=https://api.openweathermap.org/data/2.5
WEATHER_API_KEY=your_api_key_here  # Get your API key from OpenWeatherMap
```

> **Important**: To use the Weather API functionality:
> 1. Sign up for a free account at [OpenWeatherMap](https://openweathermap.org/api)
> 2. Generate your API key in your account dashboard
> 3. Replace `your_api_key_here` in the `.env` file with your actual API key
> 4. The free tier has a limit of 60 calls/minute
>
> Without a valid API key, the weather endpoints will not function correctly.

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

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user

### Weather
- `GET /api/weather` - Get weather data
  - Query params: `city` (required)

### Language
- `POST /api/language` - Switch application language
  - Body: `lang` (required, enum: 'en', 'es')

## Testing

Run the test suite:
```bash
php artisan test
```

Or with coverage report:
```bash
php artisan test --coverage
```

## Environment Variables

Key environment variables:
- `APP_ENV` - Application environment
- `DB_*` - Database configuration
- `WEATHER_API_*` - Weather API configuration
- `APP_LOCALE` - Default application locale
- `SANCTUM_STATEFUL_DOMAINS` - Allowed domains for Sanctum

