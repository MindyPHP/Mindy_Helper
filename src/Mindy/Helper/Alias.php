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

namespace Mindy\Helper;


class Alias
{
    private static $_aliases = [];

    /**
     * Translates an alias into a file path.
     * Note, this method does not ensure the existence of the resulting file path.
     * It only checks if the root alias is valid or not.
     * @param string $alias alias (e.g. system.web.CController)
     * @return mixed file path corresponding to the alias, false if the alias is invalid.
     */
    public static function get($alias)
    {
        if (isset(self::$_aliases[$alias])) {
            return self::$_aliases[$alias];
        } elseif (($pos = strpos($alias, '.')) !== false) {
            $rootAlias = substr($alias, 0, $pos);
            if (isset(self::$_aliases[$rootAlias])) {
                return self::$_aliases[$alias] = rtrim(self::$_aliases[$rootAlias] . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, substr($alias, $pos + 1)), '*' . DIRECTORY_SEPARATOR);
            }
//            elseif (self::$_app instanceof CWebApplication) {
//                if (self::$_app->findModule($rootAlias) !== null)
//                    return self::getPathOfAlias($alias);
//            }
        }
        return false;
    }

    /**
     * Create a path alias.
     * Note, this method neither checks the existence of the path nor normalizes the path.
     * @param string $alias alias to the path
     * @param string $path the path corresponding to the alias. If this is null, the corresponding
     * path alias will be removed.
     */
    public static function set($alias, $path)
    {
        if (empty($path)) {
            unset(self::$_aliases[$alias]);
        } else {
            self::$_aliases[$alias] = rtrim($path, '\\/');
        }
    }
}
