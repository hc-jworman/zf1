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
 * @see Zend_Db_Statement
 */
// require_once 'Zend/Db/Statement.php';

/**
 * Proxy class to wrap a PDOStatement object.
 * Matches the interface of PDOStatement.  All methods simply proxy to the
 * matching method in PDOStatement.  PDOExceptions thrown by PDOStatement
 * are re-thrown as Zend_Db_Statement_Exception.
 *
 * @category   Zend
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Statement_Pdo extends Zend_Db_Statement implements IteratorAggregate
{
    /**
     * @var int
     */
    protected $_fetchMode = PDO::FETCH_ASSOC;

    /**
     * Prepare a string SQL statement and create a statement object.
     *
     * @param string $sql
     *
     * @return void
     *
     * @throws Zend_Db_Statement_Exception
     */
    protected function _prepare($sql)
    {
        try {
            $this->_stmt = $this->_adapter->getConnection()->prepare($sql);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Bind a column of the statement result set to a PHP variable.
     *
     * @param string $column name the column in the result set, either by
     *                       position or by name
     * @param mixed  $param  reference to the PHP variable containing the value
     * @param mixed  $type   OPTIONAL
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function bindColumn($column, &$param, $type = null)
    {
        try {
            if (null === $type) {
                return $this->_stmt->bindColumn($column, $param);
            } else {
                return $this->_stmt->bindColumn($column, $param, $type);
            }
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter name the parameter, either integer or string
     * @param mixed $variable  reference to PHP variable containing the value
     * @param mixed $type      OPTIONAL Datatype of SQL parameter
     * @param mixed $length    OPTIONAL Length of SQL parameter
     * @param mixed $options   OPTIONAL Other options
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    protected function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        try {
            if (null === $type) {
                if (is_bool($variable)) {
                    $type = PDO::PARAM_BOOL;
                } elseif (null === $variable) {
                    $type = PDO::PARAM_NULL;
                } elseif (is_integer($variable)) {
                    $type = PDO::PARAM_INT;
                } else {
                    $type = PDO::PARAM_STR;
                }
            }

            return $this->_stmt->bindParam($parameter, $variable, $type, (int) $length, $options);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Binds a value to a parameter.
     *
     * @param mixed $parameter name the parameter, either integer or string
     * @param mixed $value     scalar value to bind to the parameter
     * @param mixed $type      OPTIONAL Datatype of the parameter
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function bindValue($parameter, $value, $type = null)
    {
        if (is_string($parameter) && ':' != $parameter[0]) {
            $parameter = ":$parameter";
        }

        $this->_bindParam[$parameter] = $value;

        try {
            if (null === $type) {
                return $this->_stmt->bindValue($parameter, $value);
            } else {
                return $this->_stmt->bindValue($parameter, $value, $type);
            }
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function closeCursor()
    {
        try {
            return $this->_stmt->closeCursor();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int the number of columns
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function columnCount()
    {
        try {
            return $this->_stmt->columnCount();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Retrieves the error code, if any, associated with the last operation on
     * the statement handle.
     *
     * @return string error code
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function errorCode()
    {
        try {
            return $this->_stmt->errorCode();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Retrieves an array of error information, if any, associated with the
     * last operation on the statement handle.
     *
     * @return array
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function errorInfo()
    {
        try {
            return $this->_stmt->errorInfo();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function _execute(array $params = null)
    {
        try {
            if (null !== $params) {
                return $this->_stmt->execute($params);
            } else {
                return $this->_stmt->execute();
            }
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            $message = sprintf('%s, query was: %s', $e->getMessage(), $this->_stmt->queryString);
            throw new Zend_Db_Statement_Exception($message, (int) $e->getCode(), $e);
        }
    }

    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation
     * @param int $cursor OPTIONAL Absolute, relative, or other
     * @param int $offset OPTIONAL Number for absolute or relative cursors
     *
     * @return mixed array, object, or scalar depending on fetch mode
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (null === $style) {
            $style = $this->_fetchMode;
        }
        try {
            return $this->_stmt->fetch($style, (int) $cursor, (int) $offset);
        } catch (ValueError $e) {
            // PHP 8.0+ throws ValueError on invalid arguments
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Required by IteratorAggregate interface.
     *
     * @return IteratorIterator
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new IteratorIterator($this->_stmt);
    }

    /**
     * Returns an array containing all of the result set rows.
     *
     * @param int $style OPTIONAL Fetch mode
     * @param int $col   OPTIONAL Column number, if fetch mode is by column
     *
     * @return array collection of rows, each in a format by the fetch mode
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchAll($style = null, $col = null)
    {
        if (null === $style) {
            $style = $this->_fetchMode;
        }
        try {
            if (PDO::FETCH_COLUMN == $style) {
                if (null === $col) {
                    $col = 0;
                }

                return $this->_stmt->fetchAll($style, $col);
            } else {
                return $this->_stmt->fetchAll($style);
            }
        } catch (ValueError $e) {
            // PHP 8.0+ throws ValueError on invalid arguments
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns a single column from the next row of a result set.
     *
     * @param int $col OPTIONAL Position of the column to fetch
     *
     * @return string
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchColumn($col = 0)
    {
        try {
            return $this->_stmt->fetchColumn($col);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Fetches the next row and returns it as an object.
     *
     * @param string $class  OPTIONAL Name of the class to create
     * @param array  $config OPTIONAL Constructor arguments for the class
     *
     * @return mixed one object instance of the specified class
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchObject($class = 'stdClass', array $config = [])
    {
        try {
            return $this->_stmt->fetchObject($class, $config);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Retrieve a statement attribute.
     *
     * @param int $key attribute name
     *
     * @return mixed attribute value
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function getAttribute($key)
    {
        try {
            return $this->_stmt->getAttribute($key);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns metadata for a column in a result set.
     *
     * @param int $column
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function getColumnMeta($column)
    {
        try {
            return $this->_stmt->getColumnMeta($column);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function nextRowset()
    {
        try {
            return $this->_stmt->nextRowset();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Returns the number of rows affected by the execution of the
     * last INSERT, DELETE, or UPDATE statement executed by this
     * statement object.
     *
     * @return int the number of rows affected
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function rowCount()
    {
        try {
            return $this->_stmt->rowCount();
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Set a statement attribute.
     *
     * @param string $key attribute name
     * @param mixed  $val attribute value
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function setAttribute($key, $val)
    {
        try {
            return $this->_stmt->setAttribute($key, $val);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Set the default fetch mode for this statement.
     *
     * @param int $mode the fetch mode
     *
     * @return bool
     *
     * @throws Zend_Db_Statement_Exception
     */
    public function setFetchMode($mode)
    {
        $this->_fetchMode = $mode;
        try {
            return $this->_stmt->setFetchMode($mode);
        } catch (ValueError $e) {
            // PHP 8.0+ throws ValueError on invalid arguments
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        } catch (PDOException $e) {
            // require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
