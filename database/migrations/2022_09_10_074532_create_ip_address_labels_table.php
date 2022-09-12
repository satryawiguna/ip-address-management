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
            $table->foreignId('ip_address_id')->constrained('ip_addresses');
            $table->foreignId('label_id')->constrained('labels');

            $table->unique(["ip_address_id"]);
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
