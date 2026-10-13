<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vinkla\Hashids\Facades\Hashids;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('invite_code', 32)->nullable()->unique()->comment('个人邀请码')->after('parent_id');
        });
        // 历史数据回填：已存在用户按主键生成邀请码
        try {
            User::whereNull('invite_code')->select('id')->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $user->forceFill(['invite_code' => Hashids::encode($user->id)])->saveQuietly();
                }
            });
        } catch (Throwable $e) {
            // 回填失败不阻断迁移，调用方可手动回填
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['invite_code']);
            $table->dropColumn('invite_code');
        });
    }
};
