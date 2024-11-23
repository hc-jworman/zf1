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
 * @package    Zend_Mobile
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 24593 2012-01-05 20:35:02Z matthew $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Mobile_AllTests::main');
}

require_once 'Zend/Mobile/Push/AllTests.php';


/**
 * @category   Zend
 * @package    Zend_Mobile
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mobile
 */
#[AllowDynamicProperties]
class Zend_Mobile_AllTests
{
    public static function main()
    {
        \PHPUnit\TextUI\TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = \PHPUnit\Framework\TestSuite::empty('Zend Framework - Zend_Mobile');

        $suite->addTest(Zend_Mobile_Push_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Mobile_AllTests::main') {
    Zend_Mobile_AllTests::main();
}
