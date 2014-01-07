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
 * @date 06/01/14.01.2014 18:41
 */

namespace Mindy\Helper;


use InvalidArgumentException;

class Creator
{
    /**
     * @var array initial property values that will be applied to objects newly created via [[createObject]].
     * The array keys are class names without leading backslashes "\", and the array values are the corresponding
     * name-value pairs for initializing the created class instances. For example,
     *
     * ~~~
     * [
     *     'Bar' => [
     *         'prop1' => 'value1',
     *         'prop2' => 'value2',
     *     ],
     *     'mycompany\foo\Car' => [
     *         'prop1' => 'value1',
     *         'prop2' => 'value2',
     *     ],
     * ]
     * ~~~
     *
     * @see createObject()
     */
    public static $objectConfig = [];

    /**
     * Creates a new object using the given configuration.
     *
     * The configuration can be either a string or an array.
     * If a string, it is treated as the *object class*; if an array,
     * it must contain a `class` element specifying the *object class*, and
     * the rest of the name-value pairs in the array will be used to initialize
     * the corresponding object properties.
     *
     * Below are some usage examples:
     *
     * ~~~
     * $object = \Yii::createObject('app\components\GoogleMap');
     * $object = \Yii::createObject([
     *     'class' => 'app\components\GoogleMap',
     *     'apiKey' => 'xyz',
     * ]);
     * ~~~
     *
     * This method can be used to create any object as long as the object's constructor is
     * defined like the following:
     *
     * ~~~
     * public function __construct(..., $config = []) {
     * }
     * ~~~
     *
     * The method will pass the given configuration as the last parameter of the constructor,
     * and any additional parameters to this method will be passed as the rest of the constructor parameters.
     *
     * @param string|array $config the configuration. It can be either a string representing the class name
     * or an array representing the object configuration.
     * @return mixed the created object
     * @throws InvalidArgumentException if the configuration is invalid.
     */
    public static function createObject($config)
    {
        static $reflections = [];

        if (is_string($config)) {
            $class = $config;
            $config = [];
        } elseif (isset($config['class'])) {
            $class = $config['class'];
            unset($config['class']);
        } else {
            throw new InvalidArgumentException('Object configuration must be an array containing a "class" element.');
        }

        $class = ltrim($class, '\\');

        if (isset(static::$objectConfig[$class])) {
            $config = array_merge(static::$objectConfig[$class], $config);
        }

        if (($n = func_num_args()) > 1) {
            /** @var \ReflectionClass $reflection */
            if (isset($reflections[$class])) {
                $reflection = $reflections[$class];
            } else {
                $reflection = $reflections[$class] = new \ReflectionClass($class);
            }
            $args = func_get_args();
            array_shift($args); // remove $config
            if (!empty($config)) {
                $args[] = $config;
            }
            return $reflection->newInstanceArgs($args);
        } else {
            return empty($config) ? new $class : new $class($config);
        }
    }

    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

    /**
     * Returns the public member variables of an object.
     * This method is provided such that we can get the public member variables of an object.
     * It is different from "get_object_vars()" because the latter will return private
     * and protected variables if it is called within the object itself.
     * @param object $object the object to be handled
     * @return array the public member variables of the object
     */
    public static function getObjectVars($object)
    {
        return get_object_vars($object);
    }
}
