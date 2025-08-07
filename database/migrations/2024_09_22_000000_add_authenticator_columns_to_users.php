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
        $userModel = config('auth.providers.users.model');
        $userTable = (new $userModel)->getTable();
        $secretColumnName = config('authenticator.otp_settings.secret_column_name', 'authenticator');

        Schema::table($userTable, function (Blueprint $table) use ($secretColumnName, $userTable) {
            if (!Schema::hasColumn($userTable, $secretColumnName)) {
                $table->longText($secretColumnName)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $userModel = config('auth.providers.users.model');
        $userTable = (new $userModel)->getTable();
        $secretColumnName = config('authenticator.otp_settings.secret_column_name', 'authenticator');

        Schema::table($userTable, function (Blueprint $table) use ($secretColumnName, $userTable) {
            if (Schema::hasColumn($userTable, $secretColumnName)) {
                $table->dropColumn($secretColumnName);
            }
        });
    }
};