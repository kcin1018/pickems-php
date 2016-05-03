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
    protected function callGet($url, array $parameters = [])
    {
        return $this->call('GET', $url, $parameters, [], [], $this->getServerArray());
    }
    /**
     * @param string $url
     *
     * @return Response
     */
    protected function callDelete($url)
    {
        return $this->call('DELETE', $url, [], [], [], $this->getServerArray());
    }
    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPost($url, $content)
    {
        return $this->call('POST', $url, [], [], [], $this->getServerArray(), $content);
    }
    /**
     * @param string $url
     * @param string $content
     *
     * @return Response
     */
    protected function callPatch($url, $content)
    {
        return $this->call('PATCH', $url, [], [], [], $this->getServerArray(), $content);
    }
    /**
     * @return array
     */
    public function getServerArray()
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
        foreach ($headers as $key => $value) {
            $server['HTTP_'.$key] = $value;
        }

        return $server;
    }

    public function authenticateUser()
    {
        $user = factory(User::class)->create(['password' => bcrypt('testing')]);

        $url = 'api/v1/auth/login';
        $data = ['email' => $user->email, 'password' => 'testing'];

        $response = $this->callPost($url, json_encode($data));

        return [$response, json_decode($response->getContent())];
    }
}
