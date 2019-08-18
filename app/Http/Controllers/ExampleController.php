<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Notifications\UserNotification;

class ExampleController extends Controller
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

    public function notify(Request $request){
        try {
            $user = $request->user();
            $data = [
                'sender_id' => 1,
                'receiver_id' => 2,
                'amount' => 3,
                'transaction_time' => 'time'
            ];
            
            $user->notify(new UserNotification($data));
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
        
    }
}
