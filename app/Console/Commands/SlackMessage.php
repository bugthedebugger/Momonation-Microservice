<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
use Illuminate\Support\Facades\Notification;
use App\User;

class SlackMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:slack {title} {message}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send custom message to slack channel. Usage slack:send -title <title> -message <message>';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $title = $this->argument('title');
        $message = $this->argument('message');
        print $title . "\n";
        print $message . "\n";
        Notification::send(User::find(1), new \App\Notifications\SlackNotification($title, $message));
    }
}