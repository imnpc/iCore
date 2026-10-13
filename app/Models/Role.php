<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Maggomann\FilamentModelTranslator\Traits\HasTranslateableModel;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use DateTrait;
    use HasFactory; // 日期重写
    use HasTranslateableModel; // 翻译
    use LogsActivity; // 记录日志

    protected static ?string $translateablePackageKey = ''; // 翻译

    /**
     * 日志
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
