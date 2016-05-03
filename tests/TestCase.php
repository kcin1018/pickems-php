<?php

namespace PickemsTest;

use Pickems\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTest;

class TestCase extends BaseTest
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    public function tearDown()
    {
    }
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return Response
     */
    protected function callGet($url, array $parameters = [], $auth = false)
    {
        return $this->call('GET', $url, $parameters, [], [], $this->getServerArray($auth));
    }
    /**
     * @param string $url
     *
     * @return Response
     */
    protected function callDelete($url, $auth = false)
    {
        return $this->call('DELETE', $url, [], [], [], $this->getServerArray($auth));
    }
    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPost($url, $content, $auth = false)
    {
        return $this->call('POST', $url, [], [], [], $this->getServerArray($auth), $content);
    }
    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPatch($url, $content, $auth = false)
    {
        return $this->call('PATCH', $url, [], [], [], $this->getServerArray($auth), $content);
    }
    /**
     * @return array
     */
    public function getServerArray($auth = false)
    {
        $server = [
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];
        // required for csrf_token()
        \Session::start();

        // Here you can choose what auth will be used for testing (basic or jwt)
        $headers = [
            'CONTENT-TYPE' => 'application/vnd.api+json',
            'ACCEPT' => 'application/vnd.api+json',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-CSRF-TOKEN' => csrf_token(),
        ];

        if ($auth) {
            list($response, $tokenResponse) = $this->authenticateUser();
            $headers['Authorization'] = 'Bearer '.$tokenResponse->token;
        }

        foreach ($headers as $key => $value) {
            $server['HTTP_'.$key] = $value;
        }

        return $server;
    }

    public function authenticateUser()
    {
        $user = factory(User::class)->create(['password' => bcrypt('testing'), 'admin' => true]);

        $url = 'api/v1/auth/login';
        $data = ['email' => $user->email, 'password' => 'testing'];

        $response = $this->callPost($url, json_encode($data));
        $tokenResponse = json_decode($response->getContent());

        return [$response, json_decode($response->getContent())];
    }
}
