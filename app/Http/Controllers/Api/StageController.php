<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Stage;

class StageController extends Controller
{
    public function index()
    {
        return Stage::get();
    }
}
