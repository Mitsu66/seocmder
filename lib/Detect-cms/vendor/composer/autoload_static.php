<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd4ea10d2e5808d3645ba0d5d55971b53
{
    public static $prefixesPsr0 = array (
        'D' => 
        array (
            'DetectCMS' => 
            array (
                0 => __DIR__ . '/../..' . '/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInitd4ea10d2e5808d3645ba0d5d55971b53::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
