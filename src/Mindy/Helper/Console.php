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
 * @date 11/04/14.04.2014 16:05
 */

namespace Mindy\Helper;


class Console 
{
    public static function isCli()
    {
        return php_sapi_name() === 'cli';
    }
}
