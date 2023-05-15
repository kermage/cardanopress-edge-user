<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit35b0040732ee4fc2c278f55e45c9fad7
{
    public static $files = array (
        'e2fb8214a7589690aae8ec82f7aa8973' => __DIR__ . '/..' . '/kermage/external-update-manager/class-external-update-manager.php',
    );

    public static $prefixLengthsPsr4 = array (
        'k' => 
        array (
            'kermage\\CardanoPress\\EdgeUser\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'kermage\\CardanoPress\\EdgeUser\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'kermage\\CardanoPress\\EdgeUser\\Main' => __DIR__ . '/../..' . '/src/Main.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit35b0040732ee4fc2c278f55e45c9fad7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit35b0040732ee4fc2c278f55e45c9fad7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit35b0040732ee4fc2c278f55e45c9fad7::$classMap;

        }, null, ClassLoader::class);
    }
}