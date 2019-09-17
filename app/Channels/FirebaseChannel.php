<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Log;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $userTokens = "fZMxf5tq2eE:APA1bE_5z-qAseVaJrAPmNxPC1an-nR40brYfBzwJJcc07ldsc8Ht5Zt94aq_XCpKI_8WmICX-OFuvdhhZh20An0OHEizleF0dY7_C4-CipiJx1GL3Ysc3cDukMgJrXeQWrVA7XAFU1";
            // $message = $notification->toVoice($notifiable);
            $serviceAccount = ServiceAccount::fromJson(Storage::disk('public')->get('firebaseKarkhanaService.json'));
   			//$config = AndroidConfig::fromArray([
			//     'ttl' => '3600s',
			//     'priority' => 'normal',
			//     'notification' => [
			//         'title' => '$GOOG up 1.43% on the day',
			//         'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
			//         'icon' => 'stock_ticker_update',
			//         'color' => '#f45342',
			//     ],
			// ]);

			// $message = $message->withAndroidConfig($config);
            $notification = FirebaseNotification::fromArray([
                'title' => 'Momonation Notification',
                'body'  => $notification->feed->senderUser->name.' sent you'. $notification->feed->transaction->amount
            ]);

            $firebase = (new Factory)
                ->withServiceAccount($serviceAccount)
                // The following line is optional if the project id in your credentials file
                // is identical to the subdomain of your Firebase project. If you need it,
                // make sure to replace the URL with the URL of your project.
                ->create();
            $message = CloudMessage::withTarget('token', $userTokens)
                ->withNotification($notification);

            $messaging = $firebase->getMessaging();

            $responseData = $messaging->send($message);
        }
        catch (InvalidMessage $e)
        {
            Log::warning('Firebase Notification Error:  '.$e->errors()['error']['message']);
        }
        catch(Exception $e){
        	Log::warning('Firebase Notification Error:  '.$e->getMessage());
        }

        // Send notification to the $notifiable instance...
    }
}
