<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class SingupSigninTest extends TestCase
{

    /**
     * Signup/Signin
     * 
     * @endpoint: POST /v1/signup
     * @endpoint: POST /v1/signin
     *
     * @return void
     */
    public function testSignupAndSigninEndpoints()
    {
        $faker = \Faker\Factory::create();
        $fakeEmail = $faker->email;
        $fakePswd = $faker->randomLetter;

        // Test Signup
        $signupResponse = $this->json(
            'POST', '/v1/signup', ['email' => $fakeEmail, 'password' => $fakePswd]
        );
        $signupResponse->assertStatus(200)
            ->assertJsonStructure(['token']);

        logResult(
            'POST /v1/signup', [
                'statusCode' => $signupResponse
                    ->status(),
                'content' => $signupResponse
                    ->content()
            ]
        );

        // Test missing email
        $signupResponse2 = $this->json(
            'POST', '/v1/signup', ['password' => $fakePswd]
        );
        $signupResponse2->assertStatus(401)
            ->assertJsonStructure(['error']);

        logResult(
            'POST /v1/signup (with missing data - email)', [
                'statusCode' => $signupResponse2
                    ->status(),
                'content' => $signupResponse2
                    ->content()
            ]
        );

        // Test missing password
        $signupResponse3 = $this->json(
            'POST', '/v1/signup', ['email' => $fakeEmail]
        );
        $signupResponse2->assertStatus(401)
            ->assertJsonStructure(['error']);

        logResult(
            'POST /v1/signup (with missing data - password)', [
                'statusCode' => $signupResponse3
                    ->status(),
                'content' => $signupResponse3
                    ->content()
            ]
        );

        // Test Signin
        $signinResponse = $this->json('POST', '/v1/signin', ['email' => $fakeEmail, 'password' => $fakePswd]);
        $signinResponse->assertStatus(200)
            ->assertJsonStructure(['token']);

        logResult(
            'POST /v1/signin', [
                'statusCode' => $signinResponse
                    ->status(),
                'content' => $signinResponse
                    ->content()
            ]
        );

        // Test wrong email
        $signinResponse2 = $this->json(
            'POST', '/v1/signin', [
                'email' => 'wrong-email@territory-api.com', 'password' => $fakePswd
            ]
        );
        $signinResponse2->assertStatus(401);
        logResult(
            'POST /v1/signin (with wrong email)', [
                'statusCode' => $signinResponse2
                    ->status(),
                'content' => $signinResponse2
                    ->content()
            ]
        );

        // Test wrong password
        $signinResponse3 = $this->json(
            'POST', '/v1/signin', [
                'email' => $fakeEmail, 'password' => 'wrong-password'
            ]
        );
        $signinResponse3->assertStatus(401);
        logResult(
            'POST /v1/signin (with wrong password)', [
                'statusCode' => $signinResponse3
                    ->status(),
                'content' => $signinResponse3
                    ->content()
            ]
        );
    }

    /**
     * Auth User
     * 
     * @endpoint: GET /v1/auth-user
     *
     * @return void
     */
    public function testAuthUserEndpoint()
    {
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        $adminToken = $signinResponse->getData()->token;

        // Test admin User data
        $userResponse = $this->json(
            'GET', '/v1/auth-user', [], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $userResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['userId', 'email', 'userType']])
            ->assertJsonFragment(['userType' => 'Admin']);

        logResult(
            'GET /v1/auth-user', [
                'statusCode' => $userResponse
                    ->status(),
                'content' => $userResponse
                    ->content()
            ]
        );

        // Test with wrong token
        $userResponse2 = $this->json(
            'GET', '/v1/auth-user', [], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken . 'corrupted'
            ]
        );
        $userResponse2->assertStatus(401)
            ->assertSee('Token is invalid');

        logResult(
            'GET /v1/auth-user (wrong token)', [
                'statusCode' => $userResponse2
                    ->status(),
                'content' => $userResponse2
                    ->content()
            ]
        );

        // Test with missing token
        $userResponse3 = $this->json('GET', '/v1/auth-user');
        $userResponse3->assertStatus(401)
            ->assertSee('Token is invalid');

        logResult(
            'GET /v1/auth-user (missing token)', [
                'statusCode' => $userResponse3
                    ->status(),
                'content' => $userResponse3
                    ->content()
            ]
        );
    }

    /**
     * Password Reset
     * 
     * @endpoint: GET 
     *
     * @return void
     */
    public function testPasswordResetEndpoint()
    {

        // TODO: Test Password Reset

    }
}