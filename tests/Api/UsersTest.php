<?php

namespace PickemsTest\Api;

use Exception;
use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersTest extends TestCase
{
    use DatabaseMigrations;
    const API_URL = 'api/v1/users/';

    public function testGetUsers()
    {
        // make and test request
        factory(User::class, 5)->create();
        $response = $this->callGet(self::API_URL, [], true);
        $this->assertResponseOk();
        $this->assertNotEmpty($collection = json_decode($response->getContent()));

        // make and test request for single items
        foreach ($collection->data as $dbObject) {
            $response = $this->callGet(self::API_URL.$dbObject->id, [], true);
            $this->assertResponseOk();
            $this->assertEquals($dbObject->type, 'users');
            $this->assertNotNull($item = json_decode($response->getContent()));
            $this->assertEquals($dbObject->attributes, $item->data->attributes);
            $this->assertFalse(isset($item->data->attributes->password));
        }
    }

    public function testPostUsers()
    {
        // generate request data
        $object = factory(User::class)->make();
        $postData = json_encode([
            'data' => [
                'type' => 'users',
                'attributes' => $object->toArray() + ['password' => $object->password],
            ],
        ]);

        // make and test request
        $response = $this->callPost(self::API_URL, $postData, true);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($dbObject = json_decode($response->getContent())->data);
        $this->assertNotEmpty($dbObject->id);

        // test data in database
        try {
            User::findOrFail($dbObject->id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false, 'User account not found');
        }
    }

    public function testPatchUsers()
    {
        // generate request data
        $object = factory(User::class)->create();
        $object->name = str_random(15);
        $objectId = $object->id;
        unset($object->id);
        $patchData = json_encode([
            'data' => [
                'id' => $objectId,
                'type' => 'users',
                'attributes' => $object->toArray(),
            ],
        ]);

        // make and test request
        $response = $this->callPatch(self::API_URL.$objectId, $patchData, true);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($dbObject = json_decode($response->getContent())->data);
        $this->assertNotEmpty($dbObject->id);

        // test data in database
        try {
            $updatedObject = User::findOrFail($objectId);
            $this->assertEquals($object->name, $updatedObject->name);
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testDeleteUsers()
    {
        // generate request data
        $object = factory(User::class)->create();
        $response = $this->callDelete(self::API_URL.$object->id, true);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        try {
            User::findOrFail($object->id);
            $this->assertTrue(false, 'Found user that should have been deleted');
        } catch (Exception $e) {
            $this->assertTrue($e instanceof ModelNotFoundException, $e->getMessage());
        }
    }
}
