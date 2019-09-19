<?php
namespace App\Notifications;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Channels\FirebaseChannel;

use Illuminate\Notifications\Messages\SlackMessage;


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

        return ['database', 'mail', FirebaseChannel::class, 'slack'];

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

            // ->subject('User Notifications! '. $this->feed->senderUser->name)
            // ->line($this->feed->receiverUser->name. ' send you'. $this->feed->transaction->amount. ' momo')
            // ->from('wasp@karkhana.asia', 'wasp')
            // ->markdown('mail.usernotification', [
            //         'feed' => $this->feed
            //         ]);

            ->subject('You have been appreciated!!')
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

    public function toSlack($notifiable)
    {
        \Log::info('Inside slack function');
        return (new SlackMessage)
                    ->success()
                    ->attachment(function ($attachment) {
                        $attachment->title('Appreciation alert! :momo:')
                                ->fields([
                                        'Sender' => $this->feed->senderUser->name,
                                        'Receiver' => $this->feed->receiverUser->name,
                                        'Title' => $this->feed->title,
                                        'With' => $this->feed->transaction->amount . ' :momo:',
                                        'For' => $this->feed->description,
                                    ]);
                    });
    }
}
