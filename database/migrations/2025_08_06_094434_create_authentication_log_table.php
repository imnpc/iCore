<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('authentication-log.table_name'), function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable');
            $table->string('ip_address', 45)->nullable()->comment('IP地址');
            $table->text('user_agent')->nullable()->comment('用户代理');
            $table->timestamp('login_at')->nullable()->comment('登录时间');
            $table->boolean('login_successful')->default(false)->comment('是否登录成功');
            $table->timestamp('logout_at')->nullable()->comment('登出时间');
            $table->boolean('cleared_by_user')->default(false)->comment('是否手动清除');
            $table->json('location')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('authentication-log.table_name'));
    }
};
