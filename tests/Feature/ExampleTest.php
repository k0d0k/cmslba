<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Root menampilkan frontpage publik.
     */
    public function test_root_renders_frontpage(): void
    {
        $this->get('/')->assertStatus(200);
    }

    /**
     * Halaman login dapat diakses.
     */
    public function test_login_page_is_accessible(): void
    {
        $this->get('/login')->assertStatus(200);
    }
}
