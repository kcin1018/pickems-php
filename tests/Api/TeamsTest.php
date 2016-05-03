<?php

namespace PickemsTest\Api;

use Exception;
use Pickems\Team;
use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamsTest extends TestCase
{
    use DatabaseMigrations;
    const API_URL = 'api/v1/teams/';

    public function testGetTest()
    {
        // make and test request
        $user = factory(User::class)->create();
        factory(Team::class, 5)->create(['user_id' => $user->id]);
        $response = $this->callGet(self::API_URL, [], true);
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));

        // make and test request for single items
        foreach ($collection->data as $user) {
            $response = $this->callGet(self::API_URL.$user->id, [], true);
            $this->assertResponseOk();
            $this->assertEquals($user->type, 'teams');
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($user->attributes, $item->data->attributes);
            $this->assertFalse(isset($item->data->attributes->password));
        }
    }

    public function testPostTest()
    {
        // generate request data
        $user = factory(User::class)->create();
        $object = factory(Team::class)->make();
        $postData = json_encode([
            'data' => [
                'type' => 'teams',
                'attributes' => $object->toArray(),
                'relationships' => [
                    'user' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $user->id,
                        ],
                    ],
                ],
            ],
        ]);

        // make and test request
        $response = $this->callPost(self::API_URL, $postData, true);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($user = json_decode($response->getContent())->data);
        $this->assertNotEmpty($user->id);

        // test data in database
        try {
            Team::findOrFail($user->id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, 'Test account not found');
        }
    }

    public function testPatchTest()
    {
        // generate request data
        $user = factory(User::class)->create();
        $object = factory(Team::class)->create(['user_id' => $user->id]);
        $object->name = str_random(15);
        $objectId = $object->id;
        unset($object->id);
        $patchData = json_encode([
            'data' => [
                'id' => $objectId,
                'type' => 'teams',
                'attributes' => $object->toArray(),
                'relationships' => [
                    'user' => [
                        'data' => [
                            'type' => 'users',
                            'id' => $user->id,
                        ],
                    ],
                ],
            ],
        ]);

        // make and test request
        $response = $this->callPatch(self::API_URL.$objectId, $patchData, true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($user = json_decode($response->getContent())->data);
        $this->assertNotEmpty($user->id);

        // test data in database
        try {
            $updatedObject = Team::findOrFail($objectId);
            $this->assertEquals($object->name, $updatedObject->name);
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testDeleteTest()
    {
        // generate request data
        $user = factory(User::class)->create();
        $object = factory(Team::class)->create(['user_id' => $user]);
        $response = $this->callDelete(self::API_URL.$object->id, true);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        try {
            Team::findOrFail($object->id);
            $this->assertTrue(false, 'Found user that should have been deleted');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ModelNotFoundException, $e->getMessage());
        }
    }
}
