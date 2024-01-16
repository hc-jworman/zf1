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
 * @see Zend_Service_Amazon_S3
 */
// require_once 'Zend/Service/Amazon/S3.php';

/**
 * Amazon S3 PHP stream wrapper.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_S3_Stream
{
    /**
     * @var bool Write the buffer on fflush()?
     */
    private $_writeBuffer = false;

    /**
     * @var int Current read/write position
     */
    private $_position = 0;

    /**
     * @var int Total size of the object as returned by S3 (Content-length)
     */
    private $_objectSize = 0;

    /**
     * @var string File name to interact with
     */
    private $_objectName;

    /**
     * @var string Current read/write buffer
     */
    private $_objectBuffer;

    /**
     * @var array Available buckets
     */
    private $_bucketList = [];

    /**
     * @var Zend_Service_Amazon_S3
     */
    private $_s3;

    /**
     * Retrieve client for this stream type.
     *
     * @param string $path
     *
     * @return Zend_Service_Amazon_S3
     */
    protected function _getS3Client($path)
    {
        if (null === $this->_s3) {
            $url = explode(':', $path);

            if (!$url) {
                /*
                 * @see Zend_Service_Amazon_S3_Exception
                 */
                // require_once 'Zend/Service/Amazon/S3/Exception.php';
                throw new Zend_Service_Amazon_S3_Exception("Unable to parse URL $path");
            }

            $this->_s3 = Zend_Service_Amazon_S3::getWrapperClient($url[0]);
            if (!$this->_s3) {
                /*
                 * @see Zend_Service_Amazon_S3_Exception
                 */
                // require_once 'Zend/Service/Amazon/S3/Exception.php';
                throw new Zend_Service_Amazon_S3_Exception("Unknown client for wrapper {$url[0]}");
            }
        }

        return $this->_s3;
    }

    /**
     * Extract object name from URL.
     *
     * @param string $path
     *
     * @return string
     */
    protected function _getNamePart($path)
    {
        $url = parse_url($path);
        if ($url['host']) {
            return !empty($url['path']) ? $url['host'].$url['path'] : $url['host'];
        }

        return '';
    }

    /**
     * Open the stream.
     *
     * @param string $path
     * @param string $mode
     * @param int    $options
     * @param string $opened_path
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $name = $this->_getNamePart($path);
        // If we open the file for writing, just return true. Create the object
        // on fflush call
        if (strpbrk($mode, 'wax')) {
            $this->_objectName = $name;
            $this->_objectBuffer = null;
            $this->_objectSize = 0;
            $this->_position = 0;
            $this->_writeBuffer = true;
            $this->_getS3Client($path);

            return true;
        } else {
            // Otherwise, just see if the file exists or not
            $info = $this->_getS3Client($path)->getInfo($name);
            if ($info) {
                $this->_objectName = $name;
                $this->_objectBuffer = null;
                $this->_objectSize = $info['size'];
                $this->_position = 0;
                $this->_writeBuffer = false;
                $this->_getS3Client($path);

                return true;
            }
        }

        return false;
    }

    /**
     * Close the stream.
     *
     * @return void
     */
    public function stream_close()
    {
        $this->_objectName = null;
        $this->_objectBuffer = null;
        $this->_objectSize = 0;
        $this->_position = 0;
        $this->_writeBuffer = false;
        unset($this->_s3);
    }

    /**
     * Read from the stream.
     *
     * http://bugs.php.net/21641 - stream_read() is always passed PHP's
     * internal read buffer size (8192) no matter what is passed as $count
     * parameter to fread().
     *
     * @param int $count
     *
     * @return string
     */
    public function stream_read($count)
    {
        if (!$this->_objectName) {
            return false;
        }

        // make sure that count doesn't exceed object size
        if ($count + $this->_position > $this->_objectSize) {
            $count = $this->_objectSize - $this->_position;
        }

        $range_start = $this->_position;
        $range_end = $this->_position + $count - 1;

        // Only fetch more data from S3 if we haven't fetched any data yet (postion=0)
        // OR, the range end position plus 1 is greater than the size of the current
        // object buffer
        if (null === $this->_objectBuffer || $range_end >= strlen((string) $this->_objectBuffer)) {
            $headers = [
                'Range' => "bytes=$range_start-$range_end",
            ];

            $response = $this->_s3->_makeRequest('GET', $this->_objectName, null, $headers);

            if (206 == $response->getStatus()) { // 206 Partial Content
                $this->_objectBuffer .= $response->getBody();
            }
        }

        $data = substr((string) $this->_objectBuffer, $this->_position, $count);
        $this->_position += strlen((string) $data);

        return $data;
    }

    /**
     * Write to the stream.
     *
     * @param string $data
     *
     * @return int
     */
    public function stream_write($data)
    {
        if (!$this->_objectName) {
            return 0;
        }
        $len = strlen((string) $data);
        $this->_objectBuffer .= $data;
        $this->_objectSize += $len;

        // TODO: handle current position for writing!
        return $len;
    }

    /**
     * End of the stream?
     *
     * @return bool
     */
    public function stream_eof()
    {
        if (!$this->_objectName) {
            return true;
        }

        return $this->_position >= $this->_objectSize;
    }

    /**
     * What is the current read/write position of the stream.
     *
     * @return int
     */
    public function stream_tell()
    {
        return $this->_position;
    }

    /**
     * Update the read/write position of the stream.
     *
     * @param int $offset
     * @param int $whence
     *
     * @return bool
     */
    public function stream_seek($offset, $whence)
    {
        if (!$this->_objectName) {
            return false;
        }

        switch ($whence) {
            case SEEK_CUR:
                // Set position to current location plus $offset
                $new_pos = $this->_position + $offset;
                break;
            case SEEK_END:
                // Set position to end-of-file plus $offset
                $new_pos = $this->_objectSize + $offset;
                break;
            case SEEK_SET:
            default:
                // Set position equal to $offset
                $new_pos = $offset;
                break;
        }
        $ret = ($new_pos >= 0 && $new_pos <= $this->_objectSize);
        if ($ret) {
            $this->_position = $new_pos;
        }

        return $ret;
    }

    /**
     * Flush current cached stream data to storage.
     *
     * @return bool
     */
    public function stream_flush()
    {
        // If the stream wasn't opened for writing, just return false
        if (!$this->_writeBuffer) {
            return false;
        }

        $ret = $this->_s3->putObject($this->_objectName, $this->_objectBuffer);

        $this->_objectBuffer = null;

        return $ret;
    }

    /**
     * Returns data array of stream variables.
     *
     * @return array
     */
    public function stream_stat()
    {
        if (!$this->_objectName) {
            return false;
        }

        $stat = [];
        $stat['dev'] = 0;
        $stat['ino'] = 0;
        $stat['mode'] = 0777;
        $stat['nlink'] = 0;
        $stat['uid'] = 0;
        $stat['gid'] = 0;
        $stat['rdev'] = 0;
        $stat['size'] = 0;
        $stat['atime'] = 0;
        $stat['mtime'] = 0;
        $stat['ctime'] = 0;
        $stat['blksize'] = 0;
        $stat['blocks'] = 0;

        if (($slash = strchr($this->_objectName, '/')) === false || $slash == strlen((string) $this->_objectName) - 1) {
            /* bucket */
            $stat['mode'] |= 040000;
        } else {
            $stat['mode'] |= 0100000;
        }
        $info = $this->_s3->getInfo($this->_objectName);
        if (!empty($info)) {
            $stat['size'] = $info['size'];
            $stat['atime'] = time();
            $stat['mtime'] = $info['mtime'];
        }

        return $stat;
    }

    /**
     * Attempt to delete the item.
     *
     * @param string $path
     *
     * @return bool
     */
    public function unlink($path)
    {
        return $this->_getS3Client($path)->removeObject($this->_getNamePart($path));
    }

    /**
     * Attempt to rename the item.
     *
     * @param string $path_from
     * @param string $path_to
     *
     * @return bool False
     */
    public function rename($path_from, $path_to)
    {
        // TODO: Renaming isn't supported, always return false
        return false;
    }

    /**
     * Create a new directory.
     *
     * @param string $path
     * @param int    $mode
     * @param int    $options
     *
     * @return bool
     */
    public function mkdir($path, $mode, $options)
    {
        return $this->_getS3Client($path)->createBucket(parse_url($path, PHP_URL_HOST));
    }

    /**
     * Remove a directory.
     *
     * @param string $path
     * @param int    $options
     *
     * @return bool
     */
    public function rmdir($path, $options)
    {
        return $this->_getS3Client($path)->removeBucket(parse_url($path, PHP_URL_HOST));
    }

    /**
     * Attempt to open a directory.
     *
     * @param string $path
     * @param int    $options
     *
     * @return bool
     */
    public function dir_opendir($path, $options)
    {
        if (preg_match('@^([a-z0-9+.]|-)+://$@', $path)) {
            $this->_bucketList = $this->_getS3Client($path)->getBuckets();
        } else {
            $host = parse_url($path, PHP_URL_HOST);
            $this->_bucketList = $this->_getS3Client($path)->getObjectsByBucket($host);
        }

        return false !== $this->_bucketList;
    }

    /**
     * Return array of URL variables.
     *
     * @param string $path
     * @param int    $flags
     *
     * @return array
     */
    public function url_stat($path, $flags)
    {
        $stat = [];
        $stat['dev'] = 0;
        $stat['ino'] = 0;
        $stat['mode'] = 0777;
        $stat['nlink'] = 0;
        $stat['uid'] = 0;
        $stat['gid'] = 0;
        $stat['rdev'] = 0;
        $stat['size'] = 0;
        $stat['atime'] = 0;
        $stat['mtime'] = 0;
        $stat['ctime'] = 0;
        $stat['blksize'] = 0;
        $stat['blocks'] = 0;

        $name = $this->_getNamePart($path);
        if (($slash = strchr($name, '/')) === false || $slash == strlen((string) $name) - 1) {
            /* bucket */
            $stat['mode'] |= 040000;
        } else {
            $stat['mode'] |= 0100000;
        }
        $info = $this->_getS3Client($path)->getInfo($name);

        if (!empty($info)) {
            $stat['size'] = $info['size'];
            $stat['atime'] = time();
            $stat['mtime'] = $info['mtime'];
        }

        return $stat;
    }

    /**
     * Return the next filename in the directory.
     *
     * @return string
     */
    public function dir_readdir()
    {
        $object = current($this->_bucketList);
        if (false !== $object) {
            next($this->_bucketList);
        }

        return $object;
    }

    /**
     * Reset the directory pointer.
     *
     * @return bool True
     */
    public function dir_rewinddir()
    {
        reset($this->_bucketList);

        return true;
    }

    /**
     * Close a directory.
     *
     * @return bool True
     */
    public function dir_closedir()
    {
        $this->_bucketList = [];

        return true;
    }
}
