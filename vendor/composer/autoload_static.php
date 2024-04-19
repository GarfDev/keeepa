<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita202b6bc009686723e7c793e31c7b8d0
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Medoo\\' => 6,
        ),
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Medoo\\' => 
        array (
            0 => __DIR__ . '/..' . '/catfan/medoo/src',
        ),
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita202b6bc009686723e7c793e31c7b8d0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita202b6bc009686723e7c793e31c7b8d0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
