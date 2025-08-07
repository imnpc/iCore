<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WalletType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DateTrait; // 日期重写
    use HasTranslateableModel; // 翻译
    use LogsActivity; // 记录日志

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
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'decimal_places', 'icon', 'sort', 'is_enabled',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'icon_url',
    ];

    public function getIconUrlAttribute(): string
    {
        if ($this->icon) {
            return Storage::disk(config('filesystems.default'))->url($this->icon);
        } else {
            return '';
        }
    }
}
