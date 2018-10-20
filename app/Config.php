<?php

namespace KRLX;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = ['name', 'value'];

    /**
     * The preferred way of getting configuration values is to use
     * Config::valueOr(). If the key given does not exist, the provided
     * "fallback" value will be returned instead.
     *
     * @param  string  $key
     * @param  string|null  $default
     * @return string|null
     */
    public static function valueOr(string $key, string $default = null)
    {
        $config = self::where('name', $key)->first();
        if ($config) {
            return $config->value;
        } else {
            return $default;
        }
    }
}