<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('cognito_access_token')->nullable();
            $table->text('cognito_id_token')->nullable();
            $table->text('cognito_refresh_token')->nullable();
            $table->dateTime('cognito_access_token_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'cognito_access_token',
                'cognito_id_token',
                'cognito_refresh_token',
                'cognito_access_token_expires_at',
            ]);
        });
    }
};
