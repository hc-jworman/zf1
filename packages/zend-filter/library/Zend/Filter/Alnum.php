<?php
/**
 * Zend Framework.
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Interface
 */
// require_once 'Zend/Filter/Interface.php';
/**
 * @see Zend_Locale
 */
// require_once 'Zend/Locale.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Alnum implements Zend_Filter_Interface
{
    /**
     * Whether to allow white space characters; off by default.
     *
     * @var bool
     *
     * @deprecated
     */
    public $allowWhiteSpace;

    /**
     * Is PCRE is compiled with UTF-8 and Unicode support.
     *
     **/
    protected static $_unicodeEnabled;

    /**
     * Locale in browser.
     *
     * @var Zend_Locale object
     */
    protected $_locale;

    /**
     * The Alphabet means english alphabet.
     *
     * @var bool
     */
    protected static $_meansEnglishAlphabet;

    /**
     * Sets default option values for this instance.
     *
     * @param bool $allowWhiteSpace
     *
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        if ($allowWhiteSpace instanceof Zend_Config) {
            $allowWhiteSpace = $allowWhiteSpace->toArray();
        } elseif (is_array($allowWhiteSpace)) {
            if (array_key_exists('allowwhitespace', $allowWhiteSpace)) {
                $allowWhiteSpace = $allowWhiteSpace['allowwhitespace'];
            } else {
                $allowWhiteSpace = false;
            }
        }

        $this->allowWhiteSpace = (bool) $allowWhiteSpace;
        if (null === self::$_unicodeEnabled) {
            self::$_unicodeEnabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        }

        if (null === self::$_meansEnglishAlphabet) {
            $this->_locale = new Zend_Locale('auto');
            self::$_meansEnglishAlphabet = in_array($this->_locale->getLanguage(),
                ['ja', 'ko', 'zh']
            );
        }
    }

    /**
     * Returns the allowWhiteSpace option.
     *
     * @return bool
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Sets the allowWhiteSpace option.
     *
     * @param bool $allowWhiteSpace
     *
     * @return Zend_Filter_Alnum Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (bool) $allowWhiteSpace;

        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface.
     *
     * Returns the string $value, removing all but alphabetic and digit characters
     *
     * @param string $value
     *
     * @return string
     */
    public function filter($value)
    {
        $whiteSpace = $this->allowWhiteSpace ? '\s' : '';
        if (!self::$_unicodeEnabled) {
            // POSIX named classes are not supported, use alternative a-zA-Z0-9 match
            $pattern = '/[^a-zA-Z0-9'.$whiteSpace.']/';
        } elseif (self::$_meansEnglishAlphabet) {
            // The Alphabet means english alphabet.
            $pattern = '/[^a-zA-Z0-9'.$whiteSpace.']/u';
        } else {
            // The Alphabet means each language's alphabet.
            $pattern = '/[^\p{L}\p{N}'.$whiteSpace.']/u';
        }

        return preg_replace($pattern, '', (string) $value);
    }
}
