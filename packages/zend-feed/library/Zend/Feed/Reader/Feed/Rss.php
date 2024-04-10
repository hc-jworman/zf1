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
 * @see Zend_Feed_Reader_FeedAbstract
 */
// require_once 'Zend/Feed/Reader/FeedAbstract.php';

/**
 * @see Zend_feed_Reader_Extension_Atom_Feed
 */
// require_once 'Zend/Feed/Reader/Extension/Atom/Feed.php';

/**
 * @see Zend_Feed_Reader_Extension_DublinCore_Feed
 */
// require_once 'Zend/Feed/Reader/Extension/DublinCore/Feed.php';

/**
 * @see Zend_Date
 */
// require_once 'Zend/Date.php';

/**
 * @see Zend_Feed_Reader_Collection_Author
 */
// require_once 'Zend/Feed/Reader/Collection/Author.php';

/**
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Reader_Feed_Rss extends Zend_Feed_Reader_FeedAbstract
{
    /**
     * Constructor.
     *
     * @param string $type
     */
    public function __construct(DOMDocument $dom, $type = null)
    {
        parent::__construct($dom, $type);

        $dublinCoreClass = Zend_Feed_Reader::getPluginLoader()->getClassName('DublinCore_Feed');
        $this->_extensions['DublinCore_Feed'] = new $dublinCoreClass($dom, $this->_data['type'], $this->_xpath);
        $atomClass = Zend_Feed_Reader::getPluginLoader()->getClassName('Atom_Feed');
        $this->_extensions['Atom_Feed'] = new $atomClass($dom, $this->_data['type'], $this->_xpath);

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType() && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $xpathPrefix = '/rss/channel';
        } else {
            $xpathPrefix = '/rdf:RDF/rss:channel';
        }
        foreach ($this->_extensions as $extension) {
            $extension->setXpathPrefix($xpathPrefix);
        }
    }

    /**
     * Get a single author.
     *
     * @param int $index
     *
     * @return string|null
     */
    public function getAuthor($index = 0)
    {
        $authors = $this->getAuthors();

        if (isset($authors[$index])) {
            return $authors[$index];
        }

        return null;
    }

    /**
     * Get an array with feed authors.
     *
     * @return array
     */
    public function getAuthors()
    {
        if (array_key_exists('authors', $this->_data)) {
            return $this->_data['authors'];
        }

        $authors = [];
        $authors_dc = $this->getExtension('DublinCore')->getAuthors();
        if (!empty($authors_dc)) {
            foreach ($authors_dc as $author) {
                $authors[] = [
                    'name' => $author['name'],
                ];
            }
        }

        /*
         * Technically RSS doesn't specific author element use at the feed level
         * but it's supported on a "just in case" basis.
         */
        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
        && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $list = $this->_xpath->query('//author');
        } else {
            $list = $this->_xpath->query('//rss:author');
        }
        if ($list->length) {
            foreach ($list as $author) {
                $string = \trim((string) $author->nodeValue);
                $email = null;
                $name = null;
                $data = [];
                // Pretty rough parsing - but it's a catchall
                if (preg_match('/^.*@[^ ]*/', $string, $matches)) {
                    $data['email'] = \trim((string) $matches[0]);
                    if (preg_match("/\((.*)\)$/", $string, $matches)) {
                        $data['name'] = $matches[1];
                    }
                    $authors[] = $data;
                }
            }
        }

        if (0 == count($authors)) {
            $authors = $this->getExtension('Atom')->getAuthors();
        } else {
            $authors = new Zend_Feed_Reader_Collection_Author(
                Zend_Feed_Reader::arrayUnique($authors)
            );
        }

        if (null !== $authors && 0 == count($authors)) {
            $authors = null;
        }

        $this->_data['authors'] = $authors;

        return $this->_data['authors'];
    }

    /**
     * Get the copyright entry.
     *
     * @return string|null
     */
    public function getCopyright()
    {
        if (array_key_exists('copyright', $this->_data)) {
            return $this->_data['copyright'];
        }

        $copyright = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $copyright = $this->_xpath->evaluate('string(/rss/channel/copyright)');
        }

        if (!$copyright && null !== $this->getExtension('DublinCore')) {
            $copyright = $this->getExtension('DublinCore')->getCopyright();
        }

        if (empty($copyright)) {
            $copyright = $this->getExtension('Atom')->getCopyright();
        }

        if (!$copyright) {
            $copyright = null;
        }

        $this->_data['copyright'] = $copyright;

        return $this->_data['copyright'];
    }

    /**
     * Get the feed creation date.
     *
     * @return Zend_Date|null
     */
    public function getDateCreated()
    {
        return $this->getDateModified();
    }

    /**
     * Get the feed modification date.
     *
     * @return Zend_Date
     */
    public function getDateModified()
    {
        if (array_key_exists('datemodified', $this->_data)) {
            return $this->_data['datemodified'];
        }

        $dateModified = null;
        $date = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $dateModified = $this->_xpath->evaluate('string(/rss/channel/pubDate)');
            if (!$dateModified) {
                $dateModified = $this->_xpath->evaluate('string(/rss/channel/lastBuildDate)');
            }
            if ($dateModified) {
                $dateModifiedParsed = strtotime($dateModified);
                if ($dateModifiedParsed) {
                    $date = new Zend_Date($dateModifiedParsed);
                } else {
                    $dateStandards = [Zend_Date::RSS, Zend_Date::RFC_822,
                        Zend_Date::RFC_2822, Zend_Date::DATES];
                    $date = new Zend_Date();
                    foreach ($dateStandards as $standard) {
                        try {
                            $date->set($dateModified, $standard);
                            break;
                        } catch (Zend_Date_Exception $e) {
                            if (Zend_Date::DATES == $standard) {
                                // require_once 'Zend/Feed/Exception.php';
                                throw new Zend_Feed_Exception('Could not load date due to unrecognised format (should follow RFC 822 or 2822):'.$e->getMessage(), 0, $e);
                            }
                        }
                    }
                }
            }
        }

        if (!$date) {
            $date = $this->getExtension('DublinCore')->getDate();
        }

        if (!$date) {
            $date = $this->getExtension('Atom')->getDateModified();
        }

        if (!$date) {
            $date = null;
        }

        $this->_data['datemodified'] = $date;

        return $this->_data['datemodified'];
    }

    /**
     * Get the feed lastBuild date.
     *
     * @return Zend_Date
     */
    public function getLastBuildDate()
    {
        if (array_key_exists('lastBuildDate', $this->_data)) {
            return $this->_data['lastBuildDate'];
        }

        $lastBuildDate = null;
        $date = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $lastBuildDate = $this->_xpath->evaluate('string(/rss/channel/lastBuildDate)');
            if ($lastBuildDate) {
                $lastBuildDateParsed = strtotime($lastBuildDate);
                if ($lastBuildDateParsed) {
                    $date = new Zend_Date($lastBuildDateParsed);
                } else {
                    $dateStandards = [Zend_Date::RSS, Zend_Date::RFC_822,
                        Zend_Date::RFC_2822, Zend_Date::DATES];
                    $date = new Zend_Date();
                    foreach ($dateStandards as $standard) {
                        try {
                            $date->set($lastBuildDate, $standard);
                            break;
                        } catch (Zend_Date_Exception $e) {
                            if (Zend_Date::DATES == $standard) {
                                // require_once 'Zend/Feed/Exception.php';
                                throw new Zend_Feed_Exception('Could not load date due to unrecognised format (should follow RFC 822 or 2822):'.$e->getMessage(), 0, $e);
                            }
                        }
                    }
                }
            }
        }

        if (!$date) {
            $date = null;
        }

        $this->_data['lastBuildDate'] = $date;

        return $this->_data['lastBuildDate'];
    }

    /**
     * Get the feed description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        if (array_key_exists('description', $this->_data)) {
            return $this->_data['description'];
        }

        $description = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $description = $this->_xpath->evaluate('string(/rss/channel/description)');
        } else {
            $description = $this->_xpath->evaluate('string(/rdf:RDF/rss:channel/rss:description)');
        }

        if (!$description && null !== $this->getExtension('DublinCore')) {
            $description = $this->getExtension('DublinCore')->getDescription();
        }

        if (empty($description)) {
            $description = $this->getExtension('Atom')->getDescription();
        }

        if (!$description) {
            $description = null;
        }

        $this->_data['description'] = $description;

        return $this->_data['description'];
    }

    /**
     * Get the feed ID.
     *
     * @return string|null
     */
    public function getId()
    {
        if (array_key_exists('id', $this->_data)) {
            return $this->_data['id'];
        }

        $id = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $id = $this->_xpath->evaluate('string(/rss/channel/guid)');
        }

        if (!$id && null !== $this->getExtension('DublinCore')) {
            $id = $this->getExtension('DublinCore')->getId();
        }

        if (empty($id)) {
            $id = $this->getExtension('Atom')->getId();
        }

        if (!$id) {
            if ($this->getLink()) {
                $id = $this->getLink();
            } elseif ($this->getTitle()) {
                $id = $this->getTitle();
            } else {
                $id = null;
            }
        }

        $this->_data['id'] = $id;

        return $this->_data['id'];
    }

    /**
     * Get the feed image data.
     *
     * @return array|null
     */
    public function getImage()
    {
        if (array_key_exists('image', $this->_data)) {
            return $this->_data['image'];
        }

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $list = $this->_xpath->query('/rss/channel/image');
            $prefix = '/rss/channel/image[1]';
        } else {
            $list = $this->_xpath->query('/rdf:RDF/rss:channel/rss:image');
            $prefix = '/rdf:RDF/rss:channel/rss:image[1]';
        }
        if ($list->length > 0) {
            $image = [];
            $value = $this->_xpath->evaluate('string('.$prefix.'/url)');
            if ($value) {
                $image['uri'] = $value;
            }
            $value = $this->_xpath->evaluate('string('.$prefix.'/link)');
            if ($value) {
                $image['link'] = $value;
            }
            $value = $this->_xpath->evaluate('string('.$prefix.'/title)');
            if ($value) {
                $image['title'] = $value;
            }
            $value = $this->_xpath->evaluate('string('.$prefix.'/height)');
            if ($value) {
                $image['height'] = $value;
            }
            $value = $this->_xpath->evaluate('string('.$prefix.'/width)');
            if ($value) {
                $image['width'] = $value;
            }
            $value = $this->_xpath->evaluate('string('.$prefix.'/description)');
            if ($value) {
                $image['description'] = $value;
            }
        } else {
            $image = null;
        }

        $this->_data['image'] = $image;

        return $this->_data['image'];
    }

    /**
     * Get the feed language.
     *
     * @return string|null
     */
    public function getLanguage()
    {
        if (array_key_exists('language', $this->_data)) {
            return $this->_data['language'];
        }

        $language = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $language = $this->_xpath->evaluate('string(/rss/channel/language)');
        }

        if (!$language && null !== $this->getExtension('DublinCore')) {
            $language = $this->getExtension('DublinCore')->getLanguage();
        }

        if (empty($language)) {
            $language = $this->getExtension('Atom')->getLanguage();
        }

        if (!$language) {
            $language = $this->_xpath->evaluate('string(//@xml:lang[1])');
        }

        if (!$language) {
            $language = null;
        }

        $this->_data['language'] = $language;

        return $this->_data['language'];
    }

    /**
     * Get a link to the feed.
     *
     * @return string|null
     */
    public function getLink()
    {
        if (array_key_exists('link', $this->_data)) {
            return $this->_data['link'];
        }

        $link = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $link = $this->_xpath->evaluate('string(/rss/channel/link)');
        } else {
            $link = $this->_xpath->evaluate('string(/rdf:RDF/rss:channel/rss:link)');
        }

        if (empty($link)) {
            $link = $this->getExtension('Atom')->getLink();
        }

        if (!$link) {
            $link = null;
        }

        $this->_data['link'] = $link;

        return $this->_data['link'];
    }

    /**
     * Get a link to the feed XML.
     *
     * @return string|null
     */
    public function getFeedLink()
    {
        if (array_key_exists('feedlink', $this->_data)) {
            return $this->_data['feedlink'];
        }

        $link = null;

        $link = $this->getExtension('Atom')->getFeedLink();

        if (null === $link || empty($link)) {
            $link = $this->getOriginalSourceUri();
        }

        $this->_data['feedlink'] = $link;

        return $this->_data['feedlink'];
    }

    /**
     * Get the feed generator entry.
     *
     * @return string|null
     */
    public function getGenerator()
    {
        if (array_key_exists('generator', $this->_data)) {
            return $this->_data['generator'];
        }

        $generator = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $generator = $this->_xpath->evaluate('string(/rss/channel/generator)');
        }

        if (!$generator) {
            if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
                $generator = $this->_xpath->evaluate('string(/rss/channel/atom:generator)');
            } else {
                $generator = $this->_xpath->evaluate('string(/rdf:RDF/rss:channel/atom:generator)');
            }
        }

        if (empty($generator)) {
            $generator = $this->getExtension('Atom')->getGenerator();
        }

        if (!$generator) {
            $generator = null;
        }

        $this->_data['generator'] = $generator;

        return $this->_data['generator'];
    }

    /**
     * Get the feed title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        if (array_key_exists('title', $this->_data)) {
            return $this->_data['title'];
        }

        $title = null;

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $title = $this->_xpath->evaluate('string(/rss/channel/title)');
        } else {
            $title = $this->_xpath->evaluate('string(/rdf:RDF/rss:channel/rss:title)');
        }

        if (!$title && null !== $this->getExtension('DublinCore')) {
            $title = $this->getExtension('DublinCore')->getTitle();
        }

        if (!$title) {
            $title = $this->getExtension('Atom')->getTitle();
        }

        if (!$title) {
            $title = null;
        }

        $this->_data['title'] = $title;

        return $this->_data['title'];
    }

    /**
     * Get an array of any supported Pusubhubbub endpoints.
     *
     * @return array|null
     */
    public function getHubs()
    {
        if (array_key_exists('hubs', $this->_data)) {
            return $this->_data['hubs'];
        }

        $hubs = $this->getExtension('Atom')->getHubs();

        if (empty($hubs)) {
            $hubs = null;
        } else {
            $hubs = array_unique($hubs);
        }

        $this->_data['hubs'] = $hubs;

        return $this->_data['hubs'];
    }

    /**
     * Get all categories.
     *
     * @return Zend_Feed_Reader_Collection_Category
     */
    public function getCategories()
    {
        if (array_key_exists('categories', $this->_data)) {
            return $this->_data['categories'];
        }

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType()
            && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $list = $this->_xpath->query('/rss/channel//category');
        } else {
            $list = $this->_xpath->query('/rdf:RDF/rss:channel//rss:category');
        }

        if ($list->length) {
            $categoryCollection = new Zend_Feed_Reader_Collection_Category();
            foreach ($list as $category) {
                $categoryCollection[] = [
                    'term' => $category->nodeValue,
                    'scheme' => $category->getAttribute('domain'),
                    'label' => $category->nodeValue,
                ];
            }
        } else {
            $categoryCollection = $this->getExtension('DublinCore')->getCategories();
        }

        if (0 == count($categoryCollection)) {
            $categoryCollection = $this->getExtension('Atom')->getCategories();
        }

        $this->_data['categories'] = $categoryCollection;

        return $this->_data['categories'];
    }

    /**
     * Read all entries to the internal entries array.
     */
    protected function _indexEntries()
    {
        $entries = [];

        if (Zend_Feed_Reader::TYPE_RSS_10 !== $this->getType() && Zend_Feed_Reader::TYPE_RSS_090 !== $this->getType()) {
            $entries = $this->_xpath->evaluate('//item');
        } else {
            $entries = $this->_xpath->evaluate('//rss:item');
        }

        foreach ($entries as $index => $entry) {
            $this->_entries[$index] = $entry;
        }
    }

    /**
     * Register the default namespaces for the current feed format.
     */
    protected function _registerNamespaces()
    {
        switch ($this->_data['type']) {
            case Zend_Feed_Reader::TYPE_RSS_10:
                $this->_xpath->registerNamespace('rdf', Zend_Feed_Reader::NAMESPACE_RDF);
                $this->_xpath->registerNamespace('rss', Zend_Feed_Reader::NAMESPACE_RSS_10);
                break;

            case Zend_Feed_Reader::TYPE_RSS_090:
                $this->_xpath->registerNamespace('rdf', Zend_Feed_Reader::NAMESPACE_RDF);
                $this->_xpath->registerNamespace('rss', Zend_Feed_Reader::NAMESPACE_RSS_090);
                break;
        }
    }
}
