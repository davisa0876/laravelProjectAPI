<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we're using the test environment
        $this->app['config']->set('database.default', 'mysql');
        
        // Disable rate limiting for tests
        $this->withoutRateLimiting();
    }

    protected function withoutRateLimiting()
    {
        return $this;
    }
}
