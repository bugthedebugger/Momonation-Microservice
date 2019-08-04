<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Models\Momobank;
use App\HelperClasses\BankHelper;
use App\Models\Setting;

class BankAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            \DB::beginTransaction();
            $users = User::all();
            $setting = Setting::first();
            $amount = $setting->initialization_limit;
            foreach($users as $user) {
                $bank = [];
                $bank = BankHelper::createBankAccount($user);
                BankHelper::writeTransaction(null, $bank, $amount);
            }
            \DB::commit();
        } catch (\Exception $e) {
            report($e);
            \DB::rollback();
            print($e.'\n');
        }
    }
}
