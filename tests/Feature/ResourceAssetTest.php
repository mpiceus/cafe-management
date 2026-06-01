<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResourceAssetTest extends TestCase
{
    public function test_whitelisted_resource_assets_are_served_without_a_frontend_build(): void
    {
        $this->get(route('resource.asset', ['type' => 'css', 'file' => 'layout.css']))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/css; charset=UTF-8');

        $this->get(route('resource.asset', ['type' => 'js', 'file' => 'order-create.js']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8')
            ->assertSee('Có thể phục vụ', false)
            ->assertSee(' đ', false);

        $this->get(route('resource.asset', ['type' => 'js', 'file' => 'bao-cao.js']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8');
    }

    public function test_unlisted_resource_assets_are_not_exposed(): void
    {
        $this->get(route('resource.asset', ['type' => 'css', 'file' => 'app.css']))
            ->assertNotFound();
    }
}
