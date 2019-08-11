<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
use Auth;

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
        $banks = Momobank::all();
        $userList = [];
        foreach($banks as $bank) {
            if($bank->user_id == Auth::User()->id)
                continue;
            
            $userList[] = $bank->user->info();
        }
        $userList = collect($userList)->sortBy('name');
        $response = [
            'users' => $userList,
        ];

        return response()->json($response);
    }
}
