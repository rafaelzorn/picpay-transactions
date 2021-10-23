<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payer_wallet_id');
            $table->unsignedBigInteger('payee_wallet_id');
            $table->decimal('value', 10, 2);
            $table->enum('operation', ['transfer']);
            $table->enum('status', ['requested', 'completed', 'chargeback'])->default('requested');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payer_wallet_id')->references('id')->on('wallets');
            $table->foreign('payee_wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
