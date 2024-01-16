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
 * @see Zend_Tool_Framework_Metadata_Basic
 */
// require_once 'Zend/Tool/Framework/Metadata/Basic.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Metadata_Tool extends Zend_Tool_Framework_Metadata_Basic
{
    /**
     * @var string
     */
    protected $_type = 'Tool';

    /**#@+
     * @var string
     */
    protected $_clientName;
    protected $_actionName;
    protected $_providerName;
    protected $_specialtyName;
    /**#@-*/

    /**#@+
     * @var string
     */
    protected $_clientReference;
    protected $_actionReference;
    protected $_providerReference;
    /**#@-*/

    public function setClientName($clientName)
    {
        $this->_clientName = $clientName;

        return $this;
    }

    public function getClientName()
    {
        return $this->_clientName;
    }

    /**
     * setActionName().
     *
     * @param string $actionName
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;

        return $this;
    }

    /**
     * getActionName().
     *
     * @return string|null
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

    /**
     * setProviderName().
     *
     * @param string $providerName
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setProviderName($providerName)
    {
        $this->_providerName = $providerName;

        return $this;
    }

    /**
     * getProviderName().
     *
     * @return string|null
     */
    public function getProviderName()
    {
        return $this->_providerName;
    }

    /**
     * setSpecialtyName().
     *
     * @param string $specialtyName
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setSpecialtyName($specialtyName)
    {
        $this->_specialtyName = $specialtyName;

        return $this;
    }

    /**
     * getSpecialtyName().
     *
     * @return string|null
     */
    public function getSpecialtyName()
    {
        return $this->_specialtyName;
    }

    /**
     * setClientReference().
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setClientReference(Zend_Tool_Framework_Client_Abstract $client)
    {
        $this->_clientReference = $client;

        return $this;
    }

    /**
     * getClientReference().
     *
     * @return string|null
     */
    public function getClientReference()
    {
        return $this->_clientReference;
    }

    /**
     * setActionReference().
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setActionReference(Zend_Tool_Framework_Action_Interface $action)
    {
        $this->_actionReference = $action;

        return $this;
    }

    /**
     * getActionReference().
     *
     * @return Zend_Tool_Framework_Action_Interface|null
     */
    public function getActionReference()
    {
        return $this->_actionReference;
    }

    /**
     * setProviderReference().
     *
     * @return Zend_Tool_Framework_Metadata_Tool
     */
    public function setProviderReference(Zend_Tool_Framework_Provider_Interface $provider)
    {
        $this->_providerReference = $provider;

        return $this;
    }

    /**
     * getProviderReference().
     *
     * @return Zend_Tool_Framework_Provider_Interface|null
     */
    public function getProviderReference()
    {
        return $this->_providerReference;
    }

    /**
     * __toString() cast to string.
     *
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();
        $string .= ' (ProviderName: '.$this->_providerName
             .', ActionName: '.$this->_actionName
             .', SpecialtyName: '.$this->_specialtyName
             .')';

        return $string;
    }
}
