<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->string("phone")->nullable()->after("email");
            $table->string("email")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            $table->dropColumn("phone");
            $table->string("email")->change();
        });
    }
}
