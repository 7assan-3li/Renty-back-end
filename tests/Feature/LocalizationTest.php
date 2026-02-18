<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LocalizationTest extends TestCase
{
    /**
     * Test default locale.
     */
    public function test_default_locale_is_configured()
    {
        $this->assertEquals(config('app.locale'), App::getLocale());
    }

    /**
     * Test switching language to Arabic.
     */
    public function test_can_switch_language_to_arabic()
    {
        $response = $this->get(route('lang.switch', 'ar'));

        $response->assertRedirect();
        $this->assertEquals('ar', session('locale'));
    }

    /**
     * Test switching language to English.
     */
    public function test_can_switch_language_to_english()
    {
        $response = $this->get(route('lang.switch', 'en'));

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }

    /**
     * Test invalid locale.
     */
    public function test_invalid_locale_does_not_change_session()
    {
        $this->get(route('lang.switch', 'fr'));

        $this->assertFalse(session('locale') === 'fr');
    }

    /**
     * Test middleware sets locale from session.
     */
    public function test_middleware_sets_locale_from_session()
    {
        $this->withSession(['locale' => 'ar']);

        $this->get('/');

        $this->assertEquals('ar', App::getLocale());
    }
}
