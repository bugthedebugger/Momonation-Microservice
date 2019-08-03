<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddByUserColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('momonation')->table('transactions', function (Blueprint $table) {
            $table->boolean('by_user')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('momonation')->table('transactions', function (Blueprint $table) {
            $table->dropColumn('by_user');
        });
    }
}
