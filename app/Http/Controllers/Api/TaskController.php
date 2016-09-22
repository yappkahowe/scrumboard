<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Task;
use App\User;

class TaskController extends Controller
{
    public function myTasks()
    {
        return auth()->user()->tasks()->withTrashed()->orderBy('id', 'desc')->take(100)->get();
    }

    public function store(Request $request)
    {
        return auth()->user()->tasks()->create($request->all());
    }

    public function update(Request $request, Task $task)
    {
        $task->fill($request->all())->save();

        return $task;
    }

    public function delete(Task $task)
    {
        $task->delete();

        return $task;
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->find($id);

        if ($task) {
            $task->restore();
        }

        return $task;
    }
}
