<?php

namespace PickemsTest\Api;

use Pickems\NflTeam;
use Pickems\NflPlayer;
use PickemsTest\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NflPlayerTest extends TestCase
{
    use DatabaseMigrations;
    public function testDisplayName()
    {
        factory(NflTeam::class, 3)->create()
            ->each(function ($team) {
                factory(NflPlayer::class, 5)->create(['team_id' => $team->id])
                    ->each(function ($player) use ($team) {
                        $this->assertEquals($player->displayName(), $player->name.'-'.$player->team->abbr.'-'.$player->position);
                        $this->assertEquals($player->team->toArray(), $team->toArray());
                    });
            });
    }
}
