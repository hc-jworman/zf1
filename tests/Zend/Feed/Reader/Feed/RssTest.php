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

// require_once 'Zend/Feed/Reader.php';
// require_once 'Zend/Registry.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Feed
 * @group      Zend_Feed_Reader
 */
#[AllowDynamicProperties]
class Zend_Feed_Reader_Feed_RssTest extends PHPUnit_Framework_TestCase
{
    protected $_feedSamplePath;

    protected $_expectedCats = [];

    protected $_expectedCatsRdf = [];

    protected $_expectedCatsAtom = [];

    public function setup()
    {
        Zend_Feed_Reader::reset();
        if (Zend_Registry::isRegistered('Zend_Locale')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Locale']);
        }
        $this->_feedSamplePath = __DIR__.'/_files/Rss';
        $this->_options = Zend_Date::setOptions();
        foreach ($this->_options as $k => $v) {
            if (is_null($v)) {
                unset($this->_options[$k]);
            }
        }
        Zend_Date::setOptions(['format_type' => 'iso']);
        $this->_expectedCats = [
            [
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic1',
            ],
            [
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label' => 'topic1',
            ],
            [
                'term' => 'topic2',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic2',
            ],
        ];
        $this->_expectedCatsRdf = [
            [
                'term' => 'topic1',
                'scheme' => null,
                'label' => 'topic1',
            ],
            [
                'term' => 'topic2',
                'scheme' => null,
                'label' => 'topic2',
            ],
        ];
        $this->_expectedCatsAtom = [
            [
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label' => 'topic1',
            ],
            [
                'term' => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label' => 'topic1',
            ],
            [
                'term' => 'cat_dog',
                'scheme' => 'http://example.com/schema1',
                'label' => 'Cat & Dog',
            ],
        ];
    }

    public function teardown()
    {
        Zend_Date::setOptions($this->_options);
    }

    /**
     * Get Title (Unencoded Text).
     */
    public function testGetsTitleFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.0

    public function testGetsTitleFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.1

    public function testGetsTitleFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Atom 1.0

    public function testGetsTitleFromRss20Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/atom10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Missing Title

