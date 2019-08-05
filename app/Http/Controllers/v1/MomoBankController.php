<?php

namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
use Illuminate\Http\Request;
use App\User;
use Auth;

class MomoBankController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function transfer(Request $request) {
        $this->validate(
            $request,
            [
                'sender' => 'required',
                'receiver' => 'required',
                'amount' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]
        );

        $amount = $request->input('amount');

        $authUser = Auth::User();
        if ($authUser->id != $request->input('sender')) 
            return response()->json('Sender and Auth User mismatch', 406);
        
        $receiver = User::find($request->input('receiver'));
        if ($receiver == null) 
            return response()->json('Receiver does not exist in our database', 512);

        $authBank = null;
        $receiverBank = null;

        if ($authUser->bank == null) {
            $authBank = BankHelper::createBankAccount($authUser);
        } else {
            $authBank = $authUser->bank;
        }

        if ($authBank == null)
            return response()->json('Could not find user bank account.', 500);

        if ($authBank->raw < $amount)
            return response()->json('You do not have enough raw Mo:Mo', 406);

        if ($receiver->bank == null) {
            $receiverBank = BankHelper::createBankAccount($receiver);
        } else {
            $receiverBank = $receiver->bank;
        }

        if ($receiverBank == null)
            return response()->json('Could not find receiver bank account.', 500);

        try{
            \DB::beginTransaction();
            $transaction = BankHelper::transfer($authBank, $receiverBank, $amount);
            if ($transaction != -1) {
                BankHelper::writeFeed($authUser, $receiver, $request->input('title'), 
                    $request->input('description'), $transaction);
                \DB::commit();
            } else {
                \DB::rollback();
            }
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json('Could not deliver the Mo:Mo', 500);
        }
        return response()->json('Successfully delivered ' . $amount . ' cooked Mo:Mo');
    }
}
