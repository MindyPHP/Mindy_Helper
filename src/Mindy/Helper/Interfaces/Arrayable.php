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
 * @date 06/01/14.01.2014 19:08
 */

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * Arrayable should be implemented by classes that need to be represented in array format.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */

namespace Mindy\Helper\Interfaces;

interface Arrayable
{
    /**
     * Converts the object into an array.
     * @return array the array representation of this object
     */
    public function toArray();
}
