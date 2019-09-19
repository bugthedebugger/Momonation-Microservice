<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Kreait\Firebase;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
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

            //GET REGISTRATION TOKEN FROM FIREBASE DATABASE
            $reference = $this->database
                ->getReference('/RegistrationTokens/' . bin2hex($notifiable->email));

            //GET THE VALUE
            $snapshot = $reference->getSnapshot()->getValue();

            //CHECK IF TOKEN EXISTS
            if (!$snapshot || !isset($snapshot['fcm_token'])) {
                $userTokens = $snapshot['fcm_token'];

                // $userTokens = $token;

                //CREATING NOTIFICATION BODY
                $config = AndroidConfig::fromArray([
                    'ttl' => '3600s',
                    'priority' => 'high',
                    'notification' => [
                        'title' => 'Momonation Notification',
                        'body' => $notification->feed->senderUser->name . ' sent you' . $notification->feed->transaction->amount . ' momos',
                    ],
                ]);

                // $notification = FirebaseNotification::fromArray([
                //     'title' => 'Momonation Notification',
                //     'body'  => $notification->feed->senderUser->name.' sent you'. $notification->feed->transaction->amount
                // ]);
                
                //DATA FOR FLUTTER NOTIFICATION
                $data = ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'];
                
                //ADDING IMAGE
                $notification = FirebaseNotification::fromArray([
                    'image' => $notifiable->info()['avatar'],
                ]);
                
                //MESSAGE
                $message = CloudMessage::withTarget('token', $userTokens)
                    ->withAndroidConfig($config)
                    ->withData($data)
                    ->withNotification($notification)
                    ->withImageUrl($notifiable->feed->senderUser->info()['avatar']);

                $messaging = $this->firebase->getMessaging();
                
                //SEND MESSAGE
                $responseData = $messaging->send($message);
            } else {
                Log::warning('Firebase Notification Error:  Registration Token not availaibe.');
            }

        } catch (InvalidMessage $e) {
            Log::warning('Firebase Notification Error:  ' . $e->errors()['error']['message']);
        } catch (Exception $e) {
            Log::warning('Firebase Notification Error:  ' . $e->getMessage());
        }

        // Send notification to the $notifiable instance...
    }
}
