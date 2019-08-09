<?php

namespace App\HelperClasses;

use App\User;
use App\Models\Momobank;
use App\Models\Transaction;
use App\Models\Feed;
use App\Models\Setting;
use Carbon\Carbon;

class BankHelper {
    public static function createBankAccount(User $user) {
        $bank = null;
        try{
            \DB::connection('momonation')->beginTransaction();
            if($user->bank != null) {
                return $user->bank;
            }
            $settings = Setting::first();
            $bank = $user->bank()->create([
                'raw' => $settings->initialization_limit,
                'cooked' => 0,
            ]);
            $transaction = BankHelper::writeTransaction(null, $bank, $settings->initialization_limit, false, false);
            \DB::connection('momonation')->commit();
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            \Log::error($e);
            $bank = null;
            throw \Exception($e);
        }
        return $bank;
    }     

    public static function transfer(Momobank $sender, Momobank $receiver, $amount) {

        $transaction = -1;
        if ($sender->raw < $amount)
            return -1;

        $receiver->cooked = $receiver->cooked + $amount;
        $receiver->save();
        $sender->raw = $sender->raw - $amount;
        $sender->save();
        $transaction = BankHelper::writeTransaction($sender, $receiver, $amount, true, true);

        return $transaction;
    }

    public static function writeTransaction(Momobank $sender = null, Momobank $receiver, $amount, $by_user=false, $cooked=false) {
        
        $transaction = -1;

        $transaction = Transaction::create([
            'sender' => $sender == null? null: $sender->user->id,
            'receiver' => $receiver->user->id,
            'amount' => $amount,
            'by_user' => $by_user,
            'cooked' => $cooked,
        ]);

        return $transaction->id;
    }

    public static function writeFeed(User $sender, User $receiver, $title, $description, $transactionID) {
        
        $feed = Feed::create([
            'sender' => $sender->id,
            'receiver' => $receiver->id,
            'title' => $title,
            'description' => $description,
            'transaction_id' => $transactionID,
        ]);
        
        return $feed;
    }

    public static function systemTransfer(User $receiver, $amount) {

        $receiver->cooked = $receiver->cooked + $amount;
        $receiver->save();
        $transaction = BankHelper::writeTransaction(null, $receiver, $amount, false, true);

        return $transaction;
    }

    public static function checkDailyLimit(User $user) {
        $start = Carbon::now();
        $start->hour = 00;
        $start->minute = 00;
        $start->second = 00;

        $end = Carbon::now();
        $end->hour = 23;
        $end->minute = 59;
        $end->second = 59;

        $transactionCount = $user->receivedTransactions()
                            ->where('created_at', '>=', $start)
                            ->where('created_at', '<=', $end)
                            ->where('cooked', true)
                            ->count();

        $setting = Setting::first();

        return $transactionCount < $setting->daily_transaction_limit?true: false;
    }

    public static function checkTransferLimit($amount) {
        if ($amount <= Setting::first()->momo_transfer_limit) 
            return true;
        else 
            return false;
    }
}