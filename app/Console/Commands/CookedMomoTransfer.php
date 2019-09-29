<?php

namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
 
class CookedMomoTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cookedmomo:send {amount}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to refill raw mo:mo';
 
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
        $refillLimit = Setting::first()->momo_transfer_limit;
        $refillAmount = $this->argument('amount');
        if($refillAmount > $refillLimit)
            $refillAmount = $refillLimit;
        $momoBanks = Momobank::all();
        try {
            \DB::connection('momonation')->beginTransaction();
            foreach($momoBanks as $bank) {
                BankHelper::systemTransfer($bank->user, $refillAmount);
            }
            \DB::connection('momonation')->commit();
            print("Successfully sent\n");
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            print($e->getMessage()."\n");
        }
    }
}