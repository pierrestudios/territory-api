<?php

namespace Tests\Api;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\TestCase;
use App\Models\User;

class TerritoryAsNoteEditorTest extends TestCase
{

    /**
     * Territory Addresses, Notes as NoteEditor
     * 
     * @endpoint: POST /v1/territories/{territoryId}/addresses/{addressId}/notes/add
     * @endpoint: POST /v1/territories/{territoryId}/notes/edit/{noteId}
     *
     * @return void
     */
    public function testTerritoryEndpointsAsNoteEditor()
    {
        $faker = \Faker\Factory::create();
		$noteEditor = createNoteEditor();
        $noteEditorPass = $noteEditor->password;
		$noteEditorUser = $noteEditor->user;
        $this->assertTrue($noteEditorUser instanceof \App\Models\User);

		$noteEditorSigninResponse = getUserData(
            [
                'email' => $noteEditorUser->email, 'password' => $noteEditorPass
            ], $this
        );
        $this->assertEquals(200, $noteEditorSigninResponse->status());

		$noteEditorToken = $noteEditorSigninResponse->getData()->token;

        $editor = createEditor();
        $editorPass = $editor->password;
		$editorUser = $editor->user;
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
        $noteEditorPublisher = \App\Models\Publisher::create(
            [
                "first_name" => $faker->firstName, "last_name" => $faker->lastName
            ]
        );
        $noteEditorUserAttachPublisherResponse = $this->json(
            'POST', '/v1/publishers/attach-user', [
                "publisherId" => $noteEditorPublisher->id, "userId" => $noteEditorUser->id,
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $adminToken
            ]
        );

        $noteEditorUserAttachPublisherResponse->assertStatus(200)
            ->assertJsonFragment(['data' => true]);

        $this->assertDatabaseHas('publishers', ['id' => $noteEditorPublisher->id, 'user_id' => $noteEditorUser->id]);
        logResult(
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
                'Authorization' => 'Bearer ' . $adminToken
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

        logResult(
            'POST /v1/territories/{territoryId}/save (Assign)', [
                'statusCode' => $terrUnassignResponse
                    ->status(),
                'result' => $terrUnassignResponse
                    ->getOriginalContent()
            ]
        );

        // Try Add Address to territory as NoteEditor (Assigned)
        $addressAddResponse5 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/add', [
                'inActive' => false, 
                'isApt' => false, 
                'name' => $faker->name, 
                'address' => '500', 
                'apt' => '', 
                'phone' => $faker->phoneNumber, 
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

        logResult(
            'POST /v1/territories/{territoryId}/addresses/add (as NoteEditor Assigned)', [
                'statusCode' => $addressAddResponse5
                    ->status(),
                'result' => $addressAddResponse5
                    ->getOriginalContent()
            ]
        );

        // Try Delete Address as NoteEditor 
        $street = \App\Models\Street::create([
            'street' => $faker->streetName,
            'is_apt_building' => 0
        ]);
        $addressToDelete2 = $territory->addresses()->create([
            'inactive' => 0, 
            'name' => $faker->name,
            'address' => $faker->randomNumber(3),
            'apt' => '',
            'lat' => 0.000000,
            'long' => 0.000000,
            'phone' => $faker->phoneNumber, 
            'street_id' => $street->id,
        ]);
        $addressRemoveResponse4 = $this->json(
            'POST', '/v1/addresses/' . $addressToDelete2['id'] . '/remove', [
                'note' => 'Reason for delete is test'
            ], [
                'Accept' => 'application/json', 
                'Content-Type' => 'application/json', 
                'Authorization' => 'Bearer ' . $noteEditorToken
            ]
        );

        $addressRemoveResponse4->assertStatus(403)
            ->assertJsonFragment(['error' => 'Method not allowed']);

        logResult(
            'POST /v1/addresses/{addressId}/remove (Soft Delete = set inactive=1) (as NoteEditor)', [
                'statusCode' => $addressRemoveResponse4
                    ->status(),
                'result' => $addressRemoveResponse4
                    ->content()
            ]
        );

        // Add notes to Address with Publisher->User as NoteEditor
        $noteAddResponse4 = $this->json(
            'POST', '/v1/territories/' . $territory['id'] . '/addresses/' . $addressToDelete2['id'] . '/notes/add', [
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

        logResult(
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

        logResult(
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

        logResult(
            'POST /v1/territories/{territoryId}/notes/edit/{noteId} (as NoteEditor)', [
                'statusCode' => $noteEditResponse6->status(),
                'result' => $noteEditResponse6->content()
            ]
        );
    }
}