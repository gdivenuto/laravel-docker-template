<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // ---- Remote Login Logic
            // This user can request a remote login?
            $table->boolean('can_transfer_login')->default(false);

            // Remote user data
            $table->integer('remote_user_id')->nullable();
            $table->integer('remote_system_id')->nullable();
            $table->integer('remote_profile_id')->nullable();

            // Remote credential token & expiration
            $table->string('remote_token', 128)->nullable();
            $table->dateTime('remote_token_expiration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