    public function testGetsTitleFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/title/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text).
     */
    public function testGetsAuthorArrayFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // DC 1.0

    public function testGetsAuthorArrayFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // DC 1.1

    public function testGetsAuthorArrayFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // Atom 1.0

    public function testGetsAuthorArrayFromRss20Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/atom10/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'], ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // Missing Authors

    public function testGetsAuthorArrayFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text).
     */
    public function testGetsSingleAuthorFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    // DC 1.0

    public function testGetsSingleAuthorFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    // DC 1.1

    public function testGetsSingleAuthorFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    // Missing Author

    public function testGetsSingleAuthorFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    /**
     * Get Copyright (Unencoded Text).
     */
    public function testGetsCopyrightFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    // DC 1.0

    public function testGetsCopyrightFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // DC 1.1

    public function testGetsCopyrightFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // Missing Copyright

    public function testGetsCopyrightFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/copyright/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text).
     */
    public function testGetsDescriptionFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.0

    public function testGetsDescriptionFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.1

    public function testGetsDescriptionFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // Missing Description

    public function testGetsDescriptionFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/description/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    /**
     * Get Language (Unencoded Text).
     */
    public function testGetsLanguageFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    // DC 1.0

    public function testGetsLanguageFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc10/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // DC 1.1

    public function testGetsLanguageFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/dc11/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // Other

    public function testGetsLanguageFromRss10XmlLang()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/rdf/rss10.xml')
        );
        $this->assertEquals('en', $feed->getLanguage());
    }

    // Missing Language

    public function testGetsLanguageFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/language/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text).
     */
    public function testGetsLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    // Missing Link

    public function testGetsLinkFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    /**
     * Implements Countable.
     */
    public function testCountableInterface()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/link/plain/none/rss090.xml')
        );
        $this->assertEquals(0, count($feed));
    }

    /**
     * Get Feed Link (Unencoded Text).
     */
    public function testGetsFeedLinkFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsOriginalSourceUriIfFeedLinkNotAvailableFromFeed()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss20_NoFeedLink.xml')
        );
        $feed->setOriginalSourceUri('http://www.example.com/feed/rss');
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    // Missing Feed Link

    public function testGetsFeedLinkFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/feedlink/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    /**
     * Get Generator (Unencoded Text).
     */
    public function testGetsGeneratorFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss20.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss094.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss093.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss092.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss091.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss10.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/rss090.xml')
        );
        $this->assertEquals('Zend_Feed_Writer', $feed->getGenerator());
    }

    // Missing Generator

    public function testGetsGeneratorFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/generator/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    /**
     * Get Last Build Date (Unencoded Text).
     */
    public function testGetsLastBuildDateFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/lastbuilddate/plain/rss20.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getLastBuildDate()));
    }

    public function testGetsLastBuildDateFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/lastbuilddate/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLastBuildDate());
    }

    /**
     * Get Date Modified (Unencoded Text).
     */
    public function testGetsDateModifiedFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    /**
     * @group ZF-8702
     */
    public function testParsesCorrectDateIfMissingOffsetWhenSystemUsesUSLocale()
    {
        $locale = new Zend_Locale('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/rss20_en_US.xml')
        );
        $fdate = $feed->getDateModified();
        $edate = new Zend_Date();
        $edate->set('2010-01-04T02:14:00-0600', Zend_Date::ISO_8601);
        Zend_Registry::getInstance()->offsetUnset('Zend_Locale');
        $this->assertTrue($edate->equals($fdate));
    }

    // DC 1.0

    public function testGetsDateModifiedFromRss20Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss20.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss094.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss093.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss092.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss091.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss10.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc10/rss090.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // DC 1.1

    public function testGetsDateModifiedFromRss20Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss20.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss094.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss093.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss092.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss091.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss10.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/dc11/rss090.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // Atom 1.0

    public function testGetsDateModifiedFromRss20Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss20.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss094Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss094.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss093Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss093.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss092Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss092.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss091Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss091.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss10Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss10.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    public function testGetsDateModifiedFromRss090Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/atom10/rss090.xml')
        );
        $edate = new Zend_Date();
        $edate->set('2009-03-07T08:03:50Z', Zend_Date::ISO_8601);
        $this->assertTrue($edate->equals($feed->getDateModified()));
    }

    // Missing DateModified

    public function testGetsDateModifiedFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/datemodified/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDateModified());
    }

    /**
     * Get Hubs (Unencoded Text).
     */
    public function testGetsHubsFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss20.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss094.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss093.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss092.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss091.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss10.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/atom10/rss090.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    // Missing Hubs

    public function testGetsHubsFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/hubs/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    /**
     * Get category data.
     */

    // RSS 2.0

    public function testGetsCategoriesFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/rss20.xml')
        );
        $this->assertEquals($this->_expectedCats, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // DC 1.0

    public function testGetsCategoriesFromRss090Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Dc10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc10/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // DC 1.1

    public function testGetsCategoriesFromRss090Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Dc11()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/dc11/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // Atom 1.0

    public function testGetsCategoriesFromRss090Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss090.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss091.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss092.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss093.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss094.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Atom10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/atom10/rss10.xml')
        );
        $this->assertEquals($this->_expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    // No Categories In Entry

    public function testGetsCategoriesFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss20.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss090.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss091.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss092.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss093.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss094.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/category/plain/none/rss10.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    /**
     * Get Image data (Unencoded Text).
     */
    public function testGetsImageFromRss20()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss20.xml')
        );
        $this->assertEquals([
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss094()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss094.xml')
        );
        $this->assertEquals([
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss093()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss093.xml')
        );
        $this->assertEquals([
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss092()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss092.xml')
        );
        $this->assertEquals([
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss091()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss091.xml')
        );
        $this->assertEquals([
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    /*public function testGetsImageFromRss10()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss10.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss090()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss090.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }*/

    /**
     * Get Image data (Unencoded Text) Missing.
     */
    public function testGetsImageFromRss20None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss094None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss093None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss092None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss091None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss10None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss090None()
    {
        $feed = Zend_Feed_Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }
}
