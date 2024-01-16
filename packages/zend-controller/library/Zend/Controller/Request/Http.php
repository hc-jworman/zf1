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

/** @see Zend_Controller_Request_Abstract */
// require_once 'Zend/Controller/Request/Abstract.php';

/** @see Zend_Uri */
// require_once 'Zend/Uri.php';

/**
 * Zend_Controller_Request_Http.
 *
 * HTTP request object for use with Zend_Controller family.
 *
 * @uses Zend_Controller_Request_Abstract
 */
class Zend_Controller_Request_Http extends Zend_Controller_Request_Abstract
{
    /**
     * Scheme for http.
     */
    public const SCHEME_HTTP = 'http';

    /**
     * Scheme for https.
     */
    public const SCHEME_HTTPS = 'https';

    /**
     * Allowed parameter sources.
     *
     * @var array
     */
    protected $_paramSources = ['_GET', '_POST'];

    /**
     * REQUEST_URI.
     *
     * @var string;
     */
    protected $_requestUri;

    /**
     * Base URL of request.
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Base path of request.
     *
     * @var string
     */
    protected $_basePath;

    /**
     * PATH_INFO.
     *
     * @var string
     */
    protected $_pathInfo = '';

    /**
     * Instance parameters.
     *
     * @var array
     */
    protected $_params = [];

    /**
     * Raw request body.
     *
     * @var string|false
     */
    protected $_rawBody;

    /**
     * Alias keys for request parameters.
     *
     * @var array
     */
    protected $_aliases = [];

    /**
     * Constructor.
     *
     * If a $uri is passed, the object will attempt to populate itself using
     * that information.
     *
     * @param string|Zend_Uri $uri
     *
     * @return void
     *
     * @throws Zend_Controller_Request_Exception when invalid URI passed
     */
    public function __construct($uri = null)
    {
        if (null !== $uri) {
            if (!$uri instanceof Zend_Uri) {
                $uri = Zend_Uri::factory($uri);
            }
            if ($uri->valid()) {
                $path = $uri->getPath();
                $query = $uri->getQuery();
                if (!empty($query)) {
                    $path .= '?'.$query;
                }

                $this->setRequestUri($path);
            } else {
                // require_once 'Zend/Controller/Request/Exception.php';
                throw new Zend_Controller_Request_Exception('Invalid URI provided to constructor');
            }
        } else {
            $this->setRequestUri();
        }
    }

    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV.
     *
     * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     *
     * @param string $key
     */
    public function __get($key)
    {
        switch (true) {
            case isset($this->_params[$key]):
                return $this->_params[$key];
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case 'REQUEST_URI' == $key:
                return $this->getRequestUri();
            case 'PATH_INFO' == $key:
                return $this->getPathInfo();
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    }

    /**
     * Alias to __get.
     *
     * @param string $key
     */
    public function get($key)
    {
        return $this->__get($key);
    }

    /**
     * Set values.
     *
     * In order to follow {@link __get()}, which operates on a number of
     * superglobals, setting values through overloading is not allowed and will
     * raise an exception. Use setParam() instead.
     *
     * @param string $key
     *
     * @return void
     *
     * @throws Zend_Controller_Request_Exception
     */
    public function __set($key, $value)
    {
        // require_once 'Zend/Controller/Request/Exception.php';
        throw new Zend_Controller_Request_Exception('Setting values in superglobals not allowed; please use setParam()');
    }

    /**
     * Alias to __set().
     *
     * @param string $key
     *
     * @return void
     */
    public function set($key, $value)
    {
        return $this->__set($key, $value);
    }

    /**
     * Check to see if a property is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        switch (true) {
            case isset($this->_params[$key]):
                return true;
            case isset($_GET[$key]):
                return true;
            case isset($_POST[$key]):
                return true;
            case isset($_COOKIE[$key]):
                return true;
            case isset($_SERVER[$key]):
                return true;
            case isset($_ENV[$key]):
                return true;
            default:
                return false;
        }
    }

    /**
     * Alias to __isset().
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->__isset($key);
    }

    /**
     * Set GET values.
     *
     * @param string|array $spec
     * @param mixed|null   $value
     *
     * @return Zend_Controller_Request_Http
     */
    public function setQuery($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            // require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid value passed to setQuery(); must be either array of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setQuery($key, $value);
            }

            return $this;
        }
        $_GET[(string) $spec] = $value;

