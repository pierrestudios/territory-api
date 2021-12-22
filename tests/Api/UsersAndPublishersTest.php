<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class UsersAndPublishersTest extends TestCase
{

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
        $manager = createManager();
		$managerPass = $manager->password;
		$managerUser = $manager->user;
        $this->assertTrue($managerUser instanceof \App\Models\User);

        $managerSigninResponse = getUserData(['email' => $managerUser->email, 'password' => $managerPass], $this);
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

        logResult(
            'GET /v1/users  (as Manager)', [
                'statusCode' => $managerUsersResponse
                    ->status(),
                'totalUsers' => $managerUsersResponse
                    ->content()
            ]
        );

        // Test as Editor viewing Users
        $editor = createEditor();
        $editorPass = $editor->password;
		$editorUser = $editor->user;
        $this->assertTrue($editorUser instanceof \App\Models\User);

		$editorSigninResponse = getUserData(['email' => $editorUser->email, 'password' => $editorPass], $this);
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

        logResult(
            'GET /v1/users (as Editor)', [
                'statusCode' => $editorSigninResponse
                    ->status(),
                'totalUsers' => $editorSigninResponse
                    ->content()
            ]
        );

        // Test as Admin viewing Users
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

		$adminToken = $signinResponse->getData()->token;
        $usersResponse = $this->withHeaders([
				'Accept' => 'application/json', 
				'Content-Type' => 'application/json', 
				'Authorization' => 'Bearer ' . $adminToken
			])->json(
				'GET', '/v1/users',
			);

        $usersResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['userId', 'email', 'userType']]]);

        logResult(
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
            'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $publisherCreateResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(["firstName" => $firstName, "lastName" => $lastName,]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
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
        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $createdPublisherUpdatedResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['lastName' => $createdPublisher['lastName'],]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $updatedPublisherResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['lastName' => $createdPublisher['lastName'],]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $allPublishersResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]]);

        logResult(
            'GET /v1/publishers', [
                'statusCode' => $allPublishersResponse
                    ->status(),
                'totalPublishers' => count(
                    $allPublishersResponse->getOriginalContent()['data']
                )
            ]
        );

        // Create another Publisher without User
        $faker = \Faker\Factory::create();
        $firstName2 = $faker->firstName;
        $lastName2 = $faker->lastName . ' NoUser';
        $anotherPublisherCreateResponse = $this->json(
            'POST', '/v1/publishers/add', [
                "firstName" => $firstName2, 
                "lastName" => $lastName2
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $anotherPublisherCreateResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
            ->assertJsonFragment(['firstName' => $firstName2, 'lastName' => $lastName2]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $allPublishersWithoutUserResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $deleteCreatedPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $deleteCreatedUserResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        logResult(
            'POST /v1/users/{userId}/delete', [
                'statusCode' => $deleteCreatedUserResponse
                    ->status()
            ]
        );
    }
}