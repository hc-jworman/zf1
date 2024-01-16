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
 * @see Zend_Controller_Response_Abstract
 */
// require_once "Zend/Controller/Response/Abstract.php";

/** @see Zend_Crypt_Math */
// require_once 'Zend/Crypt/Math.php';

/**
 * Static class that contains common utility functions for
 * {@link Zend_OpenId_Consumer} and {@link Zend_OpenId_Provider}.
 *
 * This class implements common utility functions that are used by both
 * Consumer and Provider. They include functions for Diffie-Hellman keys
 * generation and exchange, URL normalization, HTTP redirection and some others.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_OpenId
{
    /**
     * Default Diffie-Hellman key generator (1024 bit).
     */
    public const DH_P = 'dcf93a0b883972ec0e19989ac5a2ce310e1d37717e8d9571bb7623731866e61ef75a2e27898b057f9891c2e27a639c3f29b60814581cd3b2ca3986d2683705577d45c2e7e52dc81c7a171876e5cea74b1448bfdfaf18828efd2519f14e45e3826634af1949e5b535cc829a483b8a76223e5d490a257f05bdff16f2fb22c583ab';

    /**
     * Default Diffie-Hellman prime number (should be 2 or 5).
     */
    public const DH_G = '02';

    /**
     * OpenID 2.0 namespace. All OpenID 2.0 messages MUST contain variable
     * openid.ns with its value.
     */
    public const NS_2_0 = 'http://specs.openid.net/auth/2.0';

    /**
     * Allows enable/disable stoping execution of PHP script after redirect().
     */
    public static $exitOnRedirect = true;

    /**
     * Alternative request URL that can be used to override the default
     * selfUrl() response.
     */
    public static $selfUrl;

    /**
     * Sets alternative request URL that can be used to override the default
     * selfUrl() response.
     *
     * @param string $selfUrl the URL to be set
     *
     * @return string|null the old value of overriding URL
     */
    public static function setSelfUrl($selfUrl = null)
    {
        $ret = self::$selfUrl;
        self::$selfUrl = $selfUrl;

        return $ret;
    }

    /**
     * Returns a full URL that was requested on current HTTP request.
     *
     * @return string
     */
    public static function selfUrl()
    {
        if (null !== self::$selfUrl) {
            return self::$selfUrl;
        } if (isset($_SERVER['SCRIPT_URI'])) {
            return $_SERVER['SCRIPT_URI'];
        }
        $url = '';
        $port = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            if (($pos = strpos((string) $_SERVER['HTTP_HOST'], ':')) === false) {
                if (isset($_SERVER['SERVER_PORT'])) {
                    $port = ':'.$_SERVER['SERVER_PORT'];
                }
                $url = $_SERVER['HTTP_HOST'];
            } else {
                $url = substr((string) $_SERVER['HTTP_HOST'], 0, $pos);
                $port = substr((string) $_SERVER['HTTP_HOST'], $pos);
            }
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $url = $_SERVER['SERVER_NAME'];
            if (isset($_SERVER['SERVER_PORT'])) {
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        }
        if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
            $url = 'https://'.$url;
            if (':443' == $port) {
                $port = '';
            }
        } else {
            $url = 'http://'.$url;
            if (':80' == $port) {
                $port = '';
            }
        }

        $url .= $port;
        if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
            // IIS with Microsoft Rewrite Module
            $url .= $_SERVER['HTTP_X_ORIGINAL_URL'];
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            // IIS with ISAPI_Rewrite
            $url .= $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $query = strpos((string) $_SERVER['REQUEST_URI'], '?');
            if (false === $query) {
                $url .= $_SERVER['REQUEST_URI'];
            } else {
                $url .= substr((string) $_SERVER['REQUEST_URI'], 0, $query);
            }
        } elseif (isset($_SERVER['SCRIPT_URL'])) {
            $url .= $_SERVER['SCRIPT_URL'];
        } elseif (isset($_SERVER['REDIRECT_URL'])) {
            $url .= $_SERVER['REDIRECT_URL'];
        } elseif (isset($_SERVER['PHP_SELF'])) {
            $url .= $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['SCRIPT_NAME'])) {
            $url .= $_SERVER['SCRIPT_NAME'];
            if (isset($_SERVER['PATH_INFO'])) {
                $url .= $_SERVER['PATH_INFO'];
            }
        }

        return $url;
    }

    /**
     * Returns an absolute URL for the given one.
     *
     * @param string $url absilute or relative URL
     *
     * @return string
     */
    public static function absoluteUrl($url)
    {
        if (empty($url)) {
            return Zend_OpenId::selfUrl();
        } elseif (!preg_match('|^([^:]+)://|', $url)) {
            if (preg_match('|^([^:]+)://([^:@]*(?:[:][^@]*)?@)?([^/:@?#]*)(?:[:]([^/?#]*))?(/[^?]*)?((?:[?](?:[^#]*))?(?:#.*)?)$|', Zend_OpenId::selfUrl(), $reg)) {
                $scheme = $reg[1];
                $auth = $reg[2];
                $host = $reg[3];
                $port = $reg[4];
                $path = $reg[5];
                $query = $reg[6];
                if ('/' == $url[0]) {
                    return $scheme
                        .'://'
                        .$auth
                        .$host
                        .(empty($port) ? '' : (':'.$port))
                        .$url;
                } else {
                    $dir = dirname($path);

                    return $scheme
                        .'://'
                        .$auth
                        .$host
                        .(empty($port) ? '' : (':'.$port))
                        .(strlen((string) $dir) > 1 ? $dir : '')
                        .'/'
                        .$url;
                }
            }
        }

        return $url;
    }

    /**
     * Converts variable/value pairs into URL encoded query string.
     *
     * @param array $params variable/value pairs
     *
     * @return string URL encoded query string
     */
    public static function paramsToQuery($params)
    {
        foreach ($params as $key => $value) {
            if (isset($query)) {
                $query .= '&'.$key.'='.urlencode((string) $value);
            } else {
                $query = $key.'='.urlencode((string) $value);
            }
        }

        return isset($query) ? $query : '';
    }

    /**
     * Normalizes URL according to RFC 3986 to use it in comparison operations.
     * The function gets URL argument by reference and modifies it.
     * It returns true on success and false of failure.
     *
     * @param string &$id url to be normalized
     *
     * @return bool
     */
    public static function normalizeUrl(&$id)
    {
        // RFC 3986, 6.2.2.  Syntax-Based Normalization

        // RFC 3986, 6.2.2.2 Percent-Encoding Normalization
        $i = 0;
        $n = strlen((string) $id);
        $res = '';
        while ($i < $n) {
            if ('%' == $id[$i]) {
                if ($i + 2 >= $n) {
                    return false;
                }
                ++$i;
                if ($id[$i] >= '0' && $id[$i] <= '9') {
                    $c = ord((string) $id[$i]) - ord((string) '0');
                } elseif ($id[$i] >= 'A' && $id[$i] <= 'F') {
                    $c = ord((string) $id[$i]) - ord((string) 'A') + 10;
                } elseif ($id[$i] >= 'a' && $id[$i] <= 'f') {
                    $c = ord((string) $id[$i]) - ord((string) 'a') + 10;
                } else {
                    return false;
                }
                ++$i;
                if ($id[$i] >= '0' && $id[$i] <= '9') {
                    $c = ($c << 4) | (ord((string) $id[$i]) - ord((string) '0'));
                } elseif ($id[$i] >= 'A' && $id[$i] <= 'F') {
                    $c = ($c << 4) | (ord((string) $id[$i]) - ord((string) 'A') + 10);
                } elseif ($id[$i] >= 'a' && $id[$i] <= 'f') {
                    $c = ($c << 4) | (ord((string) $id[$i]) - ord((string) 'a') + 10);
                } else {
                    return false;
                }
                ++$i;
                $ch = chr($c);
                if (($ch >= 'A' && $ch <= 'Z')
                    || ($ch >= 'a' && $ch <= 'z')
                    || '-' == $ch
                    || '.' == $ch
                    || '_' == $ch
                    || '~' == $ch) {
                    $res .= $ch;
                } else {
                    $res .= '%';
                    if (($c >> 4) < 10) {
                        $res .= chr(($c >> 4) + ord((string) '0'));
                    } else {
                        $res .= chr(($c >> 4) - 10 + ord((string) 'A'));
                    }
                    $c = $c & 0xF;
                    if ($c < 10) {
                        $res .= chr($c + ord((string) '0'));
                    } else {
                        $res .= chr($c - 10 + ord((string) 'A'));
                    }
                }
            } else {
                $res .= $id[$i++];
            }
        }

        if (!preg_match('|^([^:]+)://([^:@]*(?:[:][^@]*)?@)?([^/:@?#]*)(?:[:]([^/?#]*))?(/[^?#]*)?((?:[?](?:[^#]*))?)((?:#.*)?)$|', $res, $reg)) {
            return false;
        }
        $scheme = $reg[1];
        $auth = $reg[2];
        $host = $reg[3];
        $port = $reg[4];
        $path = $reg[5];
        $query = $reg[6];
        $fragment = $reg[7]; /* strip it */ /* ZF-4358 Fragment retained under OpenID 2.0 */

        if (empty($scheme) || empty($host)) {
            return false;
        }

        // RFC 3986, 6.2.2.1.  Case Normalization
        $scheme = strtolower((string) $scheme);
        $host = strtolower((string) $host);

        // RFC 3986, 6.2.2.3.  Path Segment Normalization
        if (!empty($path)) {
            $i = 0;
            $n = strlen((string) $path);
            $res = '';
            while ($i < $n) {
                if ('/' == $path[$i]) {
                    ++$i;
                    while ($i < $n && '/' == $path[$i]) {
                        ++$i;
                    }
                    if ($i < $n && '.' == $path[$i]) {
                        ++$i;
                        if ($i < $n && '.' == $path[$i]) {
                            ++$i;
                            if ($i == $n || '/' == $path[$i]) {
                                if (($pos = strrpos($res, '/')) !== false) {
                                    $res = substr((string) $res, 0, $pos);
                                }
                            } else {
                                $res .= '/..';
                            }
                        } elseif ($i != $n && '/' != $path[$i]) {
                            $res .= '/.';
                        }
                    } else {
                        $res .= '/';
                    }
                } else {
                    $res .= $path[$i++];
                }
            }
            $path = $res;
        }

        // RFC 3986,6.2.3.  Scheme-Based Normalization
        if ('http' == $scheme) {
            if (80 == $port) {
                $port = '';
            }
        } elseif ('https' == $scheme) {
            if (443 == $port) {
                $port = '';
            }
        }
        if (empty($path)) {
            $path = '/';
        }

        $id = $scheme
            .'://'
            .$auth
            .$host
            .(empty($port) ? '' : (':'.$port))
            .$path
            .$query
            .$fragment;

        return true;
    }

    /**
     * Normalizes OpenID identifier that can be URL or XRI name.
     * Returns true on success and false of failure.
     *
     * Normalization is performed according to the following rules:
     * 1. If the user's input starts with one of the "xri://", "xri://$ip*",
     *    or "xri://$dns*" prefixes, they MUST be stripped off, so that XRIs
     *    are used in the canonical form, and URI-authority XRIs are further
     *    considered URL identifiers.
     * 2. If the first character of the resulting string is an XRI Global
     *    Context Symbol ("=", "@", "+", "$", "!"), then the input SHOULD be
     *    treated as an XRI.
     * 3. Otherwise, the input SHOULD be treated as an http URL; if it does
     *    not include a "http" or "https" scheme, the Identifier MUST be
     *    prefixed with the string "http://".
     * 4. URL identifiers MUST then be further normalized by both following
     *    redirects when retrieving their content and finally applying the
     *    rules in Section 6 of [RFC3986] to the final destination URL.
     *
     * @param string &$id identifier to be normalized
     *
     * @return bool
     */
    public static function normalize(&$id)
    {
        $id = \trim((string) $id);
        if (0 === strlen((string) $id)) {
            return true;
        }

        // 7.2.1
        if (0 === strpos((string) $id, 'xri://$ip*')) {
            $id = substr((string) $id, strlen((string) 'xri://$ip*'));
        } elseif (0 === strpos((string) $id, 'xri://$dns*')) {
            $id = substr((string) $id, strlen((string) 'xri://$dns*'));
        } elseif (0 === strpos((string) $id, 'xri://')) {
            $id = substr((string) $id, strlen((string) 'xri://'));
        }

        // 7.2.2
        if ('=' == $id[0]
            || '@' == $id[0]
            || '+' == $id[0]
            || '$' == $id[0]
            || '!' == $id[0]) {
            return true;
        }

        // 7.2.3
        if (false === strpos((string) $id, '://')) {
            $id = 'http://'.$id;
        }

        // 7.2.4
        return self::normalizeURL($id);
    }

    /**
     * Performs a HTTP redirection to specified URL with additional data.
     * It may generate redirected request using GET or POST HTTP method.
     * The function never returns.
     *
     * @param string $url    URL to redirect to
     * @param array  $params additional variable/value pairs to send
     * @param string $method redirection method ('GET' or 'POST')
     */
    public static function redirect($url, $params = null,
        Zend_Controller_Response_Abstract $response = null, $method = 'GET')
    {
        $url = Zend_OpenId::absoluteUrl($url);
        $body = '';
        if (null === $response) {
            // require_once "Zend/Controller/Response/Http.php";
            $response = new Zend_Controller_Response_Http();
        }

        if ('POST' == $method) {
            $body = "<html><body onLoad=\"document.forms[0].submit();\">\n";
            $body .= "<form method=\"POST\" action=\"$url\">\n";
            if (is_array($params) && count($params) > 0) {
                foreach ($params as $key => $value) {
                    $body .= '<input type="hidden" name="'.$key.'" value="'.$value."\">\n";
                }
            }
            $body .= "<input type=\"submit\" value=\"Continue OpenID transaction\">\n";
            $body .= "</form></body></html>\n";
        } elseif (is_array($params) && count($params) > 0) {
            if (false === strpos((string) $url, '?')) {
                $url .= '?'.self::paramsToQuery($params);
            } else {
                $url .= '&'.self::paramsToQuery($params);
            }
        }
        if (!empty($body)) {
            $response->setBody($body);
        } elseif (!$response->canSendHeaders()) {
            $response->setBody('<script language="JavaScript"'.
                 " type=\"text/javascript\">window.location='$url';".
                 '</script>');
        } else {
            $response->setRedirect($url);
        }
        $response->sendResponse();
        if (self::$exitOnRedirect) {
            exit;
        }
    }

    /**
     * Produces string of random byte of given length.
     *
     * @param int $len length of requested string
     *
     * @return string RAW random binary string
     */
    public static function randomBytes($len)
    {
        return (string) Zend_Crypt_Math::randBytes($len);
    }

    /**
     * Generates a hash value (message digest) according to given algorithm.
     * It returns RAW binary string.
     *
     * This is a wrapper function that uses one of available internal function
     * dependent on given PHP configuration. It may use various functions from
     *  ext/openssl, ext/hash, ext/mhash or ext/standard.
     *
     * @param string $func digest algorithm
     * @param string $data data to sign
     *
     * @return string RAW digital signature
     *
     * @throws Zend_OpenId_Exception
     */
    public static function digest($func, $data)
    {
        if (function_exists('openssl_digest')) {
            return openssl_digest($data, $func, true);
        } elseif (function_exists('hash')) {
            return hash($func, $data, true);
        } elseif ('sha1' === $func) {
            return sha1($data, true);
        } elseif ('sha256' === $func) {
            if (function_exists('mhash')) {
                return mhash(MHASH_SHA256, $data);
            }
        }
        // require_once "Zend/OpenId/Exception.php";
        throw new Zend_OpenId_Exception('Unsupported digest algorithm "'.$func.'".', Zend_OpenId_Exception::UNSUPPORTED_DIGEST);
    }

    /**
     * Generates a keyed hash value using the HMAC method. It uses ext/hash
     * if available or user-level PHP implementation, that is not significantly
     * slower.
     *
     * @param string $macFunc name of selected hashing algorithm (sha1, sha256)
     * @param string $data    data to sign
     * @param string $secret  shared secret key used for generating the HMAC
     *                        variant of the message digest
     *
     * @return string RAW HMAC value
     */
    public static function hashHmac($macFunc, $data, $secret)
    {
        //        // require_once "Zend/Crypt/Hmac.php";
        //        return Zend_Crypt_Hmac::compute($secret, $macFunc, $data, Zend_Crypt_Hmac::BINARY);
        if (function_exists('hash_hmac')) {
            return hash_hmac($macFunc, $data, $secret, true);
        } else {
            if (Zend_OpenId::strlen((string) $secret) > 64) {
                $secret = self::digest($macFunc, $secret);
            }
            $secret = str_pad($secret, 64, chr(0x00));
            $ipad = str_repeat(chr(0x36), 64);
            $opad = str_repeat(chr(0x5C), 64);
            $hash1 = self::digest($macFunc, ($secret ^ $ipad).$data);

            return self::digest($macFunc, ($secret ^ $opad).$hash1);
        }
    }

    /**
     * Converts binary representation into ext/gmp or ext/bcmath big integer
     * representation.
     *
     * @param string $bin binary representation of big number
     *
     * @return GMP|int|resource|string
     *
     * @throws Zend_OpenId_Exception
     */
    protected static function binToBigNum($bin)
    {
        if (extension_loaded('gmp')) {
            return gmp_init(bin2hex($bin), 16);
        } elseif (extension_loaded('bcmath')) {
            $bn = 0;
            $len = Zend_OpenId::strlen((string) $bin);
            for ($i = 0; $i < $len; ++$i) {
                $bn = bcmul($bn, 256);
                $bn = bcadd($bn, ord((string) $bin[$i]));
            }

            return $bn;
        }
        // require_once "Zend/OpenId/Exception.php";
        throw new Zend_OpenId_Exception('The system doesn\'t have proper big integer extension', Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Converts internal ext/gmp or ext/bcmath big integer representation into
     * binary string.
     *
     * @param mixed $bn big number
     *
     * @return string
     *
     * @throws Zend_OpenId_Exception
     */
    protected static function bigNumToBin($bn)
    {
        if (extension_loaded('gmp')) {
            $s = gmp_strval($bn, 16);
            if (0 != strlen((string) $s) % 2) {
                $s = '0'.$s;
            } elseif ($s[0] > '7') {
                $s = '00'.$s;
            }

            return pack('H*', $s);
        } elseif (extension_loaded('bcmath')) {
            $cmp = bccomp($bn, 0);
            if (0 == $cmp) {
                return "\0";
            } elseif ($cmp < 0) {
                // require_once "Zend/OpenId/Exception.php";
                throw new Zend_OpenId_Exception('Big integer arithmetic error', Zend_OpenId_Exception::ERROR_LONG_MATH);
            }
            $bin = '';
            while (bccomp($bn, 0) > 0) {
                $bin = chr(bcmod($bn, 256)).$bin;
                $bn = bcdiv($bn, 256);
            }
            if (ord((string) $bin[0]) > 127) {
                $bin = "\0".$bin;
            }

            return $bin;
        }
        // require_once "Zend/OpenId/Exception.php";
        throw new Zend_OpenId_Exception('The system doesn\'t have proper big integer extension', Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Performs the first step of a Diffie-Hellman key exchange by generating
     * private and public DH values based on given prime number $p and
     * generator $g. Both sides of key exchange MUST have the same prime number
     * and generator. In this case they will able to create a random shared
     * secret that is never send from one to the other.
     *
     * @param string $p        prime number in binary representation
     * @param string $g        generator in binary representation
     * @param string $priv_key private key in binary representation
     *
     * @return array|false|resource
     */
    public static function createDhKey($p, $g, $priv_key = null)
    {
        if (function_exists('openssl_dh_compute_key')) {
            $dh_details = [
                    'p' => $p,
                    'g' => $g,
                ];
            if (null !== $priv_key) {
                $dh_details['priv_key'] = $priv_key;
            }

            return openssl_pkey_new(['dh' => $dh_details]);
        } else {
            $bn_p = self::binToBigNum($p);
            $bn_g = self::binToBigNum($g);
            if (null === $priv_key) {
                $priv_key = self::randomBytes(Zend_OpenId::strlen((string) $p));
            }
            $bn_priv_key = self::binToBigNum($priv_key);
            if (extension_loaded('gmp')) {
                $bn_pub_key = gmp_powm($bn_g, $bn_priv_key, $bn_p);
            } elseif (extension_loaded('bcmath')) {
                $bn_pub_key = bcpowmod($bn_g, $bn_priv_key, $bn_p);
            }
            $pub_key = self::bigNumToBin($bn_pub_key);

            return [
                'p' => $bn_p,
                'g' => $bn_g,
                'priv_key' => $bn_priv_key,
                'pub_key' => $bn_pub_key,
                'details' => [
                    'p' => $p,
                    'g' => $g,
                    'priv_key' => $priv_key,
                    'pub_key' => $pub_key]];
        }
    }

    /**
     * Returns an associative array with Diffie-Hellman key components in
     * binary representation. The array includes original prime number 'p' and
     * generator 'g', random private key 'priv_key' and corresponding public
     * key 'pub_key'.
     *
     * @param mixed $dh Diffie-Hellman key
     *
     * @return array
     */
    public static function getDhKeyDetails($dh)
    {
        if (function_exists('openssl_dh_compute_key')) {
            $details = openssl_pkey_get_details($dh);
            if (isset($details['dh'])) {
                return $details['dh'];
            }
        } else {
            return $dh['details'];
        }
    }

    /**
     * Computes the shared secret from the private DH value $dh and the other
     * party's public value in $pub_key.
     *
     * @param string $pub_key other party's public value
     * @param mixed  $dh      Diffie-Hellman key
     *
     * @return string
     *
     * @throws Zend_OpenId_Exception
     */
    public static function computeDhSecret($pub_key, $dh)
    {
        if (function_exists('openssl_dh_compute_key')) {
            $ret = openssl_dh_compute_key($pub_key, $dh);
            if (ord((string) $ret[0]) > 127) {
                $ret = "\0".$ret;
            }

            return $ret;
        } elseif (extension_loaded('gmp')) {
            $bn_pub_key = self::binToBigNum($pub_key);
            $bn_secret = gmp_powm($bn_pub_key, $dh['priv_key'], $dh['p']);

            return self::bigNumToBin($bn_secret);
        } elseif (extension_loaded('bcmath')) {
            $bn_pub_key = self::binToBigNum($pub_key);
            $bn_secret = bcpowmod($bn_pub_key, $dh['priv_key'], $dh['p']);

            return self::bigNumToBin($bn_secret);
        }
        // require_once "Zend/OpenId/Exception.php";
        throw new Zend_OpenId_Exception('The system doesn\'t have proper big integer extension', Zend_OpenId_Exception::UNSUPPORTED_LONG_MATH);
    }

    /**
     * Takes an arbitrary precision integer and returns its shortest big-endian
     * two's complement representation.
     *
     * Arbitrary precision integers MUST be encoded as big-endian signed two's
     * complement binary strings. Henceforth, "btwoc" is a function that takes
     * an arbitrary precision integer and returns its shortest big-endian two's
     * complement representation. All integers that are used with
     * Diffie-Hellman Key Exchange are positive. This means that the left-most
     * bit of the two's complement representation MUST be zero. If it is not,
     * implementations MUST add a zero byte at the front of the string.
     *
     * @param string $str binary representation of arbitrary precision integer
     *
     * @return string big-endian signed representation
     */
    public static function btwoc($str)
    {
        if (ord((string) $str[0]) > 127) {
            return "\0".$str;
        }

        return $str;
    }

    /**
     * Returns lenght of binary string in bytes.
     *
     * @param string $str
     *
     * @return int the string lenght
     */
    public static function strlen($str)
    {
        if (extension_loaded('mbstring')
            && (((int) ini_get('mbstring.func_overload')) & 2)) {
            return mb_strlen($str, 'latin1');
        } else {
            return strlen((string) $str);
        }
    }
}
