<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Kreait\Firebase;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
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
            $reference = $this->database
                ->getReference('/RegistrationTokens/' . bin2hex($notifiable->email));

            $snapshot = $reference->getSnapshot()->getValue();

            if (!$snapshot || !isset($snapshot['fcm_token']))
            {
                $userTokens = $snapshot['fcm_token'];

                // $message = $notification->toVoice($notifiable);
                // $serviceAccount = ServiceAccount::fromJson(Storage::disk('public')
                //                                 ->get('firebaseKarkhanaService.json'));

                $config = AndroidConfig::fromArray([
                    'ttl'          => '3600s',
                    'priority'     => 'high',
                    'notification' => [
                        'title' => 'Momonation Notification',
                        'body'  => $notification->feed->senderUser->name . ' sent you' . $notification->feed->transaction->amount,
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
