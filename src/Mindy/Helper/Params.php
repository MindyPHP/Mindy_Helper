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
 * @date 11/04/14.04.2014 14:28
 */

namespace Mindy\Helper;


class Params 
{
    public static $params = [];

    public static function setParams(array $params = [])
    {
        self::$params = $params;
    }

    public static function getParams()
    {
        return self::$params;
    }

    public static function collect($path)
    {
        $files = glob($path . '/*/config/params.php');
        $params = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                $tmp = include $file;
                if (is_array($tmp) && !empty($tmp)) {
                    $module = str_replace($path . '/', '', $file);
                    $module = str_replace('/config/params.php', '', $module);
                    $params[$module] = $tmp;
                }
            }
        }
        return self::$params = $params;
    }

    /**
     * Возвращает пользовательский параметр приложения
     * @param string $key Ключ параметра или ключи вложенных параметров через точку
     * Например, 'Media.Foto.thumbsize' преобразуется в ['Media']['Foto']['thumbsize']
     * @param mixed $defaultValue Значение, возвращаемое в случае отсутствия ключа
     *
     * @return mixed
     */
    public static function get($key, $defaultValue = null)
    {
        return self::getKeyFromAlias($key, self::$params, $defaultValue);
    }

    /**
     * Возвращает значения ключа в заданном массиве
     * @param string $key Ключ или ключи точку
     * Например, 'Media.Foto.thumbsize' преобразуется в ['Media']['Foto']['thumbsize']
     * @param array $array Массив значений
     * @param mixed $defaultValue Значение, возвращаемое в случае отсутствия ключа
     *
     * @return mixed
     */
    public static function getKeyFromAlias($key, $array, $defaultValue = null)
    {
        if (strpos($key, '.') === false) {
            return (isset($array[$key])) ? $array[$key] : $defaultValue;
        }

        $keys = explode('.', $key);

        if (!isset($array[$keys[0]])) {
            return $defaultValue;
        }

        $value = $array[$keys[0]];
        unset($keys[0]);

        foreach ($keys as $k) {
            if(!is_array($value)) {
                return $defaultValue;
            }
            if (!isset($value[$k]) && !array_key_exists($k, $value)) {
                return $defaultValue;
            }
            $value = $value[$k];
        }

        return $value;
    }
}
