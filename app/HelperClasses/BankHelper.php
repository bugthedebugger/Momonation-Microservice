<?php

namespace App\HelperClasses;

use App\User;
use App\Models\Momobank;
use App\Models\Transaction;
use App\Models\Feed;

class BankHelper {
    public static function createBankAccount(User $user) {
        $bank = null;
        if($user->bank != null)
            return $user->bank;
        try {
            \DB::beginTransaction();
            $bank = $user->bank()->create([
                'raw' => env('DEFAULT_RAW_MOMO'),
                'cooked' => 0,
            ]);
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return null;
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
        $transaction = BankHelper::writeTransaction($sender, $receiver, $amount);

        return $transaction;
    }

    private static function writeTransaction(Momobank $sender, Momobank $receiver, $amount) {
        
        $transaction = -1;

        $transaction = Transaction::create([
            'sender' => $sender->user->id,
            'receiver' => $receiver->user->id,
            'amount' => $amount,
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
}