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
 * @date 12/05/14.05.2014 18:25
 */

declare(strict_types=1);

namespace Mindy\Helper;

use InvalidArgumentException;

/**
 * Class Alias
 * @package Mindy\Helper
 */
class Alias
{
    private static $_aliases = [];

    public static function all()
    {
        return self::$_aliases;
    }

    /**
     * Translates an alias into a file path.
     * Note, this method does not ensure the existence of the resulting file path.
     * It only checks if the root alias is valid or not.
     * @param string $alias alias (e.g. system.web.CController)
     * @throws \InvalidArgumentException
     * @return mixed file path corresponding to the alias, false if the alias is invalid.
     */
    public static function get(string $alias)
    {
        if (!is_string($alias)) {
            throw new InvalidArgumentException("Alias must be a string. " . gettype($alias) . " given.");
        }

        $alias = strtolower($alias);
        if (isset(self::$_aliases[$alias])) {
            return self::$_aliases[$alias];
        } elseif (($pos = strpos($alias, '.')) !== false) {
            $tmp = explode('.', $alias);
            $parentAlias = str_replace("." . end($tmp), "", $alias);

            if (isset(self::$_aliases[$parentAlias])) {
                $path = rtrim(self::$_aliases[$parentAlias] . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, end($tmp)), '*' . DIRECTORY_SEPARATOR);
                return self::$_aliases[$alias] = $path;
            } else {
                $rootAlias = substr($alias, 0, $pos);
                if (isset(self::$_aliases[$rootAlias])) {
                    return self::$_aliases[$alias] = rtrim(self::$_aliases[$rootAlias] . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, substr($alias, $pos + 1)), '*' . DIRECTORY_SEPARATOR);
                }
            }
        }
        return false;
    }

    public static function find($alias)
    {
        if (!is_string($alias)) {
            throw new InvalidArgumentException("Alias must be a string. " . gettype($alias) . " given.");
        }
        $alias = strtolower($alias);
        $found = [];
        $parentAlias = str_replace('.*', '', $alias);
        foreach (self::$_aliases as $aliasPath => $path) {
            if ($parentAlias === "" || mb_strpos($aliasPath, $parentAlias, 0, 'UTF-8') === 0) {
                $cleanAlias = substr_replace($aliasPath, '', 0, strlen($parentAlias) + 1);
                if (strlen($cleanAlias) > 0 && strpos($cleanAlias, '.') === false) {
                    $found[] = $path;
                }
            }
        }
        return $found;
    }

    /**
     * Create a path alias.
     * Note, this method neither checks the existence of the path nor normalizes the path.
     * @param string $alias alias to the path
     * @param string $path the path corresponding to the alias. If this is null, the corresponding
     * path alias will be removed.
     */
    public static function set(string $alias, string $path)
    {
        if (empty($path)) {
            unset(self::$_aliases[strtolower($alias)]);
        } else {
            self::$_aliases[strtolower($alias)] = rtrim($path, '\\/');
        }
    }
}
