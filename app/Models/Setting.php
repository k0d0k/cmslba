<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    public static function get(string $key, $default = null, string $group = 'general')
    {
        $row = static::where('group', $group)->where('key', $key)->first();

        return $row?->value ?? $default;
    }

    public static function put(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value],
        );
    }
}
