<?php
namespace App\Notifications;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\FirebaseChannel;

class UserNotification extends Notification
{
    use Queueable;
/**
 * @param 
 */
	private $data;

    public function __construct($data)
    {	
       $this->data = $data;
       $this->feed = Feed::findOrFail($this->data['feed_id']);
    }
/**
 * Get the notification's delivery channels.
 *
 * @param mixed $notifiable
 * @return array
 */
    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }
/**
 * Get the mail representation of the notification.
 *
 * @param mixed $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('User Notifications! '. $this->feed->senderUser->name)
            // ->line($this->feed->receiverUser->name. ' send you'. $this->feed->transaction->amount. ' momo')
            ->from('wasp@karkhana.asia', 'wasp')
            ->markdown('mail.usernotification', [
                    'feed' => $this->feed
                    ]);
    }

    public function toArray($notifiable)
    {
        return [
	        'feed_id' => $this->data['feed_id'],
    	];
    }
}
