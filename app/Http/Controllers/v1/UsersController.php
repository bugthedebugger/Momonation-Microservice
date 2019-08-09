<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\HelperClasses\BankHelper;

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
        $users = User::find(2);
        return response()->json(BankHelper::checkDailyLimit($users));
    }
}
