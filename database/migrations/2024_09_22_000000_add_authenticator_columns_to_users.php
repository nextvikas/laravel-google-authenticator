<?php
namespace Nextvikas\Authenticator;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuthenticatorColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the user model class dynamically from the configuration
        $userModel = config('auth.providers.users.model');

        // Create an instance of the user model to get the associated table name
        $userTable = (new $userModel)->getTable();

        Schema::table($userTable, function (Blueprint $table) {
            // Add your extra columns here
            $table->string('authenticator')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get the user model and table name dynamically again
        $userModel = config('auth.providers.users.model');
        $userTable = (new $userModel)->getTable();

        Schema::table($userTable, function (Blueprint $table) {
            // Drop the columns when rolling back the migration
            $table->dropColumn('authenticator');
        });
    }
}
