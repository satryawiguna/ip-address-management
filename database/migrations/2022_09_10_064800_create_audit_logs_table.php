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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("audit_logable_id");
            $table->string("audit_logable_type");
            $table->enum("level", ["FATAL", "ERROR", "WARNING", "INFO", "DEBUG", "TRACE"]);
            $table->dateTime("logged_at");
            $table->string("message");
            $table->longText("context");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
