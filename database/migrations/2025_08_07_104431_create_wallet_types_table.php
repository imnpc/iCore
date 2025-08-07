<?php

use App\Models\WalletType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('slug')->unique()->comment('大写英文代码');
            $table->string('description')->nullable()->comment('说明');
            $table->integer('decimal_places')->default(2)->comment('小数位数');
            $table->string('icon')->nullable()->comment('图标');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('is_enabled')->default(0)->comment('是否启用'); // 0-禁用 1-启用
            $table->timestamps();
            $table->softDeletes();
            $table->comment('钱包类型');
        });

        // 插入默认数据
        $walletTypes = [
            [
                'name'        => '余额',
                'slug'        => 'MONEY',
                'description' => '余额',
                'is_enabled'  => 1
            ],
            [
                'name'        => '积分',
                'slug'        => 'CREDIT',
                'description' => '积分',
                'is_enabled'  => 1
            ]
        ];
        WalletType::insert($walletTypes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_types');
    }
};
