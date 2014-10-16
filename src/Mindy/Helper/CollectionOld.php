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
 * @date 09/06/14.06.2014 20:37
 */

namespace Mindy\Helper;


/**
 * This file contains classes implementing Map feature.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Mindy\Exception\Exception;
use Traversable;

/**
 * CMap implements a collection that takes key-value pairs.
 *
 * You can access, add or remove an item with a key by using
 * {@link itemAt}, {@link add}, and {@link remove}.
 * To get the number of the items in the map, use {@link getCount}.
 * CMap can also be used like a regular array as follows,
 * <pre>
 * $map[$key]=$value; // add a key-value pair
 * unset($map[$key]); // remove the value with the specified key
 * if(isset($map[$key])) // if the map contains the key
 * foreach($map as $key=>$value) // traverse the items in the map
 * $n=count($map);  // returns the number of items in the map
 * </pre>
 *
 * @property boolean $readOnly Whether this map is read-only or not. Defaults to false.
 * @property ArrayIterator $iterator An iterator for traversing the items in the list.
 * @property integer $count The number of items in the map.
 * @property array $keys The key list.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.collections
 * @since 1.0
 * @deprecated
 */
class CollectionOld implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @var array internal data storage
     */
    protected $data = [];
    /**
     * @var boolean whether this list is read-only
     */
    protected $readOnly = false;

    /**
     * Constructor.
     * Initializes the list with an array or an iterable object.
     * @param array $data the initial data. Default is null, meaning no initialization.
     * @param boolean $readOnly whether the list is read-only
     * @throws Exception If data is not null and neither an array nor an iterator.
     */
    public function __construct($data = null, $readOnly = false)
    {
        if ($data !== null) {
            $this->copyFrom($data);
        }
        $this->setReadOnly($readOnly);
    }

    /**
     * @return boolean whether this map is read-only or not. Defaults to false.
     */
    public function getReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param boolean $value whether this list is read-only or not
     */
    protected function setReadOnly($value)
    {
        $this->readOnly = $value;
    }

    /**
     * Returns an iterator for traversing the items in the list.
     * This method is required by the interface IteratorAggregate.
     * @return ArrayIterator an iterator for traversing the items in the list.
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Returns the number of items in the map.
     * This method is required by Countable interface.
     * @return integer number of items in the map.
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * Returns the number of items in the map.
     * @return integer the number of items in the map
     */
    public function getCount()
    {
        return count($this->data);
    }

    /**
     * @return array the key list
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * Returns the item with the specified key.
     * This method is exactly the same as {@link offsetGet}.
     * @param mixed $key the key
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * @deprecated
     * @param $key
     * @return mixed
     */
    public function itemAt($key)
    {
        return $this->get($key);
    }

    /**
     * Adds an item into the map.
     * Note, if the specified key already exists, the old value will be overwritten.
     * @param mixed $key key
     * @param mixed $value value
     * @throws Exception if the map is read-only
     */
    public function add($key, $value)
    {
        if (!$this->readOnly) {
            if ($key === null)
                $this->data[] = $value;
            else
                $this->data[$key] = $value;
        } else
            throw new Exception('The collection is read only.');
    }

    /**
     * Removes an item from the map by its key.
     * @param mixed $key the key of the item to be removed
     * @return mixed the removed value, null if no such key exists.
     * @throws Exception if the map is read-only
     */
    public function remove($key)
    {
        if (!$this->readOnly) {
            if (isset($this->data[$key])) {
                $value = $this->data[$key];
                unset($this->data[$key]);
                return $value;
            } else {
                // it is possible the value is null, which is not detected by isset
                unset($this->data[$key]);
                return null;
            }
        } else {
            throw new Exception('The collection is read only.');
        }
    }

    /**
     * Removes all items in the map.
     */
    public function clear()
    {
        foreach (array_keys($this->data) as $key) {
            $this->remove($key);
        }
    }

    /**
     * @param mixed $key the key
     * @return boolean whether the map contains an item with the specified key
     */
    public function contains($key)
    {
        return isset($this->data[$key]) || array_key_exists($key, $this->data);
    }

    /**
     * @return array the list of items in array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Copies iterable data into the map.
     * Note, existing data in the map will be cleared first.
     * @param mixed $data the data to be copied from, must be an array or object implementing Traversable
     * @throws Exception If data is neither an array nor an iterator.
     */
    public function copyFrom($data)
    {
        if (is_array($data) || $data instanceof Traversable) {
            if ($this->getCount() > 0)
                $this->clear();
            if ($data instanceof Map)
                $data = $data->data;
            foreach ($data as $key => $value)
                $this->add($key, $value);
        } elseif ($data !== null)
            throw new Exception('Collection data must be an array or an object implementing Traversable.');
    }

    /**
     * Merges iterable data into the map.
     *
     * Existing elements in the map will be overwritten if their keys are the same as those in the source.
     * If the merge is recursive, the following algorithm is performed:
     * <ul>
     * <li>the map data is saved as $a, and the source data is saved as $b;</li>
     * <li>if $a and $b both have an array indexed at the same string key, the arrays will be merged using this algorithm;</li>
     * <li>any integer-indexed elements in $b will be appended to $a and reindexed accordingly;</li>
     * <li>any string-indexed elements in $b will overwrite elements in $a with the same index;</li>
     * </ul>
     *
     * @param mixed $data the data to be merged with, must be an array or object implementing Traversable
     * @param boolean $recursive whether the merging should be recursive.
     *
     * @throws Exception If data is neither an array nor an iterator.
     */
    public function mergeWith($data, $recursive = true)
    {
        if (is_array($data) || $data instanceof Traversable) {
            if ($data instanceof Collection)
                $data = $data->data;
            if ($recursive) {
                if ($data instanceof Traversable) {
                    $d = array();
                    foreach ($data as $key => $value)
                        $d[$key] = $value;
                    $this->data = self::mergeArray($this->data, $d);
                } else
                    $this->data = self::mergeArray($this->data, $data);
            } else {
                foreach ($data as $key => $value)
                    $this->add($key, $value);
            }
        } elseif ($data !== null)
            throw new Exception('Collection data must be an array or an object implementing Traversable.');
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

    public function toArrayClear()
    {
        return self::recursiveClear($this->toArray());
    }

    /**
     * @param $array
     * @return array
     */
    public static function recursiveClear($array)
    {
        $array = array_map(function ($value) {
            return is_array($value) ? self::recursiveClear(array_filter($value)) : $value;
        }, $array);
        return array_filter($array);
    }

    /**
     * Determines whether a property is defined.
     * A property is defined if there is a getter or setter method
     * defined in the class. Note, property names are case-insensitive.
     * @param string $name the property name
     * @return boolean whether the property is defined
     * @see canGetProperty
     * @see canSetProperty
     */
    public function hasProperty($name)
    {
        return method_exists($this, 'get' . $name) || method_exists($this, 'set' . $name);
    }

    /**
     * Determines whether a property can be read.
     * A property can be read if the class has a getter method
     * for the property name. Note, property name is case-insensitive.
     * @param string $name the property name
     * @return boolean whether the property can be read
     * @see canSetProperty
     */
    public function canGetProperty($name)
    {
        return method_exists($this, 'get' . $name);
    }

    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->contains($offset);
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to retrieve element.
     * @return mixed the element at the offset, null if no element is found at the offset
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param integer $offset the offset to set element
     * @param mixed $item the element value
     */
    public function offsetSet($offset, $item)
    {
        $this->add($offset, $item);
    }

    /**
     * Unsets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed $offset the offset to unset element
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
