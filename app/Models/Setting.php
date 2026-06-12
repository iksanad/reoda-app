<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'label',
    ];

    /**
     * Helper untuk mendapatkan nilai setting.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /** Alias for get() */
    public static function getValue(string $key, $default = null)
    {
        return self::get($key, $default);
    }

    /**
     * Helper untuk mengatur/update nilai setting.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string|null $label
     * @return \App\Models\Setting
     */
    public static function set($key, $value, $group = 'general', $label = null)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        if (!$setting->exists) {
            $setting->group = $group;
            $setting->label = $label ?: ucwords(str_replace('_', ' ', $key));
        }
        $setting->save();

        return $setting;
    }
}
