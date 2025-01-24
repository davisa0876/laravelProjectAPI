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

>