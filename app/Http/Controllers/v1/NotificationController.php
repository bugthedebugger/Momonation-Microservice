<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;

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
        //GET USER
        $user = $request->user();
        $data = collect([]);
        try {
            //LOOPING THROUGH NOTIFICATIONS
            foreach ($user->notifications as $notification)
            {
                //CHECKING NOTIFICATIONS
                if ($notification->type == 'App\Notifications\UserNotification' && isset($notification->data['feed_id']))
                {
                    //GETTING FEED
                    $feed        = Feed::find($notification->data['feed_id']);

                    //CREATING ARRAY
                    $notifyArray = [
                        'sender'      => $feed->senderUser->name,
                        'title'       => $feed->title,
                        'amount'      => $feed->transaction->amount,
                        'description' => $feed->description,
                    ];
                    $data->push($notifyArray);
                }
            }

        }
        catch (Exception $e)
        {
            return response()->json(['error' => $exception->getMessage(), 'code' => 500, 'line' => $exception->getline(), 'file' => $exception->getFile()], 500);
        }

        return response()->json(['notifications' => $data, 'code' => 200], 200);
    }
}
