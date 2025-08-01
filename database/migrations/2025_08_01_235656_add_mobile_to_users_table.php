<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->nullable()->unique()->comment('手机号码')->after('email');
            $table->integer('status')->default('1')->comment('状态')->after('remember_token'); //  0-禁用 1-启用
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间')->after('status');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP')->after('last_login_at');
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
