<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
    }
/**
 * Get the notification's delivery channels.
 *
 * @param mixed $notifiable
 * @return array
 */
    public function via($notifiable)
    {
        return ['database'];
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
            ->subject('User Notifications!');
    }

    public function toArray($notifiable)
    {
        return [
	        'feed_id' => $this->data['feed_id'],
    	];
    }
}
