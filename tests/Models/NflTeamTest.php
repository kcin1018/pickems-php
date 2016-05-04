<?php

namespace PickemsTest\Api;

use Pickems\NflTeam;
use PickemsTest\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class NflTeamTest extends TestCase
{
    use DatabaseMigrations;
    public function testDisplayName()
    {
        factory(NflTeam::class, 3)->create()
            ->each(function ($team) {
                $this->assertEquals($team->displayName(), $team->city.' '.$team->name.'-'.$team->abbr.'-'.$team->conference);
            });
    }
}