        return $this;
    }

    /**
     * Retrieve a member of the $_GET superglobal.
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @todo How to retrieve from nested arrays
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    /**
     * Set POST values.
     *
     * @param string|array $spec
     * @param mixed|null   $value
     *
     * @return Zend_Controller_Request_Http
     */
    public function setPost($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            // require_once 'Zend/Controller/Exception.php';
            throw new Zend_Controller_Exception('Invalid value passed to setPost(); must be either array of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setPost($key, $value);
            }

            return $this;
        }
        $_POST[(string) $spec] = $value;

        return $this;
    }

    /**
     * Retrieve a member of the $_POST superglobal.
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @todo How to retrieve from nested arrays
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * Retrieve a member of the $_COOKIE superglobal.
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @todo How to retrieve from nested arrays
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    /**
     * Retrieve a member of the $_SERVER superglobal.
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Retrieve a member of the $_ENV superglobal.
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null)
    {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }

    /**
     * Set the REQUEST_URI on which the instance operates.
     *
     * If no request URI is passed, uses the value in $_SERVER['REQUEST_URI'],
     * $_SERVER['HTTP_X_REWRITE_URL'], or $_SERVER['ORIG_PATH_INFO'] + $_SERVER['QUERY_STRING'].
     *
     * @param string $requestUri
     *
     * @return Zend_Controller_Request_Http
     */
    public function setRequestUri($requestUri = null)
    {
        if (null === $requestUri) {
            if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
                // IIS with Microsoft Rewrite Module
                $requestUri = $_SERVER['HTTP_X_ORIGINAL_URL'];
            } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                // IIS with ISAPI_Rewrite
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (
                // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
                isset($_SERVER['IIS_WasUrlRewritten'])
                && '1' == $_SERVER['IIS_WasUrlRewritten']
                && isset($_SERVER['UNENCODED_URL'])
                && '' != $_SERVER['UNENCODED_URL']
            ) {
                $requestUri = $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                // Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
                $schemeAndHttpHost = $this->getScheme().'://'.$this->getHttpHost();
                if (0 === strpos((string) $requestUri, $schemeAndHttpHost)) {
                    $requestUri = substr((string) $requestUri, strlen((string) $schemeAndHttpHost));
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?'.$_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            // Set GET items, if available
            if (false !== ($pos = strpos((string) $requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr((string) $requestUri, $pos + 1);
                parse_str($query, $vars);
                $this->setQuery($vars);
            }
        }

        $this->_requestUri = $requestUri;

        return $this;
    }

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS.
     *
     * @return string
     */
    public function getRequestUri()
    {
        if (empty($this->_requestUri)) {
            $this->setRequestUri();
        }

        return $this->_requestUri;
    }

    /**
     * Set the base URL of the request; i.e., the segment leading to the script name.
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Do not use the full URI when providing the base. The following are
     * examples of what not to use:
     * - http://example.com/admin (should be just /admin)
     * - http://example.com/subdir/index.php (should be just /subdir/index.php)
     *
     * If no $baseUrl is provided, attempts to determine the base URL from the
     * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
     * ORIG_SCRIPT_NAME in its determination.
     *
     * @return Zend_Controller_Request_Http
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ((null !== $baseUrl) && !is_string($baseUrl)) {
            return $this;
        }

        if (null === $baseUrl) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
            } else {
                // Backtrack up the script_filename to find the portion matching
                // php_self
                $path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
                $file = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
                $segs = explode('/', \trim((string) $file, '/'));
                $segs = array_reverse($segs);
                $index = 0;
                $last = count($segs);
                $baseUrl = '';
                do {
                    $seg = $segs[$index];
                    $baseUrl = '/'.$seg.$baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos((string) $path, $baseUrl))) && (0 != $pos));
            }

            // Does the baseUrl have anything in common with the request_uri?
            $requestUri = $this->getRequestUri();

            if (0 === strpos((string) $requestUri, $baseUrl)) {
                // full $baseUrl matches
                $this->_baseUrl = $baseUrl;

                return $this;
            }

            if (0 === strpos((string) $requestUri, dirname($baseUrl))) {
                // directory portion of $baseUrl matches
                $this->_baseUrl = rtrim((string) dirname($baseUrl), '/');

                return $this;
            }

            $truncatedRequestUri = $requestUri;
            if (($pos = strpos((string) $requestUri, '?')) !== false) {
                $truncatedRequestUri = substr((string) $requestUri, 0, $pos);
            }

            $basename = basename($baseUrl);
            if (empty($basename) || !strpos((string) $truncatedRequestUri, $basename)) {
                // no match whatsoever; set it blank
                $this->_baseUrl = '';

                return $this;
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            if ((strlen((string) $requestUri) >= strlen((string) $baseUrl))
                && ((false !== ($pos = strpos((string) $requestUri, $baseUrl))) && (0 !== $pos))) {
                $baseUrl = substr((string) $requestUri, 0, $pos + strlen((string) $baseUrl));
            }
        }

        $this->_baseUrl = rtrim((string) $baseUrl, '/');

        return $this;
    }

    /**
     * Everything in REQUEST_URI before PATH_INFO
     * <form action="<?=$baseUrl?>/news/submit" method="POST"/>.
     *
     * @return string
     */
    public function getBaseUrl($raw = false)
    {
        if (null === $this->_baseUrl) {
            $this->setBaseUrl();
        }

        return (false == $raw) ? urldecode($this->_baseUrl) : $this->_baseUrl;
    }

    /**
     * Set the base path for the URL.
     *
     * @param string|null $basePath
     *
     * @return Zend_Controller_Request_Http
     */
    public function setBasePath($basePath = null)
    {
        if (null === $basePath) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME']))
                      ? basename($_SERVER['SCRIPT_FILENAME'])
                      : '';

            $baseUrl = $this->getBaseUrl();
            if (empty($baseUrl)) {
                $this->_basePath = '';

                return $this;
            }

            if (basename($baseUrl) === $filename) {
                $basePath = dirname($baseUrl);
            } else {
                $basePath = $baseUrl;
            }
        }

        if ('WIN' === substr((string) PHP_OS, 0, 3)) {
            $basePath = str_replace((string) '\\', '/', $basePath);
        }

        $this->_basePath = rtrim((string) $basePath, '/');

        return $this;
    }

    /**
     * Everything in REQUEST_URI before PATH_INFO not including the filename
     * <img src="<?=$basePath?>/images/zend.png"/>.
     *
     * @return string
     */
    public function getBasePath()
    {
        if (null === $this->_basePath) {
            $this->setBasePath();
        }

        return $this->_basePath;
    }

    /**
     * Set the PATH_INFO string.
     *
     * @param string|null $pathInfo
     *
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if (null === $pathInfo) {
            $baseUrl = $this->getBaseUrl(); // this actually calls setBaseUrl() & setRequestUri()
            $baseUrlRaw = $this->getBaseUrl(false);
            $baseUrlEncoded = urlencode((string) $baseUrlRaw);

            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            if ($pos = strpos((string) $requestUri, '?')) {
                $requestUri = substr((string) $requestUri, 0, $pos);
            }

            if (!empty($baseUrl) || !empty($baseUrlRaw)) {
                if (0 === strpos((string) $requestUri, $baseUrl)) {
                    $pathInfo = substr((string) $requestUri, strlen((string) $baseUrl));
                } elseif (0 === strpos((string) $requestUri, $baseUrlRaw)) {
                    $pathInfo = substr((string) $requestUri, strlen((string) $baseUrlRaw));
                } elseif (0 === strpos((string) $requestUri, $baseUrlEncoded)) {
                    $pathInfo = substr((string) $requestUri, strlen((string) $baseUrlEncoded));
                } else {
                    $pathInfo = $requestUri;
                }
            } else {
                $pathInfo = $requestUri;
            }
        }

        $this->_pathInfo = (string) $pathInfo;

        return $this;
    }

    /**
     * Returns everything between the BaseUrl and QueryString.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (empty($this->_pathInfo)) {
            $this->setPathInfo();
        }

        return $this->_pathInfo;
    }

    /**
     * Set allowed parameter sources.
     *
     * Can be empty array, or contain one or more of '_GET' or '_POST'.
     *
     * @return Zend_Controller_Request_Http
     */
    public function setParamSources(array $paramSources = [])
    {
        $this->_paramSources = $paramSources;

        return $this;
    }

    /**
     * Get list of allowed parameter sources.
     *
     * @return array
     */
    public function getParamSources()
    {
        return $this->_paramSources;
    }

    /**
     * Set a userland parameter.
     *
     * Uses $key to set a userland parameter. If $key is an alias, the actual
     * key will be retrieved and used to set the parameter.
     *
     * @return Zend_Controller_Request_Http
     */
    public function setParam($key, $value)
    {
        $key = (null !== ($alias = $this->getAlias($key))) ? $alias : $key;
        parent::setParam($key, $value);

        return $this;
    }

    /**
     * Retrieve a parameter.
     *
     * Retrieves a parameter from the instance. Priority is in the order of
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, null is returned.
     *
     * If the $key is an alias, the actual key aliased will be used.
     *
     * @param mixed $default Default value to use if key not found
     */
    public function getParam($key, $default = null)
    {
        $keyName = (null !== ($alias = $this->getAlias($key))) ? $alias : $key;

        $paramSources = $this->getParamSources();
        if (isset($this->_params[$keyName])) {
            return $this->_params[$keyName];
        } elseif (in_array('_GET', $paramSources) && (isset($_GET[$keyName]))) {
            return $_GET[$keyName];
        } elseif (in_array('_POST', $paramSources) && (isset($_POST[$keyName]))) {
            return $_POST[$keyName];
        }

        return $default;
    }

    /**
     * Retrieve an array of parameters.
     *
     * Retrieves a merged array of parameters, with precedence of userland
     * params (see {@link setParam()}), $_GET, $_POST (i.e., values in the
     * userland params will take precedence over all others).
     *
     * @return array
     */
    public function getParams()
    {
        $return = $this->_params;
        $paramSources = $this->getParamSources();
        if (in_array('_GET', $paramSources)
            && isset($_GET)
            && is_array($_GET)
        ) {
            $return += $_GET;
        }
        if (in_array('_POST', $paramSources)
            && isset($_POST)
            && is_array($_POST)
        ) {
            $return += $_POST;
        }

        return $return;
    }

    /**
     * Set parameters.
     *
     * Set one or more parameters. Parameters are set as userland parameters,
     * using the keys specified in the array.
     *
     * @return Zend_Controller_Request_Http
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

        return $this;
    }

    /**
     * Set a key alias.
     *
     * Set an alias used for key lookups. $name specifies the alias, $target
     * specifies the actual key to use.
     *
     * @param string $name
     * @param string $target
     *
     * @return Zend_Controller_Request_Http
     */
    public function setAlias($name, $target)
    {
        $this->_aliases[$name] = $target;

        return $this;
    }

    /**
     * Retrieve an alias.
     *
     * Retrieve the actual key represented by the alias $name.
     *
     * @param string $name
     *
     * @return string|null Returns null when no alias exists
     */
    public function getAlias($name)
    {
        if (isset($this->_aliases[$name])) {
            return $this->_aliases[$name];
        }

        return null;
    }

    /**
     * Retrieve the list of all aliases.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->_aliases;
    }

    /**
     * Return the method by which the request was made.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Was the request made by POST?
     *
     * @return bool
     */
    public function isPost()
    {
        if ('POST' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by GET?
     *
     * @return bool
     */
    public function isGet()
    {
        if ('GET' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by PUT?
     *
     * @return bool
     */
    public function isPut()
    {
        if ('PUT' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by DELETE?
     *
     * @return bool
     */
    public function isDelete()
    {
        if ('DELETE' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by HEAD?
     *
     * @return bool
     */
    public function isHead()
    {
        if ('HEAD' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return bool
     */
    public function isOptions()
    {
        if ('OPTIONS' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by PATCH?
     *
     * @return bool
     */
    public function isPatch()
    {
        if ('PATCH' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->getHeader('X_REQUESTED_WITH');
    }

    /**
     * Is this a Flash request?
     *
     * @return bool
     */
    public function isFlashRequest()
    {
        $header = strtolower((string) $this->getHeader('USER_AGENT'));

        return (strstr((string) $header, ' flash')) ? true : false;
    }

    /**
     * Is https secure request.
     *
     * @return bool
     */
    public function isSecure()
    {
        return self::SCHEME_HTTPS === $this->getScheme();
    }

    /**
     * Return the raw body of the request, if present.
     *
     * @return string|false Raw body, or false if not present
     */
    public function getRawBody()
    {
        if (null === $this->_rawBody) {
            $body = file_get_contents('php://input');

            if (strlen((string) \trim((string) $body)) > 0) {
                $this->_rawBody = $body;
            } else {
                $this->_rawBody = false;
            }
        }

        return $this->_rawBody;
    }

    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     *
     * @return string|false HTTP header value, or false if not found
     *
     * @throws Zend_Controller_Request_Exception
     */
    public function getHeader($header)
    {
        if (empty($header)) {
            // require_once 'Zend/Controller/Request/Exception.php';
            throw new Zend_Controller_Request_Exception('An HTTP header name is required');
        }

        // Try to get it from the $_SERVER array first
        $temp = strtoupper((string) str_replace((string) '-', '_', $header));
        if (isset($_SERVER['HTTP_'.$temp])) {
            return $_SERVER['HTTP_'.$temp];
        }

        /*
         * Try to get it from the $_SERVER array on POST request or CGI environment
         * @see https://www.ietf.org/rfc/rfc3875 (4.1.2. and 4.1.3.)
         */
        if (isset($_SERVER[$temp])
            && in_array($temp, ['CONTENT_TYPE', 'CONTENT_LENGTH'])
        ) {
            return $_SERVER[$temp];
        }

        // This seems to be the only way to get the Authorization header on
        // Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers[$header])) {
                return $headers[$header];
            }
            $header = strtolower((string) $header);
            foreach ($headers as $key => $value) {
                if (strtolower((string) $key) == $header) {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Get the request URI scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return ('on' == $this->getServer('HTTPS')) ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name = $this->getServer('SERVER_NAME');
        $port = $this->getServer('SERVER_PORT');

        if (null === $name) {
            return '';
        } elseif ((self::SCHEME_HTTP == $scheme && 80 == $port) || (self::SCHEME_HTTPS == $scheme && 443 == $port)) {
            return $name;
        } else {
            return $name.':'.$port;
        }
    }

    /**
     * Get the client's IP addres.
     *
     * @param bool $checkProxy
     *
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        if ($checkProxy && null != $this->getServer('HTTP_CLIENT_IP')) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } elseif ($checkProxy && null != $this->getServer('HTTP_X_FORWARDED_FOR')) {
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }

        return $ip;
    }
}
