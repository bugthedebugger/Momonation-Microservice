<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SeetingsSeeder extends Seeder
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
            Setting::create([
                'daily_transaction_limit' => env('DAILY_TRANSACTION_LIMIT'),
                'momo_transfer_limit' => env('MOMO_TRANSFER_LIMIT'),
                'auto_refill_limit' => env('AUTO_REFILL_LIMIT'),
                'initialization_limit' => env('INITIALIZATION_LIMIT'),
                'max_momo_limit' => env('MAX_MOMO_LIMIT'),
                'redeem_limit' => env('REDEEM_LIMIT'),
            ]);
            \DB::commit();
        } catch (\Exception $e) {
            \Log::error($e);
            \DB::rollback();
        }
    }
}
