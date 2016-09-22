<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $users = User::withTrashed()->orderBy('deleted_at')->get()->toArray();

        $this->get('/api/users')->seeJson($users);
    }

    public function testMe()
    {
        $me = $this->me()->toArray();

        $this->get('/api/me')->seeJson($me);
    }

    public function testTeammates()
    {
        $this->get('/api/teammates')->seeJsonStructure([
            '*' => [
                'id', 'name', 'designation', 'avatar_url', 'last_reported_at', 'email', 'stages' => [
                    '*' => [
                        'name', 'tasks'
                    ]
                ]
            ]
        ]);
    }
}
