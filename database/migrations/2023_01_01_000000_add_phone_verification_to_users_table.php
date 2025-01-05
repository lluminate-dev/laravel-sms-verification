<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->nullable();
            $table->timestamp('sms_verified_at')->nullable();
            $table->string('sms_verification_code')->nullable();
            $table->timestamp('sms_verification_code_expires_at')->nullable();
            $table->string('sms_verification_code_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('sms_verified_at');
            $table->dropColumn('sms_verification_code');
            $table->dropColumn('sms_verification_code_expires_at');
            $table->dropColumn('sms_verification_code_status');
        });
    }
};
