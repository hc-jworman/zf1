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
 * @see Zend_Gdata
 */
// require_once 'Zend/Gdata.php';

/**
 * @see Zend_Gdata_Photos_UserFeed
 */
// require_once 'Zend/Gdata/Photos/UserFeed.php';

/**
 * @see Zend_Gdata_Photos_AlbumFeed
 */
// require_once 'Zend/Gdata/Photos/AlbumFeed.php';

/**
 * @see Zend_Gdata_Photos_PhotoFeed
 */
// require_once 'Zend/Gdata/Photos/PhotoFeed.php';

/**
 * Service class for interacting with the Google Photos Data API.
 *
 * Like other service classes in this module, this class provides access via
 * an HTTP client to Google servers for working with entries and feeds.
 *
 * @see http://code.google.com/apis/picasaweb/gdata.html
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Photos extends Zend_Gdata
{
    public const PICASA_BASE_URI = 'https://picasaweb.google.com/data';
    public const PICASA_BASE_FEED_URI = 'https://picasaweb.google.com/data/feed';
    public const AUTH_SERVICE_NAME = 'lh2';

    /**
     * Default projection when interacting with the Picasa server.
     */
    public const DEFAULT_PROJECTION = 'api';

    /**
     * The default visibility to filter events by.
     */
    public const DEFAULT_VISIBILITY = 'all';

    /**
     * The default user to retrieve feeds for.
     */
    public const DEFAULT_USER = 'default';

    /**
     * Path to the user feed on the Picasa server.
     */
    public const USER_PATH = 'user';

    /**
     * Path to album feeds on the Picasa server.
     */
    public const ALBUM_PATH = 'albumid';

    /**
     * Path to photo feeds on the Picasa server.
     */
    public const PHOTO_PATH = 'photoid';

    /**
     * The path to the community search feed on the Picasa server.
     */
    public const COMMUNITY_SEARCH_PATH = 'all';

    /**
     * The path to use for finding links to feeds within entries.
     */
    public const FEED_LINK_PATH = 'http://schemas.google.com/g/2005#feed';

    /**
     * The path to use for the determining type of an entry.
     */
    public const KIND_PATH = 'http://schemas.google.com/g/2005#kind';

    /**
     * Namespaces used for Zend_Gdata_Photos.
     *
     * @var array
     */
    public static $namespaces = [
        ['gphoto', 'http://schemas.google.com/photos/2007', 1, 0],
        ['photo', 'http://www.pheed.com/pheed/', 1, 0],
        ['exif', 'http://schemas.google.com/photos/exif/2007', 1, 0],
        ['georss', 'http://www.georss.org/georss', 1, 0],
        ['gml', 'http://www.opengis.net/gml', 1, 0],
        ['media', 'http://search.yahoo.com/mrss/', 1, 0],
    ];

    /**
     * Create Zend_Gdata_Photos object.
     *
     * @param zend_Http_Client $client        (optional) The HTTP client to use when
     *                                        when communicating with the servers
     * @param string           $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend_Gdata_Photos');
        $this->registerPackage('Zend_Gdata_Photos_Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retrieve a UserFeed containing AlbumEntries, PhotoEntries and
     * TagEntries associated with a given user.
     *
     * @param string $userName The userName of interest
     * @param mixed  $location (optional) The location for the feed, as a URL
     *                         or Query. If not provided, a default URL will be used instead.
     *
     * @return string|Zend_Gdata_App_Feed
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getUserFeed($userName = null, $location = null)
    {
        if ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('feed');
            if (null !== $userName) {
                $location->setUser($userName);
            }
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            if (null !== $userName) {
                $location->setUser($userName);
            }
            $uri = $location->getQueryUrl();
        } elseif (null !== $location) {
            $uri = $location;
        } elseif (null !== $userName) {
            $uri = self::PICASA_BASE_FEED_URI.'/'.
                self::DEFAULT_PROJECTION.'/'.self::USER_PATH.'/'.
                $userName;
        } else {
            $uri = self::PICASA_BASE_FEED_URI.'/'.
                self::DEFAULT_PROJECTION.'/'.self::USER_PATH.'/'.
                self::DEFAULT_USER;
        }

        return parent::getFeed($uri, 'Zend_Gdata_Photos_UserFeed');
    }

    /**
     * Retreive AlbumFeed object containing multiple PhotoEntry or TagEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Feed
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getAlbumFeed($location = null)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('feed');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getFeed($uri, 'Zend_Gdata_Photos_AlbumFeed');
    }

    /**
     * Retreive PhotoFeed object containing comments and tags associated
     * with a given photo.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *                        or Query. If not specified, the community search feed will
     *                        be returned instead.
     *
     * @return string|Zend_Gdata_App_Feed
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getPhotoFeed($location = null)
    {
        if (null === $location) {
            $uri = self::PICASA_BASE_FEED_URI.'/'.
                self::DEFAULT_PROJECTION.'/'.
                self::COMMUNITY_SEARCH_PATH;
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('feed');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getFeed($uri, 'Zend_Gdata_Photos_PhotoFeed');
    }

    /**
     * Retreive a single UserEntry object.
     *
     * @param mixed $location the location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Entry
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getUserEntry($location)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getEntry($uri, 'Zend_Gdata_Photos_UserEntry');
    }

    /**
     * Retreive a single AlbumEntry object.
     *
     * @param mixed $location the location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Entry
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getAlbumEntry($location)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getEntry($uri, 'Zend_Gdata_Photos_AlbumEntry');
    }

    /**
     * Retreive a single PhotoEntry object.
     *
     * @param mixed $location the location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Entry
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getPhotoEntry($location)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getEntry($uri, 'Zend_Gdata_Photos_PhotoEntry');
    }

    /**
     * Retreive a single TagEntry object.
     *
     * @param mixed $location the location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Entry
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getTagEntry($location)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getEntry($uri, 'Zend_Gdata_Photos_TagEntry');
    }

    /**
     * Retreive a single CommentEntry object.
     *
     * @param mixed $location the location for the feed, as a URL or Query
     *
     * @return string|Zend_Gdata_App_Entry
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function getCommentEntry($location)
    {
        if (null === $location) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('Location must not be null');
        } elseif ($location instanceof Zend_Gdata_Photos_UserQuery) {
            $location->setType('entry');
            $uri = $location->getQueryUrl();
        } elseif ($location instanceof Zend_Gdata_Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }

        return parent::getEntry($uri, 'Zend_Gdata_Photos_CommentEntry');
    }

    /**
     * Create a new album from a AlbumEntry.
     *
     * @param Zend_Gdata_Photos_AlbumEntry $album the album entry to
     *                                            insert
     *
     * @return Zend_Gdata_App_Entry the inserted album entry as
     *                              returned by the server
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function insertAlbumEntry($album, $uri = null)
    {
        if (null === $uri) {
            $uri = self::PICASA_BASE_FEED_URI.'/'.
                self::DEFAULT_PROJECTION.'/'.self::USER_PATH.'/'.
                self::DEFAULT_USER;
        }
        $newEntry = $this->insertEntry($album, $uri, 'Zend_Gdata_Photos_AlbumEntry');

        return $newEntry;
    }

    /**
     * Create a new photo from a PhotoEntry.
     *
     * @param Zend_Gdata_Photos_PhotoEntry $photo the photo to insert
     *
     * @return Zend_Gdata_App_Entry the inserted photo entry
     *                              as returned by the server
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function insertPhotoEntry($photo, $uri = null)
    {
        if ($uri instanceof Zend_Gdata_Photos_AlbumEntry) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if (null === $uri) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('URI must not be null');
        }
        $newEntry = $this->insertEntry($photo, $uri, 'Zend_Gdata_Photos_PhotoEntry');

        return $newEntry;
    }

    /**
     * Create a new tag from a TagEntry.
     *
     * @param Zend_Gdata_Photos_TagEntry $tag the tag entry to insert
     *
     * @return Zend_Gdata_App_Entry the inserted tag entry as returned
     *                              by the server
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function insertTagEntry($tag, $uri = null)
    {
        if ($uri instanceof Zend_Gdata_Photos_PhotoEntry) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if (null === $uri) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('URI must not be null');
        }
        $newEntry = $this->insertEntry($tag, $uri, 'Zend_Gdata_Photos_TagEntry');

        return $newEntry;
    }

    /**
     * Create a new comment from a CommentEntry.
     *
     * @param Zend_Gdata_Photos_CommentEntry $comment the comment entry to
     *                                                insert
     *
     * @return Zend_Gdata_App_Entry the inserted comment entry
     *                              as returned by the server
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function insertCommentEntry($comment, $uri = null)
    {
        if ($uri instanceof Zend_Gdata_Photos_PhotoEntry) {
            $uri = $uri->getLink(self::FEED_LINK_PATH)->href;
        }
        if (null === $uri) {
            // require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException('URI must not be null');
        }
        $newEntry = $this->insertEntry($comment, $uri, 'Zend_Gdata_Photos_CommentEntry');

        return $newEntry;
    }

    /**
     * Delete an AlbumEntry.
     *
     * @param Zend_Gdata_Photos_AlbumEntry $album the album entry to
     *                                            delete
     * @param bool                         $catch Whether to catch an exception when
     *                                            modified and re-delete or throw
     *
     * @return void
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function deleteAlbumEntry($album, $catch)
    {
        if ($catch) {
            try {
                $this->delete($album);
            } catch (Zend_Gdata_App_HttpException $e) {
                if (409 === $e->getResponse()->getStatus()) {
                    $entry = new Zend_Gdata_Photos_AlbumEntry($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($album);
        }
    }

    /**
     * Delete a PhotoEntry.
     *
     * @param Zend_Gdata_Photos_PhotoEntry $photo the photo entry to
     *                                            delete
     * @param bool                         $catch Whether to catch an exception when
     *                                            modified and re-delete or throw
     *
     * @return void
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function deletePhotoEntry($photo, $catch)
    {
        if ($catch) {
            try {
                $this->delete($photo);
            } catch (Zend_Gdata_App_HttpException $e) {
                if (409 === $e->getResponse()->getStatus()) {
                    $entry = new Zend_Gdata_Photos_PhotoEntry($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($photo);
        }
    }

    /**
     * Delete a CommentEntry.
     *
     * @param Zend_Gdata_Photos_CommentEntry $comment the comment entry to
     *                                                delete
     * @param bool                           $catch   Whether to catch an exception when
     *                                                modified and re-delete or throw
     *
     * @return void
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function deleteCommentEntry($comment, $catch)
    {
        if ($catch) {
            try {
                $this->delete($comment);
            } catch (Zend_Gdata_App_HttpException $e) {
                if (409 === $e->getResponse()->getStatus()) {
                    $entry = new Zend_Gdata_Photos_CommentEntry($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($comment);
        }
    }

    /**
     * Delete a TagEntry.
     *
     * @param Zend_Gdata_Photos_TagEntry $tag   the tag entry to
     *                                          delete
     * @param bool                       $catch Whether to catch an exception when
     *                                          modified and re-delete or throw
     *
     * @return void
     *
     * @throws Zend_Gdata_App_Exception
     * @throws Zend_Gdata_App_HttpException
     */
    public function deleteTagEntry($tag, $catch)
    {
        if ($catch) {
            try {
                $this->delete($tag);
            } catch (Zend_Gdata_App_HttpException $e) {
                if (409 === $e->getResponse()->getStatus()) {
                    $entry = new Zend_Gdata_Photos_TagEntry($e->getResponse()->getBody());
                    $this->delete($entry->getLink('edit')->href);
                } else {
                    throw $e;
                }
            }
        } else {
            $this->delete($tag);
        }
    }
}
