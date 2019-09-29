<?php
namespace App\Notifications;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\FirebaseChannel;
use Illuminate\Notifications\Messages\SlackMessage;


class SlackNotification extends Notification
{
    use Queueable;
/**
 * @param 
 */
    private $title;
	private $message;

    public function __construct($title, $message)
    {	
        $this->title = $title;
        $this->message = $message;
    }
/**
 * Get the notification's delivery channels.
 *
 * @param mixed $notifiable
 * @return array
 */
    public function via($notifiable)
    {
        return ['slack'];
    }
/**
 * Get the mail representation of the notification.
 *
 * @param mixed $notifiable
 * @return \Illuminate\Notifications\Messages\MailMessage
 */

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
                    ->success()
                    ->attachment(function ($attachment) {
                        $attachment->title($this->title)
                                ->content($this->message);
                    });
    }
}
