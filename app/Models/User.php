<?php

namespace App\Models;

use App\Traits\DateTrait;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Traits\HasWallets;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Tags\HasTags;

class User extends Authenticatable implements MediableInterface, Wallet, WalletFloat,BannableContract,HasCustomFields
{
    use HasFactory, Notifiable;
    use HasApiTokens;
    use DateTrait; // 日期重写
    use Mediable; // 媒体库
    use HasWallet, HasWallets; // 钱包
    use HasWalletFloat; // 钱包
    use HasTranslateableModel; // 翻译
    use LogsActivity; // 记录日志
    use HasTags; // 标签
    use Notifiable, AuthenticationLoggable; // 登录日志
    use Bannable; // 封禁
    use SoftDeletes;
    use UsesCustomFields;

    protected static ?string $translateablePackageKey = ''; // 翻译

    /**
     * 日志
     * @return LogOptions
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
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'status',
        'parent_id',
        'avatar',
        'app_authentication_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'tags',
        'app_authentication_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_login_at'     => 'datetime',
            'app_authentication_secret' => 'encrypted',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if ($this->hasMedia('avatar')) {
            $media = $this->firstMedia('avatar');
            return $media->getUrl();
        }

        return '';
    }

    /**
     * 获取上级
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * 获取下级
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // 关联 用户钱包日志
    public function userWalletLog()
    {
        return $this->hasMany(UserWalletLog::class);
    }
}
