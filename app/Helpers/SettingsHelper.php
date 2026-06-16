<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class SettingsHelper
{
    protected static $filePath = null;
    protected static $settings = null;

    protected static function getFilePath()
    {
        if (self::$filePath === null) {
            self::$filePath = storage_path('app/settings.json');
        }
        return self::$filePath;
    }

    protected static function load()
    {
        if (self::$settings === null) {
            $path = self::getFilePath();
            if (File::exists($path)) {
                try {
                    self::$settings = json_decode(File::get($path), true) ?: [];
                } catch (\Exception $e) {
                    self::$settings = [];
                }
            } else {
                self::$settings = [];
            }
        }
        return self::$settings;
    }

    public static function get($key, $default = null)
    {
        // Check cache first
        if (cache()->has($key)) {
            return cache($key);
        }

        // Check settings.json next
        $settings = self::load();
        if (array_key_exists($key, $settings)) {
            // Restore to cache
            cache([$key => $settings[$key]]);
            return $settings[$key];
        }

        return $default;
    }

    public static function set($key, $value)
    {
        // Update cache
        cache([$key => $value]);

        // Update settings.json
        $settings = self::load();
        $settings[$key] = $value;
        self::$settings = $settings;

        $path = self::getFilePath();
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        File::put($path, json_encode($settings, JSON_PRETTY_PRINT));
    }
}
