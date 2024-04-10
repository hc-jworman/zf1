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
 */

/**
 * Zend_Service_ReCaptcha_Response.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @version    $Id$
 */
class Zend_Service_ReCaptcha_Response
{
    /**
     * Status.
     *
     * true if the response is valid or false otherwise
     *
     * @var bool
     */
    protected $_status;

    /**
     * Error code.
     *
     * The error code if the status is false. The different error codes can be found in the
     * recaptcha API docs.
     *
     * @var string
     */
    protected $_errorCode;

    /**
     * Class constructor used to construct a response.
     *
     * @param string             $status
     * @param string             $errorCode
     * @param Zend_Http_Response $httpResponse If this is set the content will override $status and $errorCode
     */
    public function __construct($status = null, $errorCode = null, ?Zend_Http_Response $httpResponse = null)
    {
        if (null !== $status) {
            $this->setStatus($status);
        }

        if (null !== $errorCode) {
            $this->setErrorCode($errorCode);
        }

        if (null !== $httpResponse) {
            $this->setFromHttpResponse($httpResponse);
        }
    }

    /**
     * Set the status.
     *
     * @param string $status
     *
     * @return Zend_Service_ReCaptcha_Response
     */
    public function setStatus($status)
    {
        if ('true' === $status) {
            $this->_status = true;
        } else {
            $this->_status = false;
        }

        return $this;
    }

    /**
     * Get the status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Alias for getStatus().
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getStatus();
    }

    /**
     * Set the error code.
     *
     * @param string $errorCode
     *
     * @return Zend_Service_ReCaptcha_Response
     */
    public function setErrorCode($errorCode)
    {
        $this->_errorCode = $errorCode;

        return $this;
    }

    /**
     * Get the error code.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * Populate this instance based on a Zend_Http_Response object.
     *
     * @return Zend_Service_ReCaptcha_Response
     */
    public function setFromHttpResponse(Zend_Http_Response $response)
    {
        $body = $response->getBody();

        // Default status and error code
        $status = 'false';
        $errorCode = '';

        $parts = explode("\n", $body);

        if ('true' === $parts[0]) {
            $status = 'true';
        }

        if (!empty($parts[1])) {
            $errorCode = $parts[1];
        }

        $this->setStatus($status);
        $this->setErrorCode($errorCode);

        return $this;
    }
}
