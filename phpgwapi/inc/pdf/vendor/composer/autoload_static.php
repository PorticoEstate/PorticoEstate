<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit55d4fd93b2e4871b40463ee8750f701e
{
    public static $files = array (
        'd5fa61a7f6cbc1df09dd4df84549a2dc' => __DIR__ . '/..' . '/rospdf/pdf-php/src/Cpdf.php',
        '2d15964294879de66053d54f6bde65d7' => __DIR__ . '/..' . '/rospdf/pdf-php/src/Cezpdf.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit55d4fd93b2e4871b40463ee8750f701e::$classMap;

        }, null, ClassLoader::class);
    }
}
