<?php

namespace PickemsTest\Models;

use Pickems\User;
use PickemsTest\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    // public function testUserFullname()
    // {
    //     $user = factory(User::class)->create();
    //     $this->assertEquals($user->fullname, $user->first_name.' '.$user->last_name);
    // }
}
