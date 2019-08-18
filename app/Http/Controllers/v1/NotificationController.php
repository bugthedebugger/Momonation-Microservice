<?php

namespace App\Http\Controllers\v1;

use App\Models\Feed;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
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

    public function notifications(Request $request)
    {
        $user = $request->user();
        $data = collect([]);
        // dd($user);
        foreach ($user->notifications as $notification)
        {
            if ($notification->type == 'App\Notifications\UserNotification' && isset($notification->data['feed_id']))
            {
                $feed = Feed::find($notification->data['feed_id']);
                $notifyArray = [
                    'sender' => $feed->senderUser->name,
                    'title' => $feed->title,
                    'amount' => $feed->transaction->amount,
                    'description' => $feed->description
                ];
                $data->push($notifyArray);
            }
        }

        return response()->json(['notifications' => $data, 'code' => 200], 200);
    }
}
