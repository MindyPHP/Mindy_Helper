<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 13/10/2016
 * Time: 21:10
 */

namespace Mindy\Helper;

/**
 * Renders a view file.
 * This method includes the view file as a PHP script
 * and captures the display result if required.
 * @param string $_viewFile_ view file
 * @param array $_data_ data to be extracted and made available to the view file
 * @return string the rendering result. Null if the rendering result is not required.
 */
function renderTemplate($_viewFile_, $_data_ = null)
{
    // we use special variable names here to avoid conflict when extracting data
    if (is_array($_data_)) {
        extract($_data_, EXTR_PREFIX_SAME, 'data');
    } else {
        $data = $_data_;
    }

    ob_start();
    ob_implicit_flush(false);
    require($_viewFile_);
    return ob_get_clean();
}

/**
 * Returns given word as CamelCased
 * Converts a word like "send_email" to "SendEmail". It
 * will remove non alphanumeric character from the word, so
 * "who's online" will be converted to "WhoSOnline"
 * @see variablize()
 * @param string $word the word to CamelCase
 * @return string
 */
function toCamelCase($word)
{
    return lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^A-Za-z0-9]+/', ' ', $word))));
}

/**
 * Converts any "CamelCased" into an "underscored_word".
 * @param string $words the word(s) to underscore
 * @return string
 */
function toUnderscore($words)
{
    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $words));
}