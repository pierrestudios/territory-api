<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class ApiTest extends TestCase
{
    /**
     * Api root
     * 
     * @endpoint: GET /v1
     *
     * @return void
     */
    public function testApiRoot()
    {
        // Test Api root (/v1)
        $response = $this->get('/v1');
        $this->assertEquals(200, $response->status());
        $this->get('/v1')->assertSee('Territory Services API Version 1.0');
        logResult('GET /v1', ['status' => $response->status()]);

        // Test non-existent page to get 404
        $response2 = $this->get('/wrong-page-name');
        $this->assertEquals(404, $response2->status());
        logResult('GET /wrong-page-name', ['status' => $response->status()]);
    }

    /**
     * Validate
     * 
     * @endpoint: GET /v1/validate
     *
     * @return void
     */
    public function testValidateEndpoint()
    {
        $response = $this->get('/v1/validate');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        logResult(
            'GET /v1/validate', [
                'statusCode' => $response
                    ->status(),
                'content' => $response
                    ->content()
            ]
        );
    }
}
