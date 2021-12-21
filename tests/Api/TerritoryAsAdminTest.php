<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class TerritoryAsAdminTest extends TestCase
{

	/**
     * Territories
     * 
     * @endpoint: GET /v1/territories
     * @endpoint: GET /v1/territories/add
     * @endpoint: GET /v1/territories/{territoryId}
     * @endpoint: POST /v1/territories/{territoryId}/save
     *
     * @return void
     */
    public function testTerritoriesEndpoints()
    {
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        $adminToken = $signinResponse->getData()->token;
        $faker = \Faker\Factory::create();

        // Add a territory as Admin
        $territoryAddResponse = $this->json(
            'POST', '/v1/territories/add', [
                'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
            ], 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
            'GET /v1/territories (as Admin)', [
                'statusCode' => $territoriesResponse
                    ->status(),
                'total territories' => count(
                    $territoriesResponse->getOriginalContent()['data']
                ),
            ]
        );

        // Get 1 territory as Manager view (territories-all)
        $territory = \App\Models\Territory::first();
        $territoryResponse = $this->json(
            'GET', '/v1/territories-all/' . $territory['id'], [], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
            'GET /v1/territories-all/{territoryId} (as Admin)', [
                'statusCode' => $territoryResponse
                    ->status(),
            ]
        );

        // Get 1 territory as User
        $territory1Response = $this->json(
            'GET', '/v1/territories/' . $territory['id'], [], 
            [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
            'GET /v1/territories/{territoryId} (as Admin)', [
                'statusCode' => $territory1Response
                    ->status(), 'result' => $territory1Response
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $terrToEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        logResult(
            'POST /v1/territories/{territoryId}/save (as Admin)', [
                'statusCode' => $terrToEditResponse
                    ->status(),
                'result' => $terrToEditResponse
                    ->getOriginalContent()
            ]
        );

    }

    /**
     * Territory Addresses
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/add
     * @endpoint: POST /v1/territories/{territoryId}/addresses/edit/{addressId}
     * @endpoint: POST /v1/addresses/{addressId}/remove
     *
     * @return void
     */
    public function testTerritoryAddessesEndpoints()
    {
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        $adminToken = $signinResponse->getData()->token;
        $faker = \Faker\Factory::create();

        // Test Add address as Admin
        $territory = \App\Models\Territory::create([
            'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
        ]);
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $addressAddResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $address2AddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['territory_id', 'name', 'phone', 'address', 'street_id']
                ]
            );

        logResult(
            'POST /v1/territories/{territoryId}/addresses/add (with New Street) (as Admin)', [
                'statusCode' => $address2AddResponse
                    ->status(),
                'result' => $address2AddResponse
                    ->getOriginalContent()
            ]
        );

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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $addressEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $addressRemoveResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true,]);

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $addressRemove2Response->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/addresses/{addressId}/remove (Hard Delete) (as Admin)', [
                'statusCode' => $addressRemove2Response
                    ->status(),
                'result' => $addressRemove2Response
                    ->getOriginalContent()
            ]
        );
    }

    /**
     * Territories
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     *
     * @return void
     */
    public function testTerritoryAddessesNotesEndpoints()
    {
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        // Get admin token
        $adminToken = $signinResponse->getData()->token;

        $faker = \Faker\Factory::create();
        $territory = \App\Models\Territory::create([
            'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
        ]);
        $street = \App\Models\Street::create([
            'street' => $faker->streetName,
            'is_apt_building' => 0
        ]);
        $address = $territory->addresses()->create([
            'inactive' => 0, 
            'name' => $faker->name,
            'address' => $faker->randomNumber(3),
            'apt' => '',
            'lat' => 0.000000,
            'long' => 0.000000,
            'phone' => $faker->phoneNumber, 
            'street_id' => $street->id,
        ]);

        // Test Add notes as Admin
        $noteAddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $address['id'] . '/notes/add', [
                'note' => 'Note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $noteAddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['content', 'date', 'entity', 'entity_id', 'user_id', 'id']
                ]
            );

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $noteEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Admin)', [
                'statusCode' => $noteEditResponse->status(),
                'result' => $noteEditResponse->getOriginalContent()
            ]
        );
    }

    /**
     * Territories
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/phones/add
     * @endpoint: POST /v1/territories/{territoryId}/phones/edit/{phoneId}
     * @endpoint: POST /v1/territories/{territoryId}/phones/{phoneId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     *
     * @return void
     */
    public function testTerritoryAddessesPhonesEndpoints()
    {
        // Get default Admin
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        // Get admin token
        $adminToken = $signinResponse->getData()->token;

        $faker = \Faker\Factory::create();
        $territory = \App\Models\Territory::create([
            'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
        ]);
        $street = \App\Models\Street::create([
            'street' => $faker->streetName,
            'is_apt_building' => 0
        ]);
        $address = $territory->addresses()->create([
            'inactive' => 0, 
            'name' => $faker->name,
            'address' => $faker->randomNumber(3),
            'apt' => '',
            'lat' => 0.000000,
            'long' => 0.000000,
            'phone' => $faker->phoneNumber, 
            'street_id' => $street->id,
        ]);

        // Test Add phone as Admin
        $phoneAddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $address['id'] . '/phones/add', [
                'name' => 'Phone test',
                'number' => $faker->phoneNumber,
                'status' => 0,
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $phoneAddResponse->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => ['number', 'name', 'status', 'id']
                ]
            );

        logResult(
            'POST /v1/territories/{territoryId}/addresses/{addressId}/phones/add (as Admin)', [
                'statusCode' => $phoneAddResponse->status(),
                'result' => $phoneAddResponse->getOriginalContent()
            ]
        );

        // Test Edit phone as Admin
        $phoneData = $phoneAddResponse->getOriginalContent()['data'];
        $phoneEditResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/phones/edit/' . $phoneData['id'], [
                'name' => 'Phone test edited',
                'phone' => $faker->phoneNumber,
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $phoneEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/territories/{territoryId}/phones/edit/{phoneId} (as Admin)', [
                'statusCode' => $phoneEditResponse->status(),
                'result' => $phoneEditResponse->getOriginalContent()
            ]
        );

        // Test Add phone notes as Admin
        $phoneNoteAddResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/phones/' . $phoneData['id'] . '/notes/add', [
                'note' => 'Phone note test',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        logResult(
            'POST /v1/territories/{territoryId}/phones/{phoneId}/notes/add (as Admin)', [
                'statusCode' => $phoneNoteAddResponse->status(),
                'result' => $phoneNoteAddResponse->getOriginalContent()
            ]
        );

        // Test Edit Phone notes as Admin
        $noteData = $phoneNoteAddResponse->getOriginalContent()['data'];
        $phoneNoteEditResponse = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/notes/edit/' . $noteData['id'], [
                'note' => 'Note test 2 edited',
                'date' => date('Y-m-d'),
            ], [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $phoneNoteEditResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Admin)', [
                'statusCode' => $phoneNoteEditResponse->status(),
                'result' => $phoneNoteEditResponse->getOriginalContent()
            ]
        );

    }
}