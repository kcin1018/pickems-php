<?php

namespace PickemsTest\Api;

use Pickems\Team;
use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PicksTest extends TestCase
{
    use DatabaseMigrations;
    const API_URL = 'api/v1/picks/';

    public function testGetPicks()
    {
        // make and test request
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        foreach (range(1, 5) as $week) {
            $response = $this->callGet(self::API_URL.$team->id.'/'.$week, [], true);
            $this->assertResponseOk();
            $this->assertNotEmpty($data = json_decode($response->getContent())->data);

            // has the required parts
            $this->assertNotEmpty($data->attributes->week);
            $this->assertNotEmpty($data->attributes->schedule);
            $this->assertNotEmpty($data->attributes->pick1);
            $this->assertNotEmpty($data->attributes->pick2);
            $this->assertNotEmpty($data->attributes->{'picks-left'});
            $this->assertNotEmpty($data->attributes->{'teams-picked'});
        }
    }
}
