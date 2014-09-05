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
 * @date 05/09/14.09.2014 13:46
 */

namespace Mindy\Helper;


use ArrayObject;
use Mindy\Helper\Traits\Configurator;

class Collection extends ArrayObject
{
    use Configurator;

    /**
     * @var array
     */
    public $data = [];
    /**
     * @var int
     */
    public $flags = 0;
    /**
     * @var string
     */
    public $iteratorClass = "ArrayIterator";

    public function __construct(array $config = [])
    {
        $this->configure($config);
        parent::__construct($this->data, $this->flags, $this->iteratorClass);
    }

    public function add($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function has($key)
    {
        return $this->offsetExists($key);
    }

    public function get($key, $defaultValue = null)
    {
        return $this->has($key) ? $this->offsetGet($key) : $defaultValue;
    }

    public function all()
    {
        return $this->data;
    }

    public function clear()
    {
        return $this->data = [];
    }

    public function remove($key)
    {
        if ($this->has($key)) {
            $this->offsetUnset($key);
        }
    }

    public function toJson()
    {
        return Json::encode($this->data);
    }
}