<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Kreait\Firebase;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Log;
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
    private $database;

    public function __construct(Firebase $firebase)
    {

        $this->firebase = $firebase;
        $this->database = $this->firebase->getDatabase();

    }

    public function send($notifiable, Notification $notification)
    {
        try {
            // $token = 'eXzrtkkmbyM:APA91bG08Fb5seKYEpMPvUx3sti0odedz0UffQJeaL-GEuCT_JpzZ73td1ORg3nBH1kXH36qJNHx1AwqjwlW_MU76ShuaeXY2xwJr5_oOtD7fkUL95sxnyycde32-ONJIQ4brKbcZbzU';
            $reference = $this->database
                ->getReference('/RegistrationTokens/' . bin2hex($notifiable->email));

            $snapshot = $reference->getSnapshot()->getValue();

            if(!$snapshot || !isset($snapshot['fcm_token']))
            {
                $userTokens = $snapshot['fcm_token'];

                // $userTokens = $token;

                // $message = $notification->toVoice($notifiable);
                // $serviceAccount = ServiceAccount::fromJson(Storage::disk('public')
                //                                 ->get('firebaseKarkhanaService.json'));

                $config = AndroidConfig::fromArray([
                    'ttl'          => '3600s',
                    'priority'     => 'high',
                    'notification' => [
                        'title' => 'Momonation Notification',
                        'body'  => $notification->feed->senderUser->name . ' sent you' . $notification->feed->transaction->amount. ' momos',
                    ],
                ]);

                // $notification = FirebaseNotification::fromArray([
                //     'title' => 'Momonation Notification',
                //     'body'  => $notification->feed->senderUser->name.' sent you'. $notification->feed->transaction->amount
                // ]);

                $data = ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'];

                $notification = FirebaseNotification::fromArray([
                    'image' => $notifiable->info()['avatar'],
                ]);
                $message = CloudMessage::withTarget('token', $userTokens)
                    ->withAndroidConfig($config)
                    ->withData($data)
                    ->withNotification($notification);
                    // ->withImageUrl($notifiable->info()['avatar']);;

                $messaging = $this->firebase->getMessaging();

                $responseData = $messaging->send($message);
            }else{
                Log::warning('Firebase Notification Error:  Registration Token not availaibe.');
            }

        }
        catch (InvalidMessage $e)
        {
            Log::warning('Firebase Notification Error:  ' . $e->errors()['error']['message']);
        }
        catch (Exception $e)
        {
            Log::warning('Firebase Notification Error:  ' . $e->getMessage());
        }

        // Send notification to the $notifiable instance...
    }
}
