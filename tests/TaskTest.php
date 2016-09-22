<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Task;

class TaskTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMyTasks()
    {
        $tasks = $this->me()->tasks()->withTrashed()->orderBy('id', 'desc')->take(100)->get()->toArray();

        $this->get('/api/my-tasks')->seeJson($tasks);
    }

    public function testStore()
    {
        $dummyTask = ['description' => 'Dummy Task', 'arrangement' => 0, 'stage_id' => 1];

        $this->post('/api/tasks', $dummyTask)->seeJson(['user_id' => $this->me()->id]);
    }

    public function testUpdate()
    {
        $task = Task::first();

        $randomDescription = str_random(100);

        $task->description = $randomDescription;

        $this->patch('/api/tasks/' . $task->id, $task->toArray())->seeJson(['description' => $randomDescription]);
    }

    public function testDelete()
    {
        $task = Task::first();

        $deleted_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        $this->delete('/api/tasks/' . $task->id)->seeJson(['deleted_at' => $deleted_at]);
    }
}
