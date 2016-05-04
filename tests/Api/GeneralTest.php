<?php

namespace PickemsTest\Api;

use Pickems\Team;
use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GeneralTest extends TestCase
{
    use DatabaseMigrations;

    public function testHomeStats()
    {
        factory(User::class, 25)->create();
        factory(Team::class, 10)->create(['paid' => false, 'user_id' => 1]);
        factory(Team::class, 15)->create(['paid' => true, 'user_id' => 1]);

        $response = $this->callGet('api/v1/home-stats', [], true);
        $this->assertResponseOk();
        $this->assertNotEmpty($data = json_decode($response->getContent()));
        $this->assertEquals($data->teams, 25);
        $this->assertEquals($data->paid, 15);
        $this->assertEquals($data->owners, 25);
        $this->assertEquals($data->money, 150);
    }
}
