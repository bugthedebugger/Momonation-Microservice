<?php

namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;
use App\HelperClasses\BankHelper;
use App\Models\Momobank;
 
class RawMomoRefill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rawmomo:refill';
 
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
        $refillAmount = Setting::first()->auto_refill_limit;
        $momoBanks = Momobank::all();
        try {
            \DB::connection('momonation')->beginTransaction();
            foreach($momoBanks as $bank) {
                BankHelper::systemTransfer($bank->user, $refillAmount, false);
            }
            \DB::connection('momonation')->commit();
            print("Successfully refilled\n");
        } catch (\Exception $e) {
            \DB::connection('momonation')->rollback();
            print($e->getMessage()."\n");
        }
       
    }
}