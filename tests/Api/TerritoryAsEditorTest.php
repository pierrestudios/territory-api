<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class TerritoryAsEditorTest extends TestCase
{

    /**
     * Territory Addresses, Notes as Editor
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/add
     * @endpoint: POST /v1/territories/{territoryId}/addresses/edit/{addressId}
     * @endpoint: POST /v1/addresses/{addressId}/remove
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     * 
     * @return void
     */
    public function testTerritoryEndpointsAsEditor()
    {
        $faker = \Faker\Factory::create();
        $editorPass = '123456';
        $editorData = ['email' => $faker->email, 'password' => bcrypt($editorPass), 'level' => 2];
        $editorUser = \App\Models\User::create($editorData);
        $this->assertTrue($editorUser instanceof \App\Models\User);
        $editorSigninResponse = getUserData(
            [
                'email' => $editorUser->email, 'password' => $editorPass
            ], $this
        );
        $this->assertEquals(200, $editorSigninResponse->status());
        $editorToken = $editorSigninResponse->getData()->token;

        $signinResponse = getAdminData($this);
        $this->assertEquals(200, $signinResponse->status());

        $adminToken = $signinResponse->getData()->token;

        $territory = \App\Models\Territory::create([
            'number' => $faker->randomNumber(3), 'location' => $faker->streetName, 'assigned_date' => date('Y-m-d')
        ]);

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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $editorUserAttachPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->assertDatabaseHas('publishers', ['id' => $editorPublisher->id, 'user_id' => $editorUser->id]);
        logResult(
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

        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $this->assertDatabaseHas(
            'territories', [
                'id' => $territory['id'], 'publisher_id' => $editorPublisher->id
            ]
        );
        $terrUnassignResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        logResult(
            'POST /v1/territories/{territoryId}/save (Assign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );

        // Try again: Add Address to territory as Editor (Assigned)
        $name = $faker->name;
        $phone = $faker->phoneNumber;
        $streetData = \App\Models\Street::create([
            'street' => $faker->streetName,
            'is_apt_building' => 0
        ]);
        $addressAddResponse4 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $name, 
                'address' => '500', 
                'apt' => '', 
                'phone' => $phone, 
                'streetId' => $streetData->id
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $editorToken
            ]
        );

        $addressAddResponse4->assertStatus(200)
            ->assertJsonStructure(['data' => ['territory_id', 'name', 'phone', 'address', 'street_id']]);

        logResult(
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

        logResult(
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

        logResult(
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

        logResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as Editor)', [
                'statusCode' => $noteEditResponse4->status(),
                'result' => $noteEditResponse4->getOriginalContent()
            ]
        );
    }
}