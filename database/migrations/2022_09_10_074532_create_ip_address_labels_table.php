<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_address_labels', function (Blueprint $table) {
            $table->unsignedBigInteger("ip_address_id")->unique();
            $table->unsignedBigInteger("label_id");

            $table->foreign('ip_address_id')->references('id')->on('ip_addresses')
                ->onDelete('CASCADE');
            $table->foreign('label_id')->references('id')->on('labels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_address_labels');
    }
};
