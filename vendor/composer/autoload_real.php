<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitfc2f9d465f0c2197695fec0ea810c522
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitfc2f9d465f0c2197695fec0ea810c522', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitfc2f9d465f0c2197695fec0ea810c522', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitfc2f9d465f0c2197695fec0ea810c522::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
