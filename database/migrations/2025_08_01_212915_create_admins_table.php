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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('email')->nullable()->comment('邮箱');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证状态');
            $table->string('password')->comment('密码');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('mobile')->nullable()->comment('手机号码');
            $table->integer('status')->default('1')->comment('状态'); //  0-禁用 1-启用
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->comment('管理员');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
