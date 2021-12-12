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
        $this->logEndpointTestResult('GET /v1', ['status' => $response->status()]);

        // Test non-existent page to get 404
        $response2 = $this->get('/wrong-page-name');
        $this->assertEquals(404, $response2->status());
        $this->logEndpointTestResult('GET /wrong-page-name', ['status' => $response->status()]);
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
        $this->logEndpointTestResult(
            'GET /v1/validate', [
                'statusCode' => $response
                    ->status(),
                'content' => $response
                    ->content()
            ]
        );
    }

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

        $this->logEndpointTestResult(
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

        $this->logEndpointTestResult(
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

        $this->logEndpointTestResult(
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

        $this->logEndpointTestResult(
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
        $this->logEndpointTestResult(
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
        $this->logEndpointTestResult(
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
        // Get admin User
        $signinResponse = $this->getAdminData();
        $this->assertEquals(200, $signinResponse->status());

        // Get admin token
        $token = $signinResponse->getData()->token;

        // Get admin User data
        $userResponse = $this->json(
            'GET', '/v1/auth-user', [], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );

        // Test admin User data
        $userResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['userId', 'email', 'userType']])
            ->assertJsonFragment(['userType' => 'Admin']);

        $this->logEndpointTestResult(
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
                'Authorization' => 'Bearer ' . $token . 'corrupted'
            ]
        );
        $userResponse2->assertStatus(401)
            ->assertSee('Token is invalid');

        $this->logEndpointTestResult(
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

        $this->logEndpointTestResult(
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

    /**
     * Users and Publishers
     * 
     * @endpoint: GET /v1/users
     * @endpoint: POST /v1/users/{userId}/save
     * @endpoint: POST /v1/publishers/add
     * @endpoint: POST /v1/publishers/attach-user
     * @endpoint: POST /v1/publishers/{publisherId}/save
     * @endpoint: GET /v1/publishers
     * @endpoint: POST /v1/publishers/filter
     * @endpoint: POST /v1/publishers/{publisherId}/delete
     * @endpoint: POST /v1/users/{userId}/delete
     *
     * @return void
     */
    public function testUsersAndPublishersEndpoints()
    {
        // Test as Manager viewing Users
        $faker = \Faker\Factory::create();
        $managerPass = '123456';
        $managerData = ['email' => $faker->email, 'password' => bcrypt($managerPass), 'level' => 3];
        $managerUser = User::factory()->create($managerData);
        $this->assertTrue($managerUser instanceof \App\Models\User);
        $managerSigninResponse = $this->getUserData(['email' => $managerUser->email, 'password' => $managerPass]);
        $this->assertEquals(200, $managerSigninResponse->status());
        $managerToken = $managerSigninResponse->getData()->token;
        $managerUsersResponse = $this->json(
            'GET', '/v1/users', [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );

        // Should fail
        $managerUsersResponse->assertStatus(403)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'GET /v1/users  (as Manager)', [
                'statusCode' => $managerUsersResponse
                    ->status(),
                'totalUsers' => $managerUsersResponse
                    ->content()
            ]
        );

        // Test as Editor viewing Users
        $faker2 = \Faker\Factory::create();
        $editorPass = '123456';
        $editorData = ['email' => $faker2->email, 'password' => bcrypt($editorPass), 'level' => 2];
        $editorUser = \App\Models\User::create($editorData);
        $this->assertTrue($editorUser instanceof \App\Models\User);
        $editorSigninResponse = $this->getUserData(['email' => $editorUser->email, 'password' => $editorPass]);
        $this->assertEquals(200, $editorSigninResponse->status());
        $editorToken = $editorSigninResponse->getData()->token;
        $editorSigninResponse = $this->json(
            'GET', '/v1/users', [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        // Should fail
        $editorSigninResponse->assertStatus(403)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'GET /v1/users (as Editor)', [
                'statusCode' => $editorSigninResponse
                    ->status(),
                'totalUsers' => $editorSigninResponse
                    ->content()
            ]
        );

        // Test as Admin viewing Users
        $signinResponse = $this->getAdminData();
        $this->assertEquals(200, $signinResponse->status());
        $token = $signinResponse->getData()->token;
        $usersResponse = $this->withHeaders([
            'Accept' => 'application/json', 
            'Content-Type' => 'application/json', 
            'Authorization' => 'Bearer ' . $token
        ])->json(
            'GET', '/v1/users',
        );

        $usersResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['userId', 'email', 'userType']]]);

        $this->logEndpointTestResult(
            'GET /v1/users', [
                'statusCode' => $usersResponse
                    ->status(),
                'totalUsers' => count(
                    $usersResponse->getOriginalContent()['data']
                )
            ]
        );

        // Get one user (Editor) to edit $userToEdit
        $userToEdit = $editorUser;
        $userToEditResponse = $this->withHeaders([
            'Accept' => 'application/json', 
            'Content-Type' => 'application/json', 
            'Authorization' => 'Bearer ' . $token
        ])->json(
            'POST', '/v1/users/' . $userToEdit->id . '/save', [
                "userType" => 'NoteEditor', "email" => $userToEdit->email
            ], 
        );
 
        $userToEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->assertDatabaseHas(
            'users', [
                "level" => \App\Models\User::getType('NoteEditor'), 'id' => $userToEdit->id, 'email' => $userToEdit->email
            ]
        );

        $this->logEndpointTestResult(
            'POST /v1/users/{userId}/save', [
                'statusCode' => $userToEditResponse
                    ->status()
            ]
        );

        // Create a  Publisher 
        $faker = \Faker\Factory::create();
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $publisherCreateResponse = $this->json(
            'POST', '/v1/publishers/add', [
                "firstName" => $firstName, "lastName" => $lastName
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $publisherCreateResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(["firstName" => $firstName, "lastName" => $lastName,]);

        $this->logEndpointTestResult(
            'POST /v1/publishers/add', [
                'statusCode' => $publisherCreateResponse
                    ->status(),
                'content' => $publisherCreateResponse
                    ->content()
            ]
        );

        // Attach Publisher to User
        $createdPublisher = $publisherCreateResponse
            ->getOriginalContent()['data'];
        $userToAttachPublisherGetResponse = $this->json(
            'POST', '/v1/publishers/attach-user', [
                "publisherId" => $createdPublisher['publisherId'], "userId" => $userToEdit['userId'],
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $userToAttachPublisherGetResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        // Check $createdPublisher data in db
        $this->assertDatabaseHas(
            'publishers', [
                'id' => $createdPublisher['publisherId'], 'user_id' => $userToEdit['userId']
            ]
        );
        $this->logEndpointTestResult(
            'POST /v1/publishers/attach-user', [
                'statusCode' => $userToAttachPublisherGetResponse
                    ->status()
            ]
        );

        // Update Publisher
        $createdPublisher['lastName'] = $createdPublisher['lastName'] . '-Editted';
        $createdPublisherUpdatedResponse = $this->json(
            'POST', '/v1/publishers/' . $createdPublisher['publisherId'] . '/save', [
                'firstName' => $createdPublisher['firstName'], 
                'lastName' => $createdPublisher['lastName']
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $createdPublisherUpdatedResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['lastName' => $createdPublisher['lastName'],]);

        $this->logEndpointTestResult(
            'POST /v1/publishers/{publisherId}/save', [
                'statusCode' => $createdPublisherUpdatedResponse
                    ->status(),
                'content' => $createdPublisherUpdatedResponse
                    ->content()
            ]
        );

        // Get Updated Publisher
        $updatedPublisherResponse = $this->json(
            'GET', '/v1/publishers/' . $createdPublisher['publisherId'], [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $updatedPublisherResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['lastName' => $createdPublisher['lastName'],]);

        $this->logEndpointTestResult(
            'GET /v1/publishers/{publisherId}', [
                'statusCode' => $updatedPublisherResponse
                    ->status(),
                'content' => $updatedPublisherResponse
                    ->content()
            ]
        );

        // Store $publisherToDelete and $userToDelete here, to delete later 
        $publisherToDelete = $updatedPublisherResponse
            ->getOriginalContent()['data'];
        $userToDelete = $userToEdit;

        // Get all Publishers
        $allPublishersResponse = $this->json(
            'GET', '/v1/publishers', [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $allPublishersResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]]);

        $this->logEndpointTestResult(
            'GET /v1/publishers', [
                'statusCode' => $allPublishersResponse
                    ->status(),
                'totalPublishers' => count(
                    $allPublishersResponse->getOriginalContent()['data']
                )
            ]
        );

        // Create another Publisher without User
        $faker2 = \Faker\Factory::create();
        $firstName2 = $faker2->firstName;
        $lastName2 = $faker2->lastName . ' NoUser';
        $anotherPublisherCreateResponse = $this->json(
            'POST', '/v1/publishers/add', [
                "firstName" => $firstName2, 
                "lastName" => $lastName2
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $anotherPublisherCreateResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['firstName' => $firstName2, 'lastName' => $lastName2]);

        $this->logEndpointTestResult(
            'POST /v1/publishers/add', [
                'statusCode' => $anotherPublisherCreateResponse
                    ->status()
            ]
        );

        // Get all Publishers with no User attached
        $allPublishersWithoutUserResponse = $this->json(
            'POST', '/v1/publishers/filter', ['userId' => null], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $allPublishersWithoutUserResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]]);

        $this->logEndpointTestResult(
            'POST /v1/publishers/filter', [
                'statusCode' => $allPublishersWithoutUserResponse
                    ->status(),
                'totalPublishers' => count(
                    $allPublishersWithoutUserResponse->getOriginalContent()['data']
                )
            ]
        );

        // Delete created publisher
        $deleteCreatedPublisherResponse = $this->json(
            'POST', '/v1/publishers/' . $publisherToDelete['publisherId'] . '/delete', [], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $deleteCreatedPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        $this->logEndpointTestResult(
            'POST /v1/publishers/{publisherId}/delete', [
                'statusCode' => $anotherPublisherCreateResponse
                    ->status()
            ]
        );

        // Delete a user
        $deleteCreatedUserResponse = $this->json(
            'POST', '/v1/users/' . $userToDelete->id . '/delete', [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $deleteCreatedUserResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        $this->logEndpointTestResult(
            'POST /v1/users/{userId}/delete', [
                'statusCode' => $deleteCreatedUserResponse
                    ->status()
            ]
        );
    }

    /**
     * Territories
     * 
     * @endpoint: GET /v1/territories
     * @endpoint: POST /v1/territories/add
     * @endpoint: GET /v1/territories/{territoryId}
     * @endpoint: POST /v1/territories/{territoryId}/save
     * @endpoint: POST /v1/territories/{territoryId}/addresses/add
     * @endpoint: POST /v1/territories/{territoryId}/addresses/edit/{addressId}
     * @endpoint: POST /v1/addresses/{addressId}/remove
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     *
     * @return void
     */
    public function testTerritoriesEndpoints()
    {
        // Get default Admin
        $signinResponse = $this->getAdminData();
        $this->assertEquals(200, $signinResponse->status());

        // Get admin token
        $token = $signinResponse->getData()->token;

        // Add a territory as Admin
        $faker = \Faker\Factory::create();
        $city = $faker->city;
        $sampleData1 = [
            'number' => $faker->numberBetween(1, 999),
            'location' => $faker->numberBetween(100, 999) . " $faker->streetName"
        ];
        $territoryAddResponse = $this->json(
            'POST', '/v1/territories/add', $sampleData1, 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $territoryAddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses'
                    ]
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/add (as Admin)', [
                'statusCode' => $territoryAddResponse
                    ->status(),
                'territory added' => $territoryAddResponse->getOriginalContent()['data'],
            ]
        );

        // Get territories
        $territoriesResponse = $this->json(
            'GET', '/v1/territories', [], 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        // Test territories data
        $territoriesResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses'
                        ]
                    ]
                ]
            );

        $this->logEndpointTestResult(
            'GET /v1/territories (as Admin)', [
                'statusCode' => $territoriesResponse
                    ->status(),
                'total territories' => count(
                    $territoriesResponse->getOriginalContent()['data']
                ),
            ]
        );

        // Get 1 territory as Admin/Manager view (territories-all)
        $territory = \App\Models\Territory::first();
        $territoryResponse = $this->json(
            'GET', '/v1/territories-all/' . $territory['id'], [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $territoryResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses'
                    ]
                ]
            );

        $this->logEndpointTestResult(
            'GET /v1/territories-all/{territoryId} (as Admin)', [
                'statusCode' => $territoryResponse
                    ->status(),
            ]
        );

        // Get 1 territory as Auth User
        $territory1Response = $this->json(
            'GET', '/v1/territories/' . $territory['id'], [], 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $territory1Response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses'
                    ]
                ]
            );

        $this->logEndpointTestResult(
            'GET /v1/territories/{territoryId} (as Admin)', [
                'statusCode' => $territory1Response
                    ->status(), 'result' => $territory1Response
            ]
        );

        // Test adding duplicate territory
        $territoryAddDupResponse = $this->json(
            'POST', '/v1/territories/add', $sampleData1, 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $territoryAddDupResponse->assertStatus(409)
            ->assertJsonFragment(['error' => 'A territory with Number, "' . $sampleData1['number'] . '" already exist.']);

            $this->logEndpointTestResult(
            'POST /v1/territories/add (Duplicate as Admin)', [
                'statusCode' => $territoryAddDupResponse
                    ->status(),
                'territory add duplicate error' => $territoryAddDupResponse->getOriginalContent(),
            ]
        );

        // Update territory
        $city = $faker->city;
        $terrToEditResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/save', [
                "cityState" => $city,
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $terrToEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/save (as Admin)', [
                'statusCode' => $terrToEditResponse
                    ->status(),
                'result' => $terrToEditResponse
                    ->getOriginalContent()
            ]
        );

        // Test Add address as Admin
        $name = $faker->name;
        $phone = $faker->phoneNumber;

        $addressAddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '300', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => 1,
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $addressAddResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (as Admin)', [
                'statusCode' => $addressAddResponse
                    ->status(),
                'result' => $addressAddResponse
                    ->getOriginalContent()
            ]
        );

        // store $addressData for later use
        $addressData = $addressAddResponse
            ->getOriginalContent()['data'];

        // Test Add address with new Street as Admin
        $street = $faker->streetName;
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $address2AddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '301', 
                'apt' => '', 
                'phone' => $phone, 
                'street' => [0 => (object)['street' => $street, 'isAptBuilding' => 0]],
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $address2AddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['territory_id', 'name', 'phone', 'address', 'street_id']
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (with New Street) (as Admin)', [
                'statusCode' => $address2AddResponse
                    ->status(),
                'result' => $address2AddResponse
                    ->getOriginalContent()
            ]
        );

        // store $address2Data for later use
        $address2Data = $address2AddResponse->getOriginalContent()['data'];

        // Test Edit address as Admin
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressEditResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData['id'], [
                'name' => $name, 
                'address' => '302', 
                'phone' => $phone, 
                'streetId' => $addressData['street_id'],
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $addressEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Admin)', [
                'statusCode' => $addressEditResponse
                    ->status(),
                'result' => $addressEditResponse
                    ->getOriginalContent()
            ]
        );

        // Test remove address as Admin (Soft Delete = set inactive=1)
        $addressRemoveResponse = $this->json(
            'POST', '/v1/addresses/' . $addressData['id'] . '/remove', [
                'note' => 'Reason for delete is test'
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $addressRemoveResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        $this->logEndpointTestResult(
            'POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as Admin)', [
                'statusCode' => $addressRemoveResponse
                    ->status(),
                'result' => $addressRemoveResponse
                    ->getOriginalContent()
            ]
        );

        // Test remove address as Admin (Hard Delete)
        $addressRemove2Response = $this->json(
            'POST', '/v1/addresses/' . $addressData['id'] . '/remove', [
                'note' => 'Reason for delete is test', 'delete' => true
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $addressRemove2Response->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/addresses/{addressId}/remove (Hard Delete) (as Admin)', [
                'statusCode' => $addressRemove2Response
                    ->status(),
                'result' => $addressRemove2Response
                    ->getOriginalContent()
            ]
        );

        // Test Add notes as Admin
        $noteAddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $address2Data['id'] . '/notes/add', [
                'note' => 'Note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $noteAddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Admin)', [
                'statusCode' => $noteAddResponse->status(),
                'result' => $noteAddResponse->getOriginalContent()
            ]
        );

        // Test Edit notes as Admin
        $noteData = $noteAddResponse->getOriginalContent()['data'];
        $noteEditResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData['id'], [
                'note' => 'Note test edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $noteEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Admin)', [
                'statusCode' => $noteEditResponse->status(),
                'result' => $noteEditResponse->getOriginalContent()
            ]
        );

        // Test as Manager
        $managerPass = '123456';
        $managerData = ['email' => $faker->email, 'password' => bcrypt($managerPass), 'level' => 3];
        $managerUser = \App\Models\User::create($managerData);
        $this->assertTrue($managerUser instanceof \App\Models\User);
        $managerSigninResponse = $this->getUserData(['email' => $managerUser->email, 'password' => $managerPass]);
        $this->assertEquals(200, $managerSigninResponse->status());
        $managerToken = $managerSigninResponse->getData()->token;

        // Add Address to Territory as Manager
        $street = $faker->streetName;
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressAddResponse2 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '400', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => 1
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );
        $addressAddResponse2->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['territory_id', 'name', 'phone', 'address', 'street_id']
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (as Manager)', [
                'statusCode' => $addressAddResponse2
                    ->status(),
                'result' => $addressAddResponse2
                    ->getOriginalContent()
            ]
        );

        // Edit Address as Manager
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressData2 = $addressAddResponse2->getOriginalContent()['data'];
        $addressEditResponse2 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData2['id'], [
                'name' => $name, 
                'address' => '402', 
                'phone' => $phone, 
                'streetId' => $addressData2['street_id']
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );
        $addressEditResponse2->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Manager)', [
                'statusCode' => $addressEditResponse2
                    ->status(),
                'result' => $addressEditResponse2
                    ->getOriginalContent()
            ]
        );


        // Add Note as Manager
        $noteAddResponse2 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData2['id'] . '/notes/add', [
                'note' => 'Note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );

        $noteAddResponse2->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Manager)', [
                'statusCode' => $noteAddResponse2->status(),
                'result' => $noteAddResponse2->getOriginalContent()
            ]
        );

        // Edit Note as Manager
        $noteData2 = $noteAddResponse2->getOriginalContent()['data'];
        $noteEditResponse2 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData2['id'], [
                'note' => 'Note test edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );

        $noteEditResponse2->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Manager)', [
                'statusCode' => $noteEditResponse2->status(),
                'result' => $noteEditResponse2->getOriginalContent()
            ]
        );

        // Test remove address as Manager (Soft Delete = set inactive=1)
        $addressRemoveResponse2 = $this->json(
            'POST', '/v1/addresses/' . $addressData2['id'] . '/remove', [
                'note' => 'Reason for delete is test'
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );

        $addressRemoveResponse2->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as Manager)', [
                'statusCode' => $addressRemoveResponse2
                    ->status(),
                'result' => $addressRemoveResponse2
                    ->getOriginalContent()
            ]
        );

        // Test remove address as Admin (Hard Delete)
        $addressRemove2Response2 = $this->json(
            'POST', '/v1/addresses/' . $addressData2['id'] . '/remove', [
                'note' => 'Reason for delete is test', 'delete' => true
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $managerToken
            ]
        );
        $addressRemove2Response2->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/addresses/{addressId}/remove (Hard Delete) (as Manager)', [
                'statusCode' => $addressRemove2Response2
                    ->status(),
                'result' => $addressRemove2Response2
                    ->getOriginalContent()
            ]
        );

        // Unassign Territory $territory
        $terrUnassignResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/save', [
                "publisherId" => null, "date" => date('Y-m-d')
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );
        $terrUnassignResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/save (Unassign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );

        // Test as Editor
        $editorPass = '123456';
        $editorData = ['email' => $faker->email, 'password' => bcrypt($editorPass), 'level' => 2];
        $editorUser = \App\Models\User::create($editorData);
        $this->assertTrue($editorUser instanceof \App\Models\User);
        $editorSigninResponse = $this->getUserData(
            [
                'email' => $editorUser->email, 'password' => $editorPass
            ]
        );
        $this->assertEquals(200, $editorSigninResponse->status());
        $editorToken = $editorSigninResponse->getData()->token;


        // Create a Publisher Assign User to Publisher
        $firstName3 = $faker->firstName;
        $lastName3 = $faker->lastName;
        $editorPublisher = \App\Models\Publisher::create(["first_name" => $firstName3, "last_name" => $lastName3]);
        $editorUserAttachPublisherResponse = $this->json(
            'POST', '/v1/publishers/attach-user', [
                "publisherId" => $editorPublisher->id, "userId" => $editorUser->id,
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $editorUserAttachPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->assertDatabaseHas('publishers', ['id' => $editorPublisher->id, 'user_id' => $editorUser->id]);
        $this->logEndpointTestResult(
            'POST /v1/publishers/attach-user', [
                'statusCode' => $editorUserAttachPublisherResponse
                    ->status(),
                'result' => $editorUserAttachPublisherResponse
                    ->content()
            ]
        );

        // Add Address to territory as Editor (Unassigned)
        $street = $faker->streetName;
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressAddResponse3 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '500', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => 1
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        $addressAddResponse3->assertStatus(200)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (as Editor Unassigned)', [
                'statusCode' => $addressAddResponse3
                    ->status(),
                'result' => $addressAddResponse3
                    ->getOriginalContent()
            ]
        );


        // Assign Territory $territory to Publisher
        $terrUnassignResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/save', [
                "publisherId" => $editorPublisher->id, "date" => date('Y-m-d')
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertDatabaseHas(
            'territories', [
                'id' => $territory['id'], 'publisher_id' => $editorPublisher->id
            ]
        );
        $terrUnassignResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/save (Assign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );

        // Try again: Add Address to territory as Editor (Assigned)
        $street = $faker->streetName;
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressAddResponse4 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '500', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => 1
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        $addressAddResponse4->assertStatus(200)
            ->assertJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (as Editor Assigned)', [
                'statusCode' => $addressAddResponse4
                    ->status(),
                'result' => $addressAddResponse4
                    ->getOriginalContent()
            ]
        );

        // Edit Address with Publisher->User as Editor
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressData3 = $addressAddResponse4->getOriginalContent()['data'];
        $addressEditResponse3 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData3['id'], [
                'name' => $name, 
                'address' => '502', 
                'phone' => $phone, 
                'streetId' => $addressData3['street_id']
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );
        $addressEditResponse3->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Editor)', [
                'statusCode' => $addressEditResponse3
                    ->status(),
                'result' => $addressEditResponse3
                    ->getOriginalContent()
            ]
        );

        // Add notes to Address with Publisher->User as Editor
        $noteAddResponse3 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData3['id'] . '/notes/add', [
                'note' => 'Note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        $noteAddResponse3->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        'content', 'date', 'entity', 'entity_id', 'user_id', 'id'
                    ]
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Editor)', [
                'statusCode' => $noteAddResponse3->status(),
                'result' => $noteAddResponse3->getOriginalContent()
            ]
        );

        // Edit Note with Publisher->User as Editor
        $noteData3 = $noteAddResponse3->getOriginalContent()['data'];
        $noteEditResponse4 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData3['id'], [
                'note' => 'Note test edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        $noteEditResponse4->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Editor)', [
                'statusCode' => $noteEditResponse4->status(),
                'result' => $noteEditResponse4->getOriginalContent()
            ]
        );

        // Test as NoteEditor
        $noteEditorPass = '123456';
        $noteEditorData = ['email' => $faker->email, 'password' => bcrypt($noteEditorPass), 'level' => 5];
        $noteEditorUser = \App\Models\User::create($noteEditorData);
        $this->assertTrue($noteEditorUser instanceof \App\Models\User);
        $noteEditorSigninResponse = $this->getUserData(
            [
                'email' => $noteEditorUser->email, 'password' => $noteEditorPass
            ]
        );
        $this->assertEquals(200, $noteEditorSigninResponse->status());
        $noteEditorToken = $noteEditorSigninResponse->getData()->token;

        // Create a Publisher Assign User to Publisher
        $firstName4 = $faker->firstName;
        $lastName4 = $faker->lastName;
        $noteEditorPublisher = \App\Models\Publisher::create(
            [
                "first_name" => $firstName4, "last_name" => $lastName4
            ]
        );
        $noteEditorUserAttachPublisherResponse = $this->json(
            'POST', '/v1/publishers/attach-user', [
                "publisherId" => $noteEditorPublisher->id, "userId" => $noteEditorUser->id,
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $noteEditorUserAttachPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->assertDatabaseHas('publishers', ['id' => $noteEditorPublisher->id, 'user_id' => $noteEditorUser->id]);
        $this->logEndpointTestResult(
            'POST /v1/publishers/attach-user (as NoteEditor)', [
                'statusCode' => $noteEditorUserAttachPublisherResponse
                    ->status(),
                'result' => $noteEditorUserAttachPublisherResponse
                    ->content()
            ]
        );

        // Assign Territory $territory to Publisher
        $terrUnassignResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/save', [
                "publisherId" => $noteEditorPublisher->id, "date" => date('Y-m-d')
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $token
            ]
        );

        $this->assertDatabaseHas(
            'territories', [
                'id' => $territory['id'], 
                'publisher_id' => $noteEditorPublisher->id
            ]
        );
        $terrUnassignResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/save (Assign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );

        // Try Add Address to territory as NoteEditor (Assigned)
        $street = $faker->streetName;
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $addressAddResponse5 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '500', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => 1
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $noteEditorToken
            ]
        );

        // Friendly fail
        $addressAddResponse5->assertStatus(200)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/add (as NoteEditor Assigned)', [
                'statusCode' => $addressAddResponse5
                    ->status(),
                'result' => $addressAddResponse5
                    ->getOriginalContent()
            ]
        );

        // TODO: Try Delete Address as NoteEditor 
        $addressRemoveResponse4 = $this->json(
            'POST', '/v1/addresses/' . $addressData3['id'] . '/remove', [
                'note' => 'Reason for delete is test'
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $noteEditorToken
            ]
        );

        $addressRemoveResponse4->assertStatus(403)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as NoteEditor)', [
                'statusCode' => $addressRemoveResponse4
                    ->status(),
                'result' => $addressRemoveResponse4
                    ->content()
            ]
        );

        // Add notes to Address with Publisher->User as NoteEditor
        $noteAddResponse4 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData3['id'] . '/notes/add', [
                'note' => 'Note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $noteEditorToken
            ]
        );

        $noteAddResponse4->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']
                ]
            );

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as NoteEditor)', [
                'statusCode' => $noteAddResponse4->status(),
                'result' => $noteAddResponse4->getOriginalContent()
            ]
        );

        // Edit notes as Non Note->User as Editor
        $noteData4 = $noteAddResponse4->getOriginalContent()['data'];
        $noteEditResponse5 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData4['id'], [
                'note' => 'Note test edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        // Should fail, since not Note->User
        $noteEditResponse5->assertStatus(403)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Editor Non-User)', [
                'statusCode' => $noteEditResponse5->status(),
                'result' => $noteEditResponse5->content()
            ]
        );

        // Edit Note with Publisher->User as NoteEditor
        $noteEditResponse6 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData4['id'], [
                'note' => 'Note test edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $noteEditorToken
            ]
        );

        $noteEditResponse6->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->logEndpointTestResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as NoteEditor)', [
                'statusCode' => $noteEditResponse6->status(),
                'result' => $noteEditResponse6->content()
            ]
        );
    }


    /**
     * Private methods
     */

    /**
     * Private getAdminData
     * 
     * @return object
     */
    protected function getAdminData()
    {
        return $this->getUserData([
            'email' => config('app.adminEmail'),
            'password' => config('app.adminPassword')
        ]);
    }

    /**
     * Private getUserData
     * 
     * @param array $creds 
     * 
     * @return object
     */
    protected function getUserData($creds = [])
    {
        return empty($creds) ? null : $this->json('POST', '/v1/signin', $creds);
    }

    /**
     * Private logEndpointTestResult
     * 
     * @param string $endpoint 
     * @param array  $result 
     * 
     * @return object
     */
    protected function logEndpointTestResult($endpoint, $result = [])
    {
        $blue = "\033[36m";
        $bold = "\033[1m";
        $normal = "\033[0m";
        $grey = "\033[37m";

        // Remove styles for browser run (argv set to: --colors=never)
        foreach ($_SERVER['argv'] as $arg) {
            if (strpos($arg, '--colors') !== false) {
                $colorsFlag_arr = explode('=', $arg);
                // var_dump($colorsFlag_arr);
                if (end($colorsFlag_arr) === 'never') {
                    $blue = $bold = $normal = $grey = "";
                }
            }
        }

        $log = "\n" . $blue . date('Y-m-d h:i:s') . 
            ' Successfully Tested Api Endpoint: ' . $bold . $endpoint . $normal;
        $log .= "\n" . $grey . 'Result: ' . json_encode($result) . $normal . "\n";
        fwrite(STDOUT, $log . "\n");
    }
}
