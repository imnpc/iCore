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
        Schema::create('user_wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('wallet_type_id')->comment('钱包类型 ID');
            $table->date('day')->nullable()->comment('日期');
            $table->decimal('old', 13, 2)->comment('原数值');
            $table->decimal('add', 13, 2)->comment('新增');
            $table->decimal('new', 13, 2)->comment('新数值');
            $table->integer('from')->default(0)->comment('来源');
            $table->integer('from_user_id')->nullable()->comment('来自用户 ID');
            $table->integer('order_id')->nullable()->comment('订单 ID');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('用户钱包日志');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallet_logs');
    }
};
