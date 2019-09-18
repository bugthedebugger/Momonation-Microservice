<?php

namespace App\Channels;

use Log;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use  Illuminate\Support\Facades\App;
use Kreait\Firebase\Messaging\AndroidConfig;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */

    private $firebase;

    public function __construct(Firebase $firebase){

        $this->firebase = $firebase;

    }

    public function send($notifiable, Notification $notification)
    {
        try {
            $userTokens = "fZMxf5tq2eE:APA1bE_5z-qAseVaJrAPmNxPC1an-nR40brYfBzwJJcc07ldsc8Ht5Zt94aq_XCpKI_8WmICX-OFuvdhhZh20An0OHEizleF0dY7_C4-CipiJx1GL3Ysc3cDukMgJrXeQWrVA7XAFU1";
            // $message = $notification->toVoice($notifiable);
            // $serviceAccount = ServiceAccount::fromJson(Storage::disk('public')
            //                                 ->get('firebaseKarkhanaService.json'));

            $config = AndroidConfig::fromArray([
                'ttl' => '3600s',
                'priority' => 'high',
                'notification' => [
                    'title' => 'Momonation Notification',
                    'body' => $notification->feed->senderUser->name.' sent you'. $notification->feed->transaction->amount,
                ],
            ]);

            // $notification = FirebaseNotification::fromArray([
            //     'title' => 'Momonation Notification',
            //     'body'  => $notification->feed->senderUser->name.' sent you'. $notification->feed->transaction->amount
            // ]);

            $data = ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'];

            $message = CloudMessage::withTarget('token', $userTokens)
                ->withAndroidConfig($config)
                ->withData($data);

            $messaging = $this->firebase->getMessaging();

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
