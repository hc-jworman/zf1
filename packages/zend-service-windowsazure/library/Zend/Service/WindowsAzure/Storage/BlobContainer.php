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
 *
 * @property string $Name         Name of the container
 * @property string $Etag         Etag of the container
 * @property string $LastModified Last modified date of the container
 * @property array  $Metadata     Key/value pairs of meta data
 */
class Zend_Service_WindowsAzure_Storage_BlobContainer
{
    /**
     * Data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @param string $name         Name
     * @param string $etag         Etag
     * @param string $lastModified Last modified date
     * @param array  $metadata     Key/value pairs of meta data
     */
    public function __construct($name, $etag, $lastModified, $metadata = [])
    {
        $this->_data = [
            'name' => $name,
            'etag' => $etag,
            'lastmodified' => $lastModified,
            'metadata' => $metadata,
        ];
    }

    /**
     * Magic overload for setting properties.
     *
     * @param string $name  Name of the property
     * @param string $value Value to set
     */
    public function __set($name, $value)
    {
        if (array_key_exists(strtolower((string) $name), $this->_data)) {
            $this->_data[strtolower((string) $name)] = $value;

            return;
        }

        throw new Exception('Unknown property: '.$name);
    }

    /**
     * Magic overload for getting properties.
     *
     * @param string $name Name of the property
     */
    public function __get($name)
    {
        if (array_key_exists(strtolower((string) $name), $this->_data)) {
            return $this->_data[strtolower((string) $name)];
        }

        throw new Exception('Unknown property: '.$name);
    }
}
