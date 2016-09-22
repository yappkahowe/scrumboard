<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Stage;
use App\Task;
use App\Http\Requests\User\StoreRequest;

class UserController extends Controller
{
    public function index()
    {
        return User::withTrashed()->orderBy('deleted_at')->get();
    }

    public function me(Request $request)
    {
        return auth()->user()->makeVisible('google_id');
    }

    public function updateMyself(Request $request)
    {
        auth()->user()->fill($request->only(['name', 'designation']))->save();

        return auth()->user()->makeVisible('google_id');
    }

    public function teammates()
    {
        $users = User::get(['id', 'name', 'designation', 'avatar_url', 'last_reported_at', 'email']);
        $stages = Stage::get(['id', 'name']);

        $teammates = $users->map(function($user) use ($stages) {

            $user->stages = $stages->map(function($stage) use ($user) {
                $stage->tasks = Task::ofUser($user)->ofStage($stage)->get();

                return clone $stage;
            });

            return $user;
        });

        return $teammates;
    }

    public function store(StoreRequest $request)
    {
        return User::create($request->all());
    }

    public function delete(User $user)
    {
        $user->delete();

        return $user;
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);

        if ($user) {
            $user->restore();
        }

        return $user;
    }
}
