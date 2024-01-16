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
 * @version $Id$
 */

// require_once 'Zend/Server/Reflection/Class.php';

// require_once 'Zend/Server/Reflection.php';

/**
 * Test case for Zend_Server_Reflection_Class.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Server
 */
#[AllowDynamicProperties]
class Zend_Server_Reflection_ClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * __construct() test.
     *
     * Call as method call
     *
     * Expects:
     * - reflection:
     * - namespace: Optional;
     * - argv: Optional; has default;
     *
     * Returns: void
     */
    public function testConstruct()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));
        $this->assertTrue($r instanceof Zend_Server_Reflection_Class);
        $this->assertEquals('', $r->getNamespace());

        $methods = $r->getMethods();
        $this->assertInternalType('array', $methods);
        foreach ($methods as $m) {
            $this->assertTrue($m instanceof Zend_Server_Reflection_Method);
        }

        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'), 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * __call() test.
     *
     * Call as method call
     *
     * Expects:
     * - method:
     * - args:
     *
     * Returns: mixed
     */
    public function testCall()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));
        $this->assertInternalType('string', $r->getName());
        $this->assertEquals('Zend_Server_Reflection', $r->getName());
    }

    /**
     * test __get/set.
     */
    public function testGetSet()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));
        $r->system = true;
        $this->assertTrue($r->system);
    }

    /**
     * getMethods() test.
     *
     * Call as method call
     *
     * Returns: array
     */
    public function testGetMethods()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));

        $methods = $r->getMethods();
        $this->assertInternalType('array', $methods);
        foreach ($methods as $m) {
            $this->assertTrue($m instanceof Zend_Server_Reflection_Method);
        }
    }

    /**
     * namespace test.
     */
    public function testGetNamespace()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));
        $this->assertEquals('', $r->getNamespace());
        $r->setNamespace('namespace');
        $this->assertEquals('namespace', $r->getNamespace());
    }

    /**
     * __wakeup() test.
     *
     * Call as method call
     *
     * Returns: void
     */
    public function testWakeup()
    {
        $r = new Zend_Server_Reflection_Class(new ReflectionClass('Zend_Server_Reflection'));
        if (PHP_VERSION_ID >= 70400) {
            $this->setExpectedException('Exception', "Serialization of 'ReflectionMethod' is not allowed");
        }
        $s = serialize($r);
        $u = unserialize($s);

        $this->assertTrue($u instanceof Zend_Server_Reflection_Class);
        $this->assertEquals('', $u->getNamespace());
        $this->assertEquals($r->getName(), $u->getName());
        $rMethods = $r->getMethods();
        $uMethods = $r->getMethods();

        $this->assertCount(count($rMethods), $uMethods);
    }
}
