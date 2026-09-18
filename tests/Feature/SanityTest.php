<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SanityTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_database_migrates(): void
    {
        $this->assertTrue(\Schema::hasTable('users'));
        $this->assertTrue(\Schema::hasTable('jadwal_ibadah'));
        $this->assertTrue(\Schema::hasTable('kitab'));
    }
}
