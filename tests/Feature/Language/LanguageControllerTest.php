<?php

namespace Tests\Feature\Language;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LanguageControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure we start with English
        App::setLocale('en');
    }

    public function test_can_switch_language_to_spanish()
    {
        $response = $this->postJson('/api/language', [
            'lang' => 'es'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Language switched successfully',
                'language' => 'es'
            ]);
    }

    public function test_can_switch_language_to_english()
    {
        $response = $this->postJson('/api/language', [
            'lang' => 'en'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Language switched successfully',
                'language' => 'en'
            ]);
    }

    public function test_cannot_switch_to_unsupported_language()
    {
        $response = $this->postJson('/api/language', [
            'lang' => 'fr'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lang']);
    }

    public function test_language_middleware_sets_locale_from_header()
    {
        // Ensure we start with English
        App::setLocale('en');
        $this->assertEquals('en', App::getLocale(), 'Initial locale should be en');

        $response = $this->withHeaders([
            'Accept-Language' => 'es',
            'Accept' => 'application/json'
        ])->getJson('/api/hello');

        $response->assertStatus(200);
        
        // Debug information
        $responseData = $response->json();
        $currentLocale = App::getLocale();
        
        $this->assertEquals(
            'es', 
            $currentLocale, 
            sprintf(
                "Expected locale to be 'es', but got '%s'. Response: %s. Initial locale was: %s",
                $currentLocale,
                json_encode($responseData),
                'en'
            )
        );
    }

    public function test_language_middleware_uses_default_for_invalid_locale()
    {
        // Ensure we start with English
        App::setLocale('en');
        $this->assertEquals('en', App::getLocale(), 'Initial locale should be en');

        $response = $this->withHeaders([
            'Accept-Language' => 'fr',
            'Accept' => 'application/json'
        ])->getJson('/api/hello');

        $response->assertStatus(200);

        $currentLocale = App::getLocale();
        
        $this->assertEquals(
            'en', 
            $currentLocale, 
            sprintf(
                "Expected locale to be 'en', but got '%s'. Response: %s. Initial locale was: %s",
                $currentLocale,
                json_encode($response->json()),
                'en'
            )
        );
    }
} 