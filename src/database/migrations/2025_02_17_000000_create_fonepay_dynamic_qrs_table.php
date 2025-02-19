<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fonepay_dynamic_qrs', function (Blueprint $table) {
            $table->id();
            $table->string('prn')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('remarks1')->nullable();
            $table->string('remarks2')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('verified_at')->nullable();

            $table->string('username');
            $table->string('password');
            $table->string('secretKey');
            $table->string('merchantCode');

            $table->text('fonepay_qrMessage')->nullable();
            $table->string('fonepay_status')->nullable();
            $table->string('fonepay_message')->nullable(); //if error
            $table->date('fonepay_requested_date')->nullable();
            $table->text('fonepay_merchantWebSocketUrl')->nullable();
            $table->text('fonepay_thirdpartyQrWebSocketUrl')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fonepay_dynamic_qrs');
    }
};
