<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends SpatieRole
{
    use HasFactory;
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
}
