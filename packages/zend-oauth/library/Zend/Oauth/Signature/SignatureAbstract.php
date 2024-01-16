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

/** Zend_Oauth_Http_Utility */
// require_once 'Zend/Oauth/Http/Utility.php';

/** Zend_Uri_Http */
// require_once 'Zend/Uri/Http.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Oauth_Signature_SignatureAbstract
{
    /**
     * Hash algorithm to use when generating signature.
     *
     * @var string
     */
    protected $_hashAlgorithm;

    /**
     * Key to use when signing.
     *
     * @var string
     */
    protected $_key;

    /**
     * Consumer secret.
     *
     * @var string
     */
    protected $_consumerSecret;

    /**
     * Token secret.
     *
     * @var string
     */
    protected $_tokenSecret = '';

    /**
     * Constructor.
     *
     * @param string      $consumerSecret
     * @param string|null $tokenSecret
     * @param string|null $hashAlgo
     *
     * @return void
     */
    public function __construct($consumerSecret, $tokenSecret = null, $hashAlgo = null)
    {
        $this->_consumerSecret = $consumerSecret;
        if (isset($tokenSecret)) {
            $this->_tokenSecret = $tokenSecret;
        }
        $this->_key = $this->_assembleKey();
        if (isset($hashAlgo)) {
            $this->_hashAlgorithm = $hashAlgo;
        }
    }

    /**
     * Sign a request.
     *
     * @param string|null $method
     * @param string|null $url
     *
     * @return string
     */
    abstract public function sign(array $params, $method = null, $url = null);

    /**
     * Normalize the base signature URL.
     *
     * @param string $url
     *
     * @return string
     */
    public function normaliseBaseSignatureUrl($url)
    {
        $uri = Zend_Uri_Http::fromString($url);
        if ('http' == $uri->getScheme() && '80' == $uri->getPort()) {
            $uri->setPort('');
        } elseif ('https' == $uri->getScheme() && '443' == $uri->getPort()) {
            $uri->setPort('');
        }
        $uri->setQuery('');
        $uri->setFragment('');
        $uri->setHost(strtolower((string) $uri->getHost()));

        return $uri->getUri(true);
    }

    /**
     * Assemble key from consumer and token secrets.
     *
     * @return string
     */
    protected function _assembleKey()
    {
        $parts = [$this->_consumerSecret];
        if (null !== $this->_tokenSecret) {
            $parts[] = $this->_tokenSecret;
        }
        foreach ($parts as $key => $secret) {
            $parts[$key] = Zend_Oauth_Http_Utility::urlEncode($secret);
        }

        return implode('&', $parts);
    }

    /**
     * Get base signature string.
     *
     * @param string|null $method
     * @param string|null $url
     *
     * @return string
     */
    protected function _getBaseSignatureString(array $params, $method = null, $url = null)
    {
        $encodedParams = [];
        foreach ($params as $key => $value) {
            $encodedParams[Zend_Oauth_Http_Utility::urlEncode($key)] =
                Zend_Oauth_Http_Utility::urlEncode($value);
        }
        $baseStrings = [];
        if (isset($method)) {
            $baseStrings[] = strtoupper((string) $method);
        }
        if (isset($url)) {
            // should normalise later
            $baseStrings[] = Zend_Oauth_Http_Utility::urlEncode(
                $this->normaliseBaseSignatureUrl($url)
            );
        }
        if (isset($encodedParams['oauth_signature'])) {
            unset($encodedParams['oauth_signature']);
        }
        $baseStrings[] = Zend_Oauth_Http_Utility::urlEncode(
            $this->_toByteValueOrderedQueryString($encodedParams)
        );

        return implode('&', $baseStrings);
    }

    /**
     * Transform an array to a byte value ordered query string.
     *
     * @return string
     */
    protected function _toByteValueOrderedQueryString(array $params)
    {
        $return = [];
        uksort($params, 'strnatcmp');
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                natsort($value);
                foreach ($value as $keyduplicate) {
                    $return[] = $key.'='.$keyduplicate;
                }
            } else {
                $return[] = $key.'='.$value;
            }
        }

        return implode('&', $return);
    }
}
