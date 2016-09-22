<?php

namespace App\Observers;

use App\Task;
use App\Events\Task\Created;
use App\Events\Task\Updated;
use App\Events\Task\Deleted;

class TaskObserver
{
    /**
     * Listen to the Task created event.
     *
     * @param  Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        broadcast(new Created($task))->toOthers();
    }

    /**
     * Listen to the Task updated event.
     *
     * @param  Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        broadcast(new Updated($task))->toOthers();
    }

    /**
     * Listen to the Task deleted event.
     *
     * @param  Task  $task
     * @return void
     */
    public function deleted(Task $task)
    {
        broadcast(new Deleted($task))->toOthers();
    }
}