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

/*
 * This test code tests the customization functions provided in the example
 * documentation code.
 */

/** Custom_Queue */
require_once 'Custom/Queue.php';

/** Custom_Message */
require_once 'Custom/Message.php';

/** Custom_Messages */
require_once 'Custom/Messages.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Queue
 */
#[AllowDynamicProperties]
class Custom_Object
{
    public $a;

    public function __construct()
    {
        $a = rand(1, 200);
    }

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function __sleep()
    {
        return ['a']; // serialize only this variable
    }
}

#[AllowDynamicProperties]
class Zend_Queue_CustomTest extends PHPUnit_Framework_TestCase
{
    public function testBehavior()
    {
        $object_count = 10;
        $objects = [];

        $queue = new Custom_Queue('Array', ['name' => 'ObjectA']);
        $this->assertTrue($queue instanceof Custom_Queue);

        // ------------------------------------------------ send

        // add items $objects[0-4]
        $objects = [];
        for ($i = 0; $i < $object_count - 5; ++$i) {
            $object = new Custom_Object();
            $queue->send(new Custom_Message($object));
            $objects[] = $object;
        }

        // add items $objects[5-9]
        $messages = new Custom_Messages();
        for ($i = 0; $i < 5; ++$i) {
            $object = new Custom_Object();
            $messages->append(new Custom_Message($object));
            $objects[] = $object;
        }
        $queue->send($messages);

        $this->assertEquals($object_count, count($queue));
        unset($messages);

        // ------------------------------------------------ receive

        // get the first 5 doing 0-4
        $receive = $queue->receive(5);
        $this->assertTrue($receive instanceof Custom_Messages);
        $this->assertEquals(5, count($receive));

        // test them
        for ($index = 0; $index < 5; ++$index) {
            $this->assertEquals($objects[$index]->getA(), $receive[$index]->getBody()->getA());
            try {
                unset($receive[$index]);
                $this->assertTrue(true, '$receive[$index] successfully deleted');
            } catch (Zend_Queue_Exception $e) {
                $this->fail('$receive[$index] should have been deleted'.$e->getMessage());
            }
        }
        // there should only be 5 objects left
        $this->assertEquals($object_count - $index, count($queue));

        // get 1 doing $objects[5]
        $receive = $queue->receive();
        ++$index;
        $this->assertTrue($receive instanceof Custom_Messages);
        $this->assertEquals(1, count($receive));

        // testing Custom_Messages::__deconstruct()
        unset($receive);
        $this->assertEquals($object_count - $index, count($queue));

        // get all the rest doing 6-20
        $receive = $queue->receive($object_count - $index);
        $this->assertTrue($receive instanceof Custom_Messages);
        $this->assertEquals($object_count - $index, count($receive));

        // test them
        $r_index = -1;
        for (; $index < $object_count; ++$index) {
            ++$r_index;
            $this->assertEquals($objects[$index]->getA(), $receive[$r_index]->getBody()->getA());

            try {
                unset($receive[$r_index]);
                $this->assertTrue(true, '$receive[$index] successfully deleted');
            } catch (Zend_Queue_Exception $e) {
                $this->fail('$receive[$index] should have been deleted'.$e->getMessage());
            }
        }

        // auto-delete should have been called on $receive
        $this->assertEquals(0, count($queue));
    }
}
