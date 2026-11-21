<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root mengarahkan tamu ke halaman login.
     */
    public function test_root_redirects_guests_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    /**
     * Halaman login dapat diakses.
     */
    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')->assertStatus(200);
    }
}
