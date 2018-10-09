<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTest extends TestCase {
	/**
	 * Api root
	 * @endpoint: GET /v1
	 *
	 * @return void
	 */
	public function test_Api_Root() {
		// Test Api root (/v1)
		$response = $this->call('GET', '/v1');
		$this->assertEquals(200, $response->status());
		$this->visit('/v1')
			->see('Territory Services API Version 1.0');
		$this->logEndpointTestResult('GET /v1', ['status' => $response->status() ]);

		// Test non-existent page to get 404
		$response2 = $this->call('GET', '/wrong-page-name');
		$this->assertEquals(404, $response2->status());
		$this->logEndpointTestResult('GET /wrong-page-name', ['status' => $response->status() ]);

	}

	/**
	 * Validate
	 * @endpoint: GET /v1/validate
	 *
	 * @return void
	 */
	public function test_Validate_Endpoint() {
		$response = $this->get('/v1/validate');
		$response->assertResponseStatus(200);
		$response->seeJsonEquals(['success' => true]);
		$this->logEndpointTestResult('GET /v1/validate', [
			'statusCode' => $response
				->response
				->status() , 
			'content' => $response
				->response
				->content() 
			]);
	}

	/**
	 * Signup/Signin
	 * @endpoint: POST /v1/signup
	 * @endpoint: POST /v1/signin
	 *
	 * @return void
	 */
	public function test_Signup_And_Signin_Endpoints() {
		$faker = Faker\Factory::create();
		$fakeEmail = $faker->email;
		$fakePswd = $faker->randomLetter;

		// Test Signup
		$signupResponse = $this->json('POST', '/v1/signup', ['email' => $fakeEmail, 'password' => $fakePswd]);
		$obj = $signupResponse
			->seeJsonStructure(['token'])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/signup', [
			'statusCode' => $signupResponse
				->response
				->status(), 
			'content' => $signupResponse
				->response
				->content() 
			]);

		// Test missing email
		$signupResponse2 = $this->json('POST', '/v1/signup', ['password' => $fakePswd]);
		$signupResponse2
			->seeJsonStructure(['error'])
			->assertResponseStatus(401);

		$this->logEndpointTestResult('POST /v1/signup (with missing data - email)', [
			'statusCode' => $signupResponse2
				->response
				->status(), 
			'content' => $signupResponse2
				->response
				->content() 
			]);

		// Test missing password
		$signupResponse3 = $this->json('POST', '/v1/signup', ['email' => $fakeEmail]);
		$signupResponse2
			->seeJsonStructure(['error'])
			->assertResponseStatus(401);

		$this->logEndpointTestResult('POST /v1/signup (with missing data - password)', [
			'statusCode' => $signupResponse3
				->response
				->status(), 
			'content' => $signupResponse3
				->response
				->content() 
			]);

		// Test Signin
		$signinResponse = $this->json('POST', '/v1/signin', ['email' => $fakeEmail, 'password' => $fakePswd]);
		$signinResponse
			->seeJsonStructure(['token'])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/signin', [
			'statusCode' => $signinResponse
				->response
				->status(), 
			'content' => $signinResponse
				->response
				->content() 
			]);

		// Test wrong email
		$signinResponse2 = $this->json('POST', '/v1/signin', ['email' => 'wrong-email@territory-api.com', 'password' => $fakePswd]);
		$signinResponse2->assertResponseStatus(401);
		$this->logEndpointTestResult('POST /v1/signin (with wrong email)', [
			'statusCode' => $signinResponse2
				->response
				->status() , 
			'content' => $signinResponse2
				->response
				->content() 
			]);

		// Test wrong password
		$signinResponse3 = $this->json('POST', '/v1/signin', ['email' => $fakeEmail, 'password' => 'wrong-password']);
		$signinResponse3->assertResponseStatus(401);
		$this->logEndpointTestResult('POST /v1/signin (with wrong password)', [
			'statusCode' => $signinResponse3
				->response
				->status(), 
			'content' => $signinResponse3
				->response
				->content() 
			]);
	}

	/**
	 * Auth User
	 * @endpoint: GET /v1/auth-user
	 *
	 * @return void
	 */
	public function test_AuthUser_Endpoint() {
		// Get admin User
		$signinResponse = $this->getAdminData();
		$this->assertEquals(200, $signinResponse->status());

		// Get admin token
		$token = $signinResponse->getData()->token;

		// Get admin User data
		$userResponse = $this->json('GET', '/v1/auth-user', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

		// Test admin User data
		$userResponse->seeJsonStructure(['data' => ['userId', 'email', 'userType']])
			->seeJson(['userType' => 'Admin'])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/auth-user', [
			'statusCode' => $userResponse
				->response
				->status(), 
			'content' => $userResponse
				->response
				->content() 
			]);

		// Test with wrong token
		$userResponse2 = $this->json('GET', '/v1/auth-user', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token . 'corrupted']);
		$userResponse2->see('Token is invalid')
			->assertResponseStatus(401);

		$this->logEndpointTestResult('GET /v1/auth-user (wrong token)', [
			'statusCode' => $userResponse2
				->response
				->status(), 
			'content' => $userResponse2
				->response
				->content() 
			]);

		// Test with missing token
		$userResponse3 = $this->json('GET', '/v1/auth-user');
		$userResponse3->see('Token is invalid')
			->assertResponseStatus(401);

		$this->logEndpointTestResult('GET /v1/auth-user (missing token)', [
			'statusCode' => $userResponse3
				->response
				->status(), 
			'content' => $userResponse3
				->response
				->content() 
			]);
	}

	/**
	 * Password Reset
	 * @endpoint: GET 
	 *
	 * @return void
	 */
	public function test_Password_Reset_Endpoint() {

		// TODO: Test Password Reset
		
	}

	/**
	 * Users and Publishers
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
	public function test_Users_And_Publishers_Endpoints() {
		// Test as Manager viewing Users
		$faker = Faker\Factory::create();
		$managerPass = '123456';
		$managerData = ['email' => $faker->email, 'password' => bcrypt($managerPass) , 'level' => 3];
		$managerUser = \App\User::create($managerData);
		$this->assertTrue($managerUser instanceof \App\User);
		$managerSigninResponse = $this->getUserData(['email' => $managerUser->email, 'password' => $managerPass]);
		$this->assertEquals(200, $managerSigninResponse->status());
		$managerToken = $managerSigninResponse->getData()->token;
		$managerUsersResponse = $this->json('GET', '/v1/users', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $managerToken]);

		// Should fail
		$managerUsersResponse->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(403);

		$this->logEndpointTestResult('GET /v1/users  (as Manager)', [
			'statusCode' => $managerUsersResponse
				->response
				->status() , 
			'totalUsers' => $managerUsersResponse
				->response
				->content() 
			]);

		// Test as Editor viewing Users
		$faker2 = Faker\Factory::create();
		$editorPass = '123456';
		$editorData = ['email' => $faker2->email, 'password' => bcrypt($editorPass) , 'level' => 2];
		$editorUser = \App\User::create($editorData);
		$this->assertTrue($editorUser instanceof \App\User);
		$editorSigninResponse = $this->getUserData(['email' => $editorUser->email, 'password' => $editorPass]);
		$this->assertEquals(200, $editorSigninResponse->status());
		$editorToken = $editorSigninResponse->getData()->token;
		$editorSigninResponse = $this->json('GET', '/v1/users', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $editorToken]);

		// Should fail
		$editorSigninResponse
			->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(403);

		$this->logEndpointTestResult('GET /v1/users (as Editor)', [
			'statusCode' => $editorSigninResponse
				->response
				->status(), 
			'totalUsers' => $editorSigninResponse
				->response
				->content() 
			]);

		// Test as Admin viewing Users
		$signinResponse = $this->getAdminData();
		$this->assertEquals(200, $signinResponse->status());
		$token = $signinResponse->getData()->token;
		$usersResponse = $this->json('GET', '/v1/users', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

		$usersResponse
			->seeJsonStructure(['data' => ['*' => ['userId', 'email', 'userType']]])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/users', [
			'statusCode' => $usersResponse
				->response
				->status() , 
			'totalUsers' => count($usersResponse
				->response
				->getOriginalContent() ['data']) 
			]);

		// Get one user (Editor) to edit $userToEdit
		$userToEdit = $editorUser;
		$userToEditResponse = $this->json('POST', '/v1/users/' . $userToEdit->id . '/save', ["userType" => 'NoteEditor', "email" => $userToEdit->email], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$userToEditResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->seeInDatabase('users', ["level" => \App\User::getType('NoteEditor') , 'id' => $userToEdit->id, 'email' => $userToEdit->email]);

		$this->logEndpointTestResult('POST /v1/users/{userId}/save', [
			'statusCode' => $userToEditResponse
				->response
				->status()
			]);

		// Update with Invalid email
		// Note: Api doesn't validate email
		/*
		$userToEditResponse2 = $this->json('POST', '/v1/users/' . $userToEdit->id . '/save', [
			"userType" => 'NoteEditor',
			"email" => 'Bad-Email.test'
		], [
			'Accept' => 'application/json',
		          'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $token
		]);
		
		var_dump(['$userToEditResponse2' => [
			// 'originalContent' => $userToEditResponse->response->getOriginalContent(),
			'content' => $userToEditResponse2->response->content(),
			'statusCode' => $userToEditResponse2->response->status(), 
		]]);
		
		$userToEditResponse2->assertResponseStatus(200)
			->seeJson([
		               'data' => true,
		           ]);
		
		$this->seeInDatabase('users', ["level" => \App\User::getType('NoteEditor'), 'id' => $userToEdit->id, 'email' => $userToEdit->email]);
		$this->logEndpointTestResult('POST /v1/users/{userId}/save (with Invalid email)', [
			'statusCode' => $userToEditResponse2->response->status(), 
		]);
		*/

		// Update with incorrect Type
		// Note: Api doesn't fail with incorrect Type
		/*
		$userToEditResponse3 = $this->json('POST', '/v1/users/' . $userToEdit->id . '/save', [
			"userType" => 'WrongType',
			"email" => $userToEdit->email
		], [
			'Accept' => 'application/json',
		          'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $token
		]);
		
		var_dump(['$userToEditResponse3' => [
			'content' => $userToEditResponse3->response->content(),
			'statusCode' => $userToEditResponse3->response->status(), 
		]]);
		
		$userToEditResponse2->assertResponseStatus(200)
			->seeJson([
		               'data' => true,
		           ]);
		
		$this->seeInDatabase('users', ["level" => \App\User::getType('NoteEditor'), 'id' => $userToEdit->id, 'email' => $userToEdit->email]);
		$this->logEndpointTestResult('POST /v1/users/{userId}/save (with Invalid email)', [
			'statusCode' => $userToEditResponse2->response->status(), 
		]);
		*/

		// Createa  Publisher 
		$faker = Faker\Factory::create();
		$firstName = $faker->firstName;
		$lastName = $faker->lastName;
		$publisherCreateResponse = $this->json('POST', '/v1/publishers/add', ["firstName" => $firstName, "lastName" => $lastName], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$publisherCreateResponse
			->seeJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
			->seeJson(["firstName" => $firstName, "lastName" => $lastName, ])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/publishers/add', [
			'statusCode' => $publisherCreateResponse
				->response
				->status(), 
			'content' => $publisherCreateResponse
				->response
				->content() 
			]);

		// Attach Publisher to User
		$createdPublisher = $publisherCreateResponse
			->response
			->getOriginalContent()['data'];
		$userToAttachPublisherGetResponse = $this->json('POST', '/v1/publishers/attach-user', [
			"publisherId" => $createdPublisher['publisherId'], "userId" => $userToEdit['userId'],
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$userToAttachPublisherGetResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		// Check $createdPublisher data in db
		$this->seeInDatabase('publishers', ['id' => $createdPublisher['publisherId'], 'user_id' => $userToEdit['userId']]);
		$this->logEndpointTestResult('POST /v1/publishers/attach-user', [
			'statusCode' => $userToAttachPublisherGetResponse
				->response
				->status() 
			]);

		// Update Publisher
		$createdPublisher['lastName'] = $createdPublisher['lastName'] . '-Editted';
		$createdPublisherUpdatedResponse = $this->json('POST', '/v1/publishers/' . $createdPublisher['publisherId'] . '/save', ['firstName' => $createdPublisher['firstName'], 'lastName' => $createdPublisher['lastName']], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$createdPublisherUpdatedResponse
			->seeJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
			->seeJson(['lastName' => $createdPublisher['lastName']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/publishers/{publisherId}/save', [
			'statusCode' => $createdPublisherUpdatedResponse
				->response
				->status(), 
			'content' => $createdPublisherUpdatedResponse
				->response
				->content() 
			]);

		// Get Updated Publisher
		$updatedPublisherResponse = $this->json('GET', '/v1/publishers/' . $createdPublisher['publisherId'], [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$updatedPublisherResponse
			->seeJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
			->seeJson(['lastName' => $createdPublisher['lastName']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/publishers/{publisherId}', [
			'statusCode' => $updatedPublisherResponse
				->response
				->status(), 
			'content' => $updatedPublisherResponse
				->response
				->content() 
			]);

		// Store $publisherToDelete and $userToDelete here, to delete later 
		$publisherToDelete = $updatedPublisherResponse
			->response
			->getOriginalContent() ['data'];
		$userToDelete = $userToEdit;

		// Get all Publishers
		$allPublishersResponse = $this->json('GET', '/v1/publishers', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$allPublishersResponse
			->seeJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/publishers', [
			'statusCode' => $allPublishersResponse
				->response
				->status(), 
			'totalPublishers' => count($allPublishersResponse
				->response
				->getOriginalContent() ['data']) 
			]);

		// Create another Publisher without User
		$faker2 = Faker\Factory::create();
		$firstName2 = $faker2->firstName;
		$lastName2 = $faker2->lastName . ' NoUser';
		$anotherPublisherCreateResponse = $this->json('POST', '/v1/publishers/add', [
			"firstName" => $firstName2, "lastName" => $lastName2
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);
		$anotherPublisherCreateResponse
			->seeJsonStructure(['data' => ['publisherId', 'firstName', 'lastName']])
			->seeJson(['firstName' => $firstName2, 'lastName' => $lastName2])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/publishers/add', [
			'statusCode' => $anotherPublisherCreateResponse
				->response
				->status()
			]);

		// Get all Publishers with no User attached
		$allPublishersWithoutUserResponse = $this->json('POST', '/v1/publishers/filter', ['userId' => null], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$allPublishersWithoutUserResponse
			->seeJsonStructure(['data' => ['*' => ['publisherId', 'firstName', 'lastName']]])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/publishers/filter', [
			'statusCode' => $allPublishersWithoutUserResponse
				->response
				->status(), 
			'totalPublishers' => count($allPublishersWithoutUserResponse
				->response
				->getOriginalContent() ['data']) 
			]);

		// Delete created publisher
		$deleteCreatedPublisherResponse = $this->json('POST', '/v1/publishers/' . $publisherToDelete['publisherId'] . '/delete', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$deleteCreatedPublisherResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/publishers/{publisherId}/delete', [
			'statusCode' => $anotherPublisherCreateResponse
				->response
				->status()
			]);

		// Delete a user
		$deleteCreatedUserResponse = $this->json('POST', '/v1/users/' . $userToDelete->id . '/delete', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$deleteCreatedUserResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/users/{userId}/delete', [
			'statusCode' => $deleteCreatedUserResponse
				->response
				->status()
			]);

	}

	/**
	 * Territories
	 * @endpoint: GET /v1/territories
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
	public function test_Territories_Endpoints() {
		// Get default Admin
		$signinResponse = $this->getAdminData();
		$this->assertEquals(200, $signinResponse->status());

		// Get admin token
		$token = $signinResponse->getData()->token;

		// Get territories
		$territoriesResponse = $this->json('GET', '/v1/territories', [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);

		// Test territories data
		$territoriesResponse
			->seeJsonStructure(['data' => ['*' => ['territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses']]])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/territories (as Admin)', [
			'statusCode' => $territoriesResponse
				->response
				->status(),
			'total territories' => count($territoriesResponse
				->response
				->getOriginalContent() ['data']) 
			]);

		// Get 1 territory as Manager view (territories-all)
		$territory = \App\Territory::first();
		$territoryResponse = $this->json('GET', '/v1/territories-all/' . $territory['id'], [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$territoryResponse
			->seeJsonStructure(['data' => ['territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/territories-all/{territoryId} (as Admin)', [
			'statusCode' => $territoryResponse
				->response
				->status(), 
			]);

		// Get 1 territory as User
		$territory1Response = $this->json('GET', '/v1/territories/' . $territory['id'], [], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$territory1Response
			->seeJsonStructure(['data' => ['territoryId', 'number', 'publisherId', 'location', 'cityState', 'addresses']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('GET /v1/territories/{territoryId} (as Admin)', [
			'statusCode' => $territory1Response
				->response
				->status() , 'result' => $territory1Response
			]);

		// Update territory
		$faker = Faker\Factory::create();
		$city = $faker->city;
		$terrToEditResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/save', [
			"cityState" => $city], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);
		
		$terrToEditResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/save (as Admin)', [
			'statusCode' => $terrToEditResponse
				->response
				->status(), 
			'result' => $terrToEditResponse
				->response
				->getOriginalContent() 
			]);

		// Test Add address as Admin
		$name = $faker->name;
		$phone = $faker->phoneNumber;

		$addressAddResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', ['inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '300', 'apt' => '', 'phone' => $phone, 'streetId' => 1, ], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$addressAddResponse
			->seeJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (as Admin)', [
			'statusCode' => $addressAddResponse
				->response
				->status(), 
			'result' => $addressAddResponse
				->response
				->getOriginalContent() 
			]);

		// store $addressData for later use
		$addressData = $addressAddResponse
			->response
			->getOriginalContent() ['data'];	

		// Test Add address with new Street as Admin
		$street = $faker->streetName;
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$address2AddResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', ['inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '301', 'apt' => '', 'phone' => $phone, 'street' => [0 => (object)['street' => $street, 'isAptBuilding' => 0]], ], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$address2AddResponse
			->seeJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (with New Street) (as Admin)', [
			'statusCode' => $address2AddResponse
				->response
				->status(), 
			'result' => $address2AddResponse
				->response
				->getOriginalContent() 
			]);
		
		// store $address2Data for later use
		$address2Data = $address2AddResponse->response->getOriginalContent()['data'];

		// Test Edit address as Admin
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressEditResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData['id'], ['name' => $name, 'address' => '302', 'phone' => $phone, 'streetId' => $addressData['street_id'], ], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$addressEditResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Admin)', [
			'statusCode' => $addressEditResponse
				->response
				->status(),
			'result' => $addressEditResponse
				->response
				->getOriginalContent()
			]);

		// Test remove address as Admin (Soft Delete = set inactive=1)
		$addressRemoveResponse = $this->json('POST', '/v1/addresses/' . $addressData['id'] . '/remove', [
			'note' => 'Reason for delete is test'
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$addressRemoveResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as Admin)', [
			'statusCode' => $addressRemoveResponse
				->response
				->status(),
			'result' => $addressRemoveResponse
				->response
				->getOriginalContent() 
			]);

		// Test remove address as Admin (Hard Delete)
		$addressRemove2Response = $this->json('POST', '/v1/addresses/' . $addressData['id'] . '/remove', ['note' => 'Reason for delete is test', 'delete' => true], ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token]);
		$addressRemove2Response
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/addresses/{addressId}/remove (Hard Delete) (as Admin)', [
			'statusCode' => $addressRemove2Response
				->response
				->status(), 
			'result' => $addressRemove2Response
				->response
				->getOriginalContent()
			]);

		// Test Add notes as Admin
		$noteAddResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $address2Data['id'] . '/notes/add', [
			'note' => 'Note test',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $token
		]);
		
		$noteAddResponse
			->seeJsonStructure(['data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Admin)', [
			'statusCode' => $noteAddResponse->response->status(), 
			'result' => $noteAddResponse->response->getOriginalContent()
		]);

		// Test Edit notes as Admin
		$noteData = $noteAddResponse->response->getOriginalContent()['data'];
		$noteEditResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData['id'], [
			'note' => 'Note test edited',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $token
		]);
		
		$noteEditResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Admin)', [
			'statusCode' => $noteEditResponse->response->status(), 
			'result' => $noteEditResponse->response->getOriginalContent()
		]);


		// Test as Manager
		$managerPass = '123456';
		$managerData = ['email' => $faker->email, 'password' => bcrypt($managerPass) , 'level' => 3];
		$managerUser = \App\User::create($managerData);
		$this->assertTrue($managerUser instanceof \App\User);
		$managerSigninResponse = $this->getUserData(['email' => $managerUser->email, 'password' => $managerPass]);
		$this->assertEquals(200, $managerSigninResponse->status());
		$managerToken = $managerSigninResponse->getData()->token;


		// Add Address to Territory as Manager
		$street = $faker->streetName;
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressAddResponse2 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
			'inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '400', 'apt' => '', 'phone' => $phone, 'streetId' => 1
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $managerToken
		]);
		$addressAddResponse2
			->seeJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (as Manager)', [
			'statusCode' => $addressAddResponse2
				->response
				->status(), 
			'result' => $addressAddResponse2
				->response
				->getOriginalContent() 
			]);


		// Edit Address as Manager
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressData2 = $addressAddResponse2->response->getOriginalContent()['data'];
		$addressEditResponse2 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData2['id'], [
			'name' => $name, 'address' => '402', 'phone' => $phone, 'streetId' => $addressData2['street_id']
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $managerToken
		]);
		$addressEditResponse2
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Manager)', [
			'statusCode' => $addressEditResponse2
				->response
				->status(),
			'result' => $addressEditResponse2
				->response
				->getOriginalContent()
			]);


		// Add Note as Manager
		$noteAddResponse2 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData2['id'] . '/notes/add', [
			'note' => 'Note test',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $managerToken
		]);
		
		$noteAddResponse2
			->seeJsonStructure(['data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Manager)', [
			'statusCode' => $noteAddResponse2->response->status(), 
			'result' => $noteAddResponse2->response->getOriginalContent()
		]);


		// Edit Note as Manager
		$noteData2 = $noteAddResponse2->response->getOriginalContent()['data'];
		$noteEditResponse2 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData2['id'], [
			'note' => 'Note test edited',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $managerToken
		]);
		
		$noteEditResponse2
			->seeJson(['data' => true])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Manager)', [
			'statusCode' => $noteEditResponse2->response->status(), 
			'result' => $noteEditResponse2->response->getOriginalContent()
		]);


		// Test remove address as Manager (Soft Delete = set inactive=1)
		$addressRemoveResponse2 = $this->json('POST', '/v1/addresses/' . $addressData2['id'] . '/remove', [
			'note' => 'Reason for delete is test'
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $managerToken
		]);

		$addressRemoveResponse2
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as Manager)', [
			'statusCode' => $addressRemoveResponse2
				->response
				->status(),
			'result' => $addressRemoveResponse2
				->response
				->getOriginalContent() 
			]);


		// Test remove address as Admin (Hard Delete)
		$addressRemove2Response2 = $this->json('POST', '/v1/addresses/' . $addressData2['id'] . '/remove', [
			'note' => 'Reason for delete is test', 'delete' => true
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $managerToken
		]);
		$addressRemove2Response2
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/addresses/{addressId}/remove (Hard Delete) (as Manager)', [
			'statusCode' => $addressRemove2Response2
				->response
				->status(), 
			'result' => $addressRemove2Response2
				->response
				->getOriginalContent()
			]);

		
		// Unassign Territory $territory
		$terrUnassignResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/save', [
			"publisherId" => null, "date" => date('Y-m-d')
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);
		$terrUnassignResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/save (Unassign)', [
			'statusCode' => $terrUnassignResponse
				->response
				->status(), 
			'result' => $terrUnassignResponse
				->response
				->getOriginalContent()
			]);




		// Test as Editor
		$editorPass = '123456';
		$editorData = ['email' => $faker->email, 'password' => bcrypt($editorPass) , 'level' => 2];
		$editorUser = \App\User::create($editorData);
		$this->assertTrue($editorUser instanceof \App\User);
		$editorSigninResponse = $this->getUserData(['email' => $editorUser->email, 'password' => $editorPass]);
		$this->assertEquals(200, $editorSigninResponse->status());
		$editorToken = $editorSigninResponse->getData()->token;
		

		// Create a Publisher Assign User to Publisher
		$firstName3 = $faker->firstName;
		$lastName3 = $faker->lastName;
		$editorPublisher = \App\Publisher::create(["first_name" => $firstName3, "last_name" => $lastName3]);
		// var_dump(['$editorPublisher' => $editorPublisher->toArray(), 'pub data' => ["firstName" => $firstName3, "lastName" => $lastName3]]);

		$editorUserAttachPublisherResponse = $this->json('POST', '/v1/publishers/attach-user', [
			"publisherId" => $editorPublisher->id, "userId" => $editorUser->id,
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$editorUserAttachPublisherResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->seeInDatabase('publishers', ['id' => $editorPublisher->id, 'user_id' => $editorUser->id]);
		$this->logEndpointTestResult('POST /v1/publishers/attach-user', [
			'statusCode' => $editorUserAttachPublisherResponse
				->response
				->status(), 
			'result' => $editorUserAttachPublisherResponse
				->response
				->content()
			]);	 


		// Add Address to territory as Editor (Unassigned)
		$street = $faker->streetName;
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressAddResponse3 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
			'inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '500', 'apt' => '', 'phone' => $phone, 'streetId' => 1
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $editorToken
		]);

		$addressAddResponse3
			->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (as Editor Unassigned)', [
			'statusCode' => $addressAddResponse3
				->response
				->status(),
			'result' => $addressAddResponse3
				->response
				->getOriginalContent() 
			]);	 


		// Assign Territory $territory to Publisher
		$terrUnassignResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/save', [
			"publisherId" => $editorPublisher->id, "date" => date('Y-m-d')
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$this->seeInDatabase('territories', ['id' => $territory['id'], 'publisher_id' => $editorPublisher->id]);
		$terrUnassignResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/save (Assign)', [
			'statusCode' => $terrUnassignResponse
				->response
				->status(), 
			'result' => $terrUnassignResponse
				->response
				->getOriginalContent()
			]);
	

		
		// Try again: Add Address to territory as Editor (Assigned)
		$street = $faker->streetName;
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressAddResponse4 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
			'inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '500', 'apt' => '', 'phone' => $phone, 'streetId' => 1
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $editorToken
		]);

		$addressAddResponse4
			->seeJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (as Editor Assigned)', [
			'statusCode' => $addressAddResponse4
				->response
				->status(),
			'result' => $addressAddResponse4
				->response
				->getOriginalContent() 
			]);	 	



		// Edit Address with Publisher->User as Editor
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressData3 = $addressAddResponse4->response->getOriginalContent()['data'];
		$addressEditResponse3 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/edit/' . $addressData3['id'], [
			'name' => $name, 'address' => '502', 'phone' => $phone, 'streetId' => $addressData3['street_id']
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $editorToken
		]);
		$addressEditResponse3
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/edit/{addressId} (as Editor)', [
			'statusCode' => $addressEditResponse3
				->response
				->status(),
			'result' => $addressEditResponse3
				->response
				->getOriginalContent()
			]);



		// Add notes to Address with Publisher->User as Editor
		$noteAddResponse3 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData3['id'] . '/notes/add', [
			'note' => 'Note test',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $editorToken
		]);
		
		$noteAddResponse3
			->seeJsonStructure(['data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as Editor)', [
			'statusCode' => $noteAddResponse3->response->status(), 
			'result' => $noteAddResponse3->response->getOriginalContent()
		]);



		// Edit Note with Publisher->User as Editor
		$noteData3 = $noteAddResponse3->response->getOriginalContent()['data'];
		$noteEditResponse4 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData3['id'], [
			'note' => 'Note test edited',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $editorToken
		]);
		
		$noteEditResponse4
			->seeJson(['data' => true])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Editor)', [
			'statusCode' => $noteEditResponse4->response->status(), 
			'result' => $noteEditResponse4->response->getOriginalContent()
		]);


 		

		// Test as NoteEditor
		$noteEditorPass = '123456';
		$noteEditorData = ['email' => $faker->email, 'password' => bcrypt($noteEditorPass) , 'level' => 5];
		$noteEditorUser = \App\User::create($noteEditorData);
		$this->assertTrue($noteEditorUser instanceof \App\User);
		$noteEditorSigninResponse = $this->getUserData(['email' => $noteEditorUser->email, 'password' => $noteEditorPass]);
		$this->assertEquals(200, $noteEditorSigninResponse->status());
		$noteEditorToken = $noteEditorSigninResponse->getData()->token;


		// Create a Publisher Assign User to Publisher
		$firstName4 = $faker->firstName;
		$lastName4 = $faker->lastName;
		$noteEditorPublisher = \App\Publisher::create(["first_name" => $firstName4, "last_name" => $lastName4]);
		$noteEditorUserAttachPublisherResponse = $this->json('POST', '/v1/publishers/attach-user', [
			"publisherId" => $noteEditorPublisher->id, "userId" => $noteEditorUser->id,
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$noteEditorUserAttachPublisherResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->seeInDatabase('publishers', ['id' => $noteEditorPublisher->id, 'user_id' => $noteEditorUser->id]);
		$this->logEndpointTestResult('POST /v1/publishers/attach-user (as NoteEditor)', [
			'statusCode' => $noteEditorUserAttachPublisherResponse
				->response
				->status(), 
			'result' => $noteEditorUserAttachPublisherResponse
				->response
				->content()
			]);	 


		
		// Assign Territory $territory to Publisher
		$terrUnassignResponse = $this->json('POST', '/v1/territories/' . $territory['id'] . '/save', [
			"publisherId" => $noteEditorPublisher->id, "date" => date('Y-m-d')
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $token
		]);

		$this->seeInDatabase('territories', ['id' => $territory['id'], 'publisher_id' => $noteEditorPublisher->id]);
		$terrUnassignResponse
			->seeJson(['data' => true])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/save (Assign)', [
			'statusCode' => $terrUnassignResponse
				->response
				->status(), 
			'result' => $terrUnassignResponse
				->response
				->getOriginalContent()
			]);
	

		
		// Try Add Address to territory as NoteEditor (Assigned)
		$street = $faker->streetName;
		$name = $faker->name;
		$phone = $faker->phoneNumber;
		$addressAddResponse5 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
			'inActive' => false, 'isApt' => false, 'name' => $name, 'address' => '500', 'apt' => '', 'phone' => $phone, 'streetId' => 1
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $noteEditorToken
		]);

		// Friendly fail
		$addressAddResponse5
			->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(200);

		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/add (as NoteEditor Assigned)', [
			'statusCode' => $addressAddResponse5
				->response
				->status(),
			'result' => $addressAddResponse5
				->response
				->getOriginalContent() 
			]);	



		// TODO: Try Delete Address as NoteEditor 
		$addressRemoveResponse4 = $this->json('POST', '/v1/addresses/' . $addressData3['id'] . '/remove', [
			'note' => 'Reason for delete is test'
		], [
			'Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $noteEditorToken
		]);

		$addressRemoveResponse4
			->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(403);

		$this->logEndpointTestResult('POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as NoteEditor)', [
			'statusCode' => $addressRemoveResponse4
				->response->status(),
			'result' => $addressRemoveResponse4
				->response->content() 
			]);


		// Add notes to Address with Publisher->User as NoteEditor
		$noteAddResponse4 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressData3['id'] . '/notes/add', [
			'note' => 'Note test',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $noteEditorToken
		]);
		
		$noteAddResponse4
			->seeJsonStructure(['data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add (as NoteEditor)', [
			'statusCode' => $noteAddResponse4->response->status(), 
			'result' => $noteAddResponse4->response->getOriginalContent()
		]);


		
		// Edit notes as Non Note->User as Editor
		$noteData4 = $noteAddResponse4->response->getOriginalContent()['data'];
		$noteEditResponse5 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData4['id'], [
			'note' => 'Note test edited',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $editorToken
		]);
		
		// Should fail, since not Note->User
		$noteEditResponse5
			->seeJson(['error' => 'Method not allowed'])
			->assertResponseStatus(403);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Editor Non-User)', [
			'statusCode' => $noteEditResponse5->response->status(), 
			'result' => $noteEditResponse5->response->content()
		]);		



		// Edit Note with Publisher->User as NoteEditor
		$noteEditResponse6 = $this->json('POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData4['id'], [
			'note' => 'Note test edited',
			'date' => date('Y-m-d'),
		], [
			'Accept' => 'application/json',
		  'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . $noteEditorToken
		]);
		
		$noteEditResponse6
			->seeJson(['data' => true])
			->assertResponseStatus(200);
		 
		$this->logEndpointTestResult('POST /v1/territories/{territoryId}/notes/edit/{noteId} (as NoteEditor)', [
			'statusCode' => $noteEditResponse6->response->status(), 
			'result' => $noteEditResponse6->response->content()
		]);		
		
	}
 
	
	/**
	 * Private methods
	 */
	protected function getAdminData() {
		return $this->getUserData(['email' => env('APP_ADMIN_EMAIL') , 'password' => env('APP_ADMIN_PASSWORD') ]);
	}

	protected function getUserData($creds = []) {
		return empty($creds) ? null : $this->call('POST', '/v1/signin', $creds);
	}

	protected function logEndpointTestResult($endpoint, $result = []) {
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

		$log = "\n" . $blue . date('Y-m-d h:i:s') . ' Successfully Tested Api Endpoint: ' . $bold . $endpoint . $normal;
		$log .= "\n" . $grey . 'Result: ' . json_encode($result) . $normal . "\n";
		fwrite(STDOUT, $log . "\n");

	}
}

