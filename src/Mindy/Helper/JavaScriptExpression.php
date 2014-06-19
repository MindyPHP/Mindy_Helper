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
 * @date 19/06/14.06.2014 13:32
 */

namespace Mindy\Helper;

/**
 * CJavaScriptExpression class file.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2012 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
use Exception;

/**
 * CJavaScriptExpression represents a JavaScript expression that does not need escaping.
 * It can be passed to {@link CJavaScript::encode()} and the code will stay as is.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @package system.web.helpers
 * @since 1.1.11
 */
class JavaScriptExpression
{
    /**
     * @var string the javascript expression wrapped by this object
     */
    public $code;

    /**
     * @param string $code a javascript expression that is to be wrapped by this object
     * @throws Exception if argument is not a string
     */
    public function __construct($code)
    {
        if (!is_string($code)) {
            throw new Exception('Value passed to CJavaScriptExpression should be a string.');
        }
        if (strpos($code, 'js:') === 0) {
            $code = substr($code, 3);
        }
        $this->code = $code;
    }

    /**
     * String magic method
     * @return string the javascript expression wrapped by this object
     */
    public function __toString()
    {
        return $this->code;
    }
}
