<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Momobank;
use Auth;
use Carbon\Carbon;
use App\Models\Leaderboard;
use App\HelperClasses\LeaderboardHelper;

class LeaderboardsController extends Controller
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

    public function leaderboard() {
        return response()->json(LeaderboardHelper::getLeaderboard());
    }

    public function fixLeaderboard() {
        return response()->json(LeaderboardHelper::updateLeaderboard('August 2019'));
    }
}
