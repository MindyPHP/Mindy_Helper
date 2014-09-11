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

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     * @see mergeWith
     */
    public static function mergeArray($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::mergeArray($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
}