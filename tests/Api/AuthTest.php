<?php

namespace PickemsTest\Api;

use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthTest extends TestCase
{
    use DatabaseMigrations;
    const API_URL = 'api/v1/auth/';

    public function testLogin()
    {
        list($response, $tokenResponse) = $this->authenticateUser();
        $this->assertResponseOk();
        $this->assertNotEmpty($tokenResponse->token);
    }

    public function testFailedLogin()
    {
        $user = factory(User::class)->create(['password' => bcrypt('testing')]);
        $url = self::API_URL.'login';
        $data = ['email' => $user->email, 'password' => 'notcorrect'];
        $response = $this->callPost($url, json_encode($data));
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRefresh()
    {
        list($response, $tokenResponse) = $this->authenticateUser();
        $user = factory(User::class)->create(['password' => bcrypt('testing')]);
        $url = self::API_URL.'token-refresh';
        $data = ['token' => $tokenResponse->token];
        $response = $this->callPost($url, json_encode($data));
        $this->assertResponseOk();
        $refreshReponse = json_decode($response->getContent());
        $this->assertNotEmpty($refreshReponse->token);
    }
}
