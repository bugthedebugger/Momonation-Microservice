<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
use Auth;
use App\Models\Leaderboard;
use Carbon\Carbon;
use App\HelperClasses\LeaderboardHelper;

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
        $userList = collect([]);
        foreach($banks as $bank) {
            if($bank->user_id == Auth::User()->id)
                continue;
            
            $userList->push($bank->user->info());
        }
        // return response()->json($userList);
        $userList = collect($userList)->sortBy('name')->values();
        // dd($userList);
        $response = [
            'users' => $userList,
        ];

        return response()->json($response);
    }

    public function test() {
        // $user = User::find(3);
        return response()->json(LeaderboardHelper::leaderboardUsers(2));
        // $user->leaderboards()
        // $leaderboards = Leaderboard::create([
        //     'date' => Carbon::now()->monthName . ' ' . Carbon::now()->year,
        // ]);

        // $leaderBoard = Leaderboard::find(1);
        // return response()->json($leaderBoard->users);
        // $user->leaderboards()->attach($leaderBoard->id);
        // $leaderBoard->users()->detach(2);
    }
}
