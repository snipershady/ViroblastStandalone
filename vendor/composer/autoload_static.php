<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7b6d399dd57e2cba1dc981da0fa1ad17
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TypeIdentifier\\' => 15,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TypeIdentifier\\' => 
        array (
            0 => __DIR__ . '/..' . '/snipershady/typeidentifier/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'TypeIdentifier\\Service\\EffectivePrimitiveTypeIdentifierService' => __DIR__ . '/..' . '/snipershady/typeidentifier/src/Service/EffectivePrimitiveTypeIdentifierService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7b6d399dd57e2cba1dc981da0fa1ad17::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7b6d399dd57e2cba1dc981da0fa1ad17::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7b6d399dd57e2cba1dc981da0fa1ad17::$classMap;

        }, null, ClassLoader::class);
    }
}
