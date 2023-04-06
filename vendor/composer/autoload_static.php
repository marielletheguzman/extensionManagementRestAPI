<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3dedbb1b68c47679689f059894575754
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3dedbb1b68c47679689f059894575754::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3dedbb1b68c47679689f059894575754::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3dedbb1b68c47679689f059894575754::$classMap;

        }, null, ClassLoader::class);
    }
}
