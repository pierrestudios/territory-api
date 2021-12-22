<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class TerritoryAsManagerTest extends TestCase
{

    /**
     * Territory Addresses, Notes as Manager
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/add
     * @endpoint: POST /v1/territories/{territoryId}/addresses/edit/{addressId}
     * @endpoint: POST /v1/addresses/{addressId}/remove
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     *
     * @return void
     */
    public function testTerritoryEndpointsAsManager()
    {
        $faker = \Faker\Factory::create();
        $manager = createManager();
		$managerPass = $manager->password;
		$managerUser = $manager->user;
        $this->assertTrue($managerUser instanceof \App\Models\User);

        $managerSigninResponse = getUserData(['email' => $managerUser->email, 'password' => $managerPass], $this);
        $this->assertEquals(200, $managerSigninResponse->status());

        $managerToken = $managerSigninResponse->getData()->token;
        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        $adminToken = $signinResponse->getData()->token;

        // Add Address to Territory as Manager
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $territory = \App\Models\Territory::create([
            'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
        ]);
        $streetData = \App\Models\Street::create([
            'street' => $faker->streetName,
            'is_apt_building' => 0
        ]);
        $addressAddResponse2 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '400', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => $streetData->id
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

        logResult(
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

        logResult(
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

        logResult(
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

        logResult(
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

        logResult(
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

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );
        $terrUnassignResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/territories/{territoryId}/save (Unassign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );
    }
}