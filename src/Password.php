<?php

declare(strict_types = 1);

namespace Mindy\Helper;

use Exception;

/**
 * Class Password
 * @package Mindy\Helper
 */
class Password
{
    /**
     * Check for availability of PHP crypt() with the Blowfish hash option.
     * @throws Exception if the runtime system does not have PHP crypt() or its Blowfish hash option.
     */
    protected static function checkBlowfish()
    {
        if (!function_exists('crypt')) {
            throw new Exception(__CLASS__ . ' requires the PHP crypt() function. This system does not have it.');
        }

        if (!defined('CRYPT_BLOWFISH') || !CRYPT_BLOWFISH) {
            throw new Exception(__CLASS__ . ' requires the Blowfish option of the PHP crypt() function. This system does not have it.');
        }
    }

    /**
     * Generate a secure hash from a password and a random salt.
     *
     * Uses the
     * PHP {@link http://php.net/manual/en/function.crypt.php crypt()} built-in function
     * with the Blowfish hash option.
     *
     * @param string $password The password to be hashed.
     * @param int $cost Cost parameter used by the Blowfish hash algorithm.
     * The higher the value of cost,
     * the longer it takes to generate the hash and to verify a password against it. Higher cost
     * therefore slows down a brute-force attack. For best protection against brute for attacks,
     * set it to the highest value that is tolerable on production servers. The time taken to
     * compute the hash doubles for every increment by one of $cost. So, for example, if the
     * hash takes 1 second to compute when $cost is 14 then then the compute time varies as
     * 2^($cost - 14) seconds.
     * @return string The password hash string, ASCII and not longer than 64 characters.
     * @throws Exception on bad password parameter or if crypt() with Blowfish hash is not available.
     */
    public static function hashPassword(string $password, int $cost = 13) : string
    {
        self::checkBlowfish();
        $hash = crypt($password, self::generateSalt($cost));

        if (!is_string($hash) || mb_strlen($hash, '8bit') < 32) {
            throw new Exception('Internal error while generating hash.');
        }

        return $hash;
    }

    /**
     * Verify a password against a hash.
     *
     * @param string $password The password to verify. If password is empty or not a string, method will return false.
     * @param string $hash The hash to verify the password against.
     * @return bool True if the password matches the hash.
     * @throws Exception on bad password or hash parameters or if crypt() with Blowfish hash is not available.
     */
    public static function verifyPassword(string $password, string $hash) : bool
    {
        self::checkBlowfish();
        if ($password === '') {
            return false;
        }

        if (!preg_match('{^\$2[axy]\$(\d\d)\$[\./0-9A-Za-z]{22}}', $hash, $matches) || $matches[1] < 4 || $matches[1] > 31) {
            return false;
        }

        $test = crypt($password, $hash);
        if (!is_string($test) || strlen($test) < 32) {
            return false;
        }

        return password_verify($test, $hash);
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     *
     * The PHP {@link http://php.net/manual/en/function.crypt.php crypt()} built-in function
     * requires, for the Blowfish hash algorithm, a salt string in a specific format:
     *  "$2a$" (in which the "a" may be replaced by "x" or "y" see PHP manual for details),
     *  a two digit cost parameter,
     *  "$",
     *  22 characters from the alphabet "./0-9A-Za-z".
     *
     * @param int $cost Cost parameter used by the Blowfish hash algorithm.
     * @return string the random salt value.
     * @throws Exception in case of invalid cost number
     */
    public static function generateSalt(int $cost = 13) : string
    {
        if ($cost < 4 || $cost > 31) {
            throw new Exception(__CLASS__ . '::$cost must be between 4 and 31.');
        }

        return sprintf('$2a$%02d$', $cost) . strtr(random_bytes($cost), ['_' => '.', '~' => '/']);
    }
}
