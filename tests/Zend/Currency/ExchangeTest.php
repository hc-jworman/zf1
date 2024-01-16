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
 * @version    $Id:$
 */

/**
 * Zend_Date.
 */
// require_once 'Zend/Currency/CurrencyInterface.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Currency
 */
#[AllowDynamicProperties]
class ExchangeTest implements Zend_Currency_CurrencyInterface
{
    /**
     * Test method for exchange rate.
     *
     * @param string $from
     * @param string $to
     *
     * @return float
     */
    public function getRate($from, $to)
    {
        if ('RUB' == $from) {
            return 2;
        } elseif ('USD' == $from) {
            return 0.5;
        } else {
            return 1;
        }
    }
}
