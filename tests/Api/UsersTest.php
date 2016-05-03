<?php

namespace PickemsTest\Api;

use Exception;
use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersTest extends TestCase
{
    use DatabaseMigrations;
    const API_URL = 'api/v1/users/';
    protected $token;

    // protected function setupAuth()
    // {
    //     $this->token =
    //     $user = factory(User::class)->create();
    //     Auth::login($user);
    // }

    // public function testGetUsers()
    // {
    //     $this->setupAuth();

    //     $users = factory(User::class, 5)->create();
    //     $response = $this->callGet(self::API_URL);
    //     $this->assertResponseOk();
    //     $this->assertNotEmpty($collection = json_decode($response->getContent()));

    //     foreach ($collection->data as $user) {
    //         $response = $this->callGet(self::API_URL.$user->id);
    //         $this->assertResponseOk();
    //         $this->assertNotNull($item = json_decode($response->getContent()));
    //         $this->assertEquals($user->attributes, $item->data->attributes);
    //     }
    // }

    // public function testDeleteUsers()
    // {
    //     $user = factory(User::class, 1)->create();
    //     // test getting all users
    //     $response = $this->callDelete(self::API_URL.$user->id);
    //     $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    //     // test to make sure the user was deleted
    //     try {
    //         User::findOrFail($user->id);
    //         $this->assertTrue(false, 'Found user that should have been deleted');
    //     } catch (Exception $e) {
    //         $this->assertTrue($e instanceof ModelNotFoundException);
    //     }
    // }
    // public function testPostUsers()
    // {
    //     // create user info and convert it to json
    //     $userObject = factory(User::class)->make();
    //     $userData = json_encode([
    //         'data' => [
    //             'type' => 'users',
    //             'attributes' => $userObject->toArray() + [
    //                 'birthday' => $userObject->birthday->format('Y-m-d'),
    //                 'password' => $userObject->password,
    //             ],
    //         ],
    //     ]);
    //     // test getting all users
    //     $response = $this->callPost(self::API_URL, $userData);
    //     $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    //     $this->assertNotNull($user = json_decode($response->getContent())->data);
    //     $this->assertNotEmpty($user->id);
    //     // test to make sure the user was created
    //     try {
    //         User::findOrFail($user->id);
    //         $this->assertTrue(true);
    //     } catch (Exception $e) {
    //         $this->assertTrue(false, 'User account not found');
    //     }
    // }
    // public function testPatchUsers()
    // {
    //     $userObject = factory(User::class)->create();
    //     $userData = [
    //         'data' => [
    //             'type' => 'users',
    //             'attributes' => $userObject->toArray() + [
    //                 'birthday' => $userObject->birthday->format('Y-m-d'),
    //                 'password' => $userObject->password,
    //             ],
    //         ],
    //     ];
    //     $userData['data']['attributes']['first_name'] = 'niweuogbnwiuebg';
    //     $userData = json_encode($userData);
    //     $response = $this->callPatch(self::API_URL.$userObject->id, $userData);
    //     $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    //     $this->assertNotNull($user = json_decode($response->getContent())->data);
    //     $this->assertNotEmpty($user->id);
    //     // test to make sure the user was created
    //     try {
    //         $updatedUser = User::findOrFail($userObject->id);
    //         $this->assertEquals('niweuogbnwiuebg', $updatedUser->first_name);
    //     } catch (Exception $e) {
    //         $this->assertTrue(false, 'User account not found');
    //     }
    // }
}
