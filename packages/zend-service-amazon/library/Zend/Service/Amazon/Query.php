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
 * @see Zend_Service_Amazon
 */
// require_once 'Zend/Service/Amazon.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Query extends Zend_Service_Amazon
{
    /**
     * Search parameters.
     *
     * @var array
     */
    protected $_search = [];

    /**
     * Search index.
     *
     * @var string
     */
    protected $_searchIndex;

    /**
     * Prepares query parameters.
     *
     * @param string $method
     * @param array  $args
     *
     * @return Zend_Service_Amazon_Query Provides a fluent interface
     *
     * @throws Zend_Service_Exception
     */
    public function __call($method, $args)
    {
        if ('asin' === strtolower((string) $method)) {
            $this->_searchIndex = 'asin';
            $this->_search['ItemId'] = $args[0];

            return $this;
        }

        if ('category' === strtolower((string) $method)) {
            $this->_searchIndex = $args[0];
            $this->_search['SearchIndex'] = $args[0];
        } elseif (isset($this->_search['SearchIndex']) || null !== $this->_searchIndex || 'asin' === $this->_searchIndex) {
            $this->_search[$method] = $args[0];
        } else {
            /*
             * @see Zend_Service_Exception
             */
            // require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('You must set a category before setting the search parameters');
        }

        return $this;
    }

    /**
     * Search using the prepared query.
     *
     * @return Zend_Service_Amazon_Item|Zend_Service_Amazon_ResultSet
     */
    public function search()
    {
        if ('asin' === $this->_searchIndex) {
            return $this->itemLookup($this->_search['ItemId'], $this->_search);
        }

        return $this->itemSearch($this->_search);
    }
}
