<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit78f5086fb8c086efd73e9d35b4f04049
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'w' => 
        array (
            'wcbel\\classes\\' => 14,
        ),
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'K' => 
        array (
            'KhanhIceTea\\Twigeval\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'wcbel\\classes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'KhanhIceTea\\Twigeval\\' => 
        array (
            0 => __DIR__ . '/..' . '/khanhicetea/twigeval/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit78f5086fb8c086efd73e9d35b4f04049::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit78f5086fb8c086efd73e9d35b4f04049::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit78f5086fb8c086efd73e9d35b4f04049::$classMap;

        }, null, ClassLoader::class);
    }
}
