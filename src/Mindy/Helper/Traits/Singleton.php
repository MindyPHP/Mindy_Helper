<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 18/07/14.07.2014 20:59
 */

namespace Mindy\Helper\Traits;


use ReflectionClass;

trait Singleton
{
    protected static $instance;

    final public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class = new ReflectionClass(__CLASS__);
            self::$instance = $class->newInstanceArgs(func_get_args());
        }

        return self::$instance;
    }

    final private function __clone()
    {
    }

    final private function __wakeup()
    {
    }

}
