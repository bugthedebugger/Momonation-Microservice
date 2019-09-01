<?php

namespace App\HelperClasses;

use App\User;
use App\Models\Momobank;
use App\Models\Transaction;
use App\Models\Feed;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\Leaderboard;


class LeaderboardHelper { 
    public static function leaderboardUsers($number, $otherDate=null) {
        if($otherDate == null)
        {
            $transactions = Transaction::where('created_at', '>=', Carbon::now()->startOfMonth())
                                    ->where('created_at', '<=', Carbon::now()->endOfMonth())
                                    ->where('cooked', true)
                                    ->get();
        } else {
            $startDate = Carbon::parse($otherDate)->startOfMonth();
            $endDate = Carbon::parse($otherDate)->endOfMonth();
            $transactions = Transaction::where('created_at', '>=', $startDate)
                                    ->where('created_at', '<=', $endDate)
                                    ->where('cooked', true)
                                    ->get();
        }
        if ($transactions == null) {
            return [];
        }
        $userIDs = $transactions->groupBy('receiver')->keys();
        $userInfos = collect();

        foreach($userIDs as $userID) {
            $userInfos->push(User::find($userID)->info());
        }

        return $userInfos->sortByDesc('momo')->take($number)->values();
    }

    public static function createLeaderboardEntry($month=null) {
        try{
            \DB::connection('momonation')->beginTransaction();
            $today = Carbon::now();
            if($month == null)
                $date = $today->monthName . ' ' . $today->year;
            else
                $date = $month;
            Leaderboard::create([
                'date' => $date,
            ]);
            \DB::connection('momonation')->commit();
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            \Log::error($e);
            throw new \Exception($e);
        }
    }

    public static function thisMonthLeaderboardExists($month=null) {
        $today = Carbon::now();
        if ($month == null)
            $date = $today->monthName . ' ' . $today->year;
        else 
            $date = $month;
        $leaderboard = Leaderboard::where('date', $date)->first();
        return $leaderboard != null;
    }

    public static function updateLeaderboard($month = null) {
        $today = Carbon::now();
        if ($month == null) {
            $date = $today->monthName . ' ' . $today->year;
            if(!LeaderboardHelper::thisMonthLeaderboardExists()) {
                LeaderboardHelper::createLeaderboardEntry();
            }
        }
        else {
            $date = $month;
            if(!LeaderboardHelper::thisMonthLeaderboardExists($date)) {
                LeaderboardHelper::createLeaderboardEntry($date);
            }
        }


        $leaderboard = Leaderboard::where('date', $date)->first();

        $newLeaderboardUsers = collect(LeaderboardHelper::leaderboardUsers(5, $month))->pluck('id');
        try{
            \DB::connection('momonation')->beginTransaction();
            $leaderboard->users()->sync($newLeaderboardUsers);
            \DB::connection('momonation')->commit();
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            \Log::error($e);
        }
        $leaderboard = Leaderboard::where('date', $date)->first();
        $users = $leaderboard->users;
        $userInfo = collect();
        foreach($users as $user) {
            $userInfo->push($user->info());
        }
        return [
            'users' => $userInfo->values(),
            'date' => $date,
        ];
    }

    public static function getLeaderboard() {
        LeaderboardHelper::updateLeaderboard();

        $leaderboards = Leaderboard::orderBy('created_at', 'desc')->get();
        $leaderboard = collect();

        foreach($leaderboards as $l) {
            $users = $l->users;
            $userInfos = collect();
            foreach($users as $user) {
                $userInfos->push($user->info($l->date));
            }
            $min = $userInfos->min('momo');
            $max = $userInfos->max('momo');
            $date = $l->date;

            $temp = [
                'users' => $userInfos->values(),
                'date' => $date,
                'min' => $min,
                'max' => $max,
            ];

            $leaderboard->push($temp);
        }
        return [
            'leaderboards' => $leaderboard->values()
        ];
    }

    public function fixLeaderboard($date) {

    }
}