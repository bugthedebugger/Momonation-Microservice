<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function users() {
        $users = User::find(1)->asSenderFeed;
        return response()->json($users);
    }
}
