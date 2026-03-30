<?php

namespace App\Models;

use App\Enums\FromType;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserWalletLog extends Model
{
    use DateTrait;
    use HasFactory;

    // 日期重写
    use HasTranslateableModel; // 翻译
    use LogsActivity;
    use SoftDeletes; // 记录日志

    protected static ?string $translateablePackageKey = ''; // 翻译

    /**
     * 日志
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'wallet_type_id', 'day', 'old', 'add', 'new', 'from', 'from_user_id', 'order_id', 'remark',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day' => 'datetime',
            'from' => FromType::class,
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'wallet_slug', 'wallet_icon_url', 'from_text', 'wallet_name', 'type_text',
    ];

    // 获取钱包类型的名称
    public function getWalletSlugAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->slug;
        } else {
            return '';
        }
    }

    // 获取钱包类型的图片
    public function getWalletIconUrlAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->icon_url;
        } else {
            return '';
        }
    }

    // 获取来源
    public function getFromTextAttribute()
    {
        return $this->from->getLabel();
    }

    // 获取钱包类型的名称
    public function getWalletNameAttribute()
    {
        if ($this->wallet_type_id > 0) {
            return WalletType::find($this->wallet_type_id)->name;
        } else {
            return '';
        }
    }

    // 获取交易类型 增加 / 扣除
    public function getTypeTextAttribute()
    {
        $state = $this->add > 0 ? 'deposit' : 'withdraw';

        return __("filament-wallet::messages.transactions.columns.{$state}");
    }

    // 关联 用户
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 关联 来自用户
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }

    // 关联 钱包类型
    public function walletType(): BelongsTo
    {
        return $this->belongsTo(WalletType::class);
    }

    // 查询 大小 正负数
    public function scopeNum($query, $num)
    {
        if ($num == 1) {
            return $query->where('add', '>', 0);
        } elseif ($num == -1) {
            return $query->where('add', '<', 0);
        }
    }
}
