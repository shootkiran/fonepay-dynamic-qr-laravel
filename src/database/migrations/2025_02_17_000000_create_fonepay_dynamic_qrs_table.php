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
            $table->string('merchant_code');
            $table->string('qr_code_url')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fonepay_dynamic_qrs');
    }
};
