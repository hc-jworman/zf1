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
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Client_Response
{
    /**
     * @var callable|null
     */
    protected $_callback;

    /**
     * @var array
     */
    protected $_content = [];

    /**
     * @var Zend_Tool_Framework_Exception
     */
    protected $_exception;

    /**
     * @var array
     */
    protected $_decorators = [];

    /**
     * @var array
     */
    protected $_defaultDecoratorOptions = [];

    /**
     * setContentCallback().
     *
     * @param callable $callback
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setContentCallback($callback)
    {
        if (!is_callable($callback)) {
            // require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('The callback provided is not callable');
        }
        $this->_callback = $callback;

        return $this;
    }

    /**
     * setContent().
     *
     * @param string $content
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setContent($content, array $decoratorOptions = [])
    {
        $content = $this->_applyDecorators($content, $decoratorOptions);

        $this->_content = [];
        $this->appendContent($content);

        return $this;
    }

    /**
     * appendCallback.
     *
     * @param string $content
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function appendContent($content, array $decoratorOptions = [])
    {
        $content = $this->_applyDecorators($content, $decoratorOptions);

        if (null !== $this->_callback) {
            call_user_func($this->_callback, $content);
        }

        $this->_content[] = $content;

        return $this;
    }

    /**
     * setDefaultDecoratorOptions().
     *
     * @param bool $mergeIntoExisting
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setDefaultDecoratorOptions(array $decoratorOptions, $mergeIntoExisting = false)
    {
        if (false == $mergeIntoExisting) {
            $this->_defaultDecoratorOptions = [];
        }

        $this->_defaultDecoratorOptions = array_merge($this->_defaultDecoratorOptions, $decoratorOptions);

        return $this;
    }

    /**
     * getContent().
     *
     * @return string
     */
    public function getContent()
    {
        return implode('', $this->_content);
    }

    /**
     * isException().
     *
     * @return bool
     */
    public function isException()
    {
        return isset($this->_exception);
    }

    /**
     * setException().
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setException(Throwable $exception)
    {
        $this->_exception = $exception;

        return $this;
    }

    /**
     * getException().
     *
     * @return Throwable
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Add Content Decorator.
     *
     * @return Zend_Tool_Framework_Client_Response
     */
    public function addContentDecorator(Zend_Tool_Framework_Client_Response_ContentDecorator_Interface $contentDecorator)
    {
        $decoratorName = strtolower((string) $contentDecorator->getName());
        $this->_decorators[$decoratorName] = $contentDecorator;

        return $this;
    }

    /**
     * getContentDecorators().
     *
     * @return array
     */
    public function getContentDecorators()
    {
        return $this->_decorators;
    }

    /**
     * __toString() to cast to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) implode('', $this->_content);
    }

    /**
     * _applyDecorators() apply a group of decorators.
     *
     * @param string $content
     *
     * @return string
     */
    protected function _applyDecorators($content, array $decoratorOptions)
    {
        $options = array_merge($this->_defaultDecoratorOptions, $decoratorOptions);

        $options = array_change_key_case($options, CASE_LOWER);

        if ($options) {
            foreach ($this->_decorators as $decoratorName => $decorator) {
                if (array_key_exists($decoratorName, $options)) {
                    $content = $decorator->decorate($content, $options[$decoratorName]);
                }
            }
        }

        return $content;
    }
}
