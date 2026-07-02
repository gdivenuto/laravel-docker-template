<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_transfer_login');
            $table->dropColumn('remote_user_id');
            $table->dropColumn('remote_system_id');
            $table->dropColumn('remote_profile_id');
            $table->dropColumn('remote_token');
            $table->dropColumn('remote_token_expiration');
        });
    }
}
