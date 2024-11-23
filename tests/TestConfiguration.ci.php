<?php
/**
 * Zend Framework
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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This file defines configuration for running the unit tests for the Zend
 * Framework.  Some tests have dependencies to PHP extensions or databases
 * which may not necessary installed on the target system.  For these cases,
 * the ability to disable or configure testing is provided below.  Tests for
 * components which should run universally are always run by the master
 * suite and cannot be disabled.
 *
 * Do not edit this file. Instead, copy this file to TestConfiguration.php,
 * and edit the new file. Never commit plaintext passwords to the source
 * code repository.
 */

///**
// * Zend_Cache
// *
// * TESTS_ZEND_CACHE_MEMCACHED_ENABLED  => memcache extension has to be enabled and
// *                                        a memcached server has to be available
// * TESTS_ZEND_CACHE_LIBMEMCACHED_ENABLED => memcached extension has to be enabled and
// *                                          a memcached server has to be available
// */
//defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') || define('TESTS_ZEND_CACHE_MEMCACHED_ENABLED', true);
//defined('TESTS_ZEND_CACHE_LIBMEMCACHED_ENABLED') || define('TESTS_ZEND_CACHE_LIBMEMCACHED_ENABLED', true);
//
///**
// * Zend_Db_Adapter_Pdo_Mysql and Zend_Db_Adapter_Mysqli
// *
// * There are separate properties to enable tests for the PDO_MYSQL adapter and
// * the native Mysqli adapters, but the other properties are shared between the
// * two MySQL-related Zend_Db adapters.
// */
//// Skip testing ext/mysqli for PHP 5.4/5.5, as the error is not fixable
//// https://github.com/zf1s/zf1/pull/49/files#r565875073
//defined('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED') || define('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED', true);
//defined('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED') || define('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED', !(PHP_VERSION_ID >= 50400 && PHP_VERSION_ID < 50600));
//defined('TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME') || define('TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME', getenv('MYSQL_HOST'));
//defined('TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME') || define('TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME', getenv('MYSQL_USER'));
//defined('TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD') || define('TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD', getenv('MYSQL_PASSWORD'));
//defined('TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE') || define('TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE', getenv('MYSQL_DATABASE'));
//
///**
// * Zend_Db_Adapter_Pdo_Pgsql
// */
//defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED') || define('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED',  true);
//defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME') || define('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME', getenv('POSTGRES_HOST'));
//defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME') || define('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME', getenv('POSTGRES_USER'));
//defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD') || define('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD', getenv('POSTGRES_PASSWORD'));
//defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE') || define('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE', getenv('POSTGRES_DB'));

require_once __DIR__ . '/TestConfiguration.dist.php';
