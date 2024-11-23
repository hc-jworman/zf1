<?php

#[AllowDynamicProperties]
class Zend_Acl_MockAssertion implements Zend_Acl_Assert_Interface
{
    protected $_returnValue;

    public function __construct($returnValue)
    {
        $this->_returnValue = (bool) $returnValue;
    }

    public function assert(
        Zend_Acl $acl,
        Zend_Acl_Role_Interface|null $role = null,
        Zend_Acl_Resource_Interface|null $resource = null,
        $privilege = null,
    ) {
       return $this->_returnValue;
    }
}
