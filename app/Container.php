<?php

namespace App;

use Psr\Container\ContainerInterface;

/**
 * @deprecated was uusing this custom container but decided to make use of PHP-DI
 */
class Container
{
    private static ContainerInterface $instance;

    public static function set(ContainerInterface $container): void
    {
        self::$instance = $container;
    }

    public static function get(): ContainerInterface
    {
        return self::$instance;
    }
}
