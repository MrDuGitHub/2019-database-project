<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 17:00
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class MySQL
{
    private static $_instance;

    /**
     * 对象初始化时连接数据库，若失败，抛出异常。
     * @throws \Exception
     */
    public static function get_instance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    private function __clone()
    {
    }

    private $_conn;

    /**
     * 对象初始化时连接数据库，若失败，抛出异常。
     * @throws \Exception
     */
    private function __construct()
    {
        $this->_conn = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_errno()) throw new \Exception('数据库连接失败。' . mysqli_connect_errno() . ':' . mysqli_connect_error());

        $this->_conn->set_charset("utf8");
    }

    function __destruct()
    {
        $this->_conn->close();
    }

    /**
     * 执行给定的不返回结果的 SQL 语句，其中变量用?表示。防止 SQL 溢出攻击。<br/>
     * 若此语句没有执行成功，则抛出异常。
     * @link http://php.net/manual/en/mysqli-stmt.bind-param.php
     * @param string $query <p>
     * The query string.
     * </p>
     * @param string $types <p>
     * A string that contains one or more characters which specify the types
     * for the corresponding bind variables:
     * <table>
     * Type specification chars
     * <tr valign="top">
     * <td>Character</td>
     * <td>Description</td>
     * </tr>
     * <tr valign="top">
     * <td>i</td>
     * <td>corresponding variable has type integer</td>
     * </tr>
     * <tr valign="top">
     * <td>d</td>
     * <td>corresponding variable has type double</td>
     * </tr>
     * <tr valign="top">
     * <td>s</td>
     * <td>corresponding variable has type string</td>
     * </tr>
     * <tr valign="top">
     * <td>b</td>
     * <td>corresponding variable is a blob and will be sent in packets</td>
     * </tr>
     * </table>
     * </p>
     * @param mixed $var1 <p>
     * The number of variables and length of string
     * types must match the parameters in the statement.
     * </p>
     * @param mixed $_ [optional]
     * @throws \Exception
     */
    public function prepare_bind_execute(string $query, string $types, &$var1, &...$_): void
    {
        //注意可变参数的传递的写法

        $stmt = $this->_conn->prepare($query);
        if ($stmt === false) throw new \Exception("数据库查询失败。检查语句拼写。");
        $stmt->bind_param($types, $var1, ...$_);
        $stmt->execute();
        if ($stmt->errno) throw new \Exception("数据库查询失败。{$stmt->errno}: {$stmt->error}");
        $stmt->close();
    }

    /**
     * 给出要执行的不返回结果的 SQL 语句。不防范 SQL 溢出攻击，因此只应用于常量 SQL 语句。<br/>
     * 若此语句没有执行成功，则抛出异常。
     * @param string $query
     * @throws \Exception
     */
    public function prepare_no_bind_execute(string $query): void
    {
        $stmt = $this->_conn->prepare($query);
        if ($stmt === false) throw new \Exception("数据库查询失败。检查语句拼写。");
        $stmt->execute();
        if ($stmt->errno) throw new \Exception("数据库查询失败。{$stmt->errno}: {$stmt->error}");
        $stmt->close();
    }

    /**
     * 给出要执行的返回结果的 SQL 语句。不防范 SQL 溢出攻击，因此只应用于常量 SQL 语句。<br/>
     * 若此语句没有执行成功，则抛出异常。<br/>
     * 返回一个 mysqli_result 对象。
     * @param string $query
     * @return \mysqli_result
     * @throws \Exception
     */
    public function prepare_no_bind_query(string $query)
    {
        $stmt = $this->_conn->prepare($query);
        if ($stmt === false) throw new \Exception("数据库查询失败。检查语句拼写。");
        $stmt->execute();
        if ($stmt->errno) throw new \Exception("数据库查询失败。{$stmt->errno}: {$stmt->error}");
        $result = $stmt->get_result();
        if ($result === false) throw new \Exception("数据库查询失败。语句可能不返回结果。");
        $stmt->close();

        return $result;
    }

    /**
     * 执行给定的返回结果的 SQL 语句，其中变量用?表示。防止 SQL 溢出攻击。<br/>
     * 若此语句没有执行成功，则抛出异常。<br/>
     * 返回一个 mysqli_result 对象。
     * @link http://php.net/manual/en/mysqli-stmt.bind-param.php
     * @param string $query <p>
     * The query string.
     * </p>
     * @param string $types <p>
     * A string that contains one or more characters which specify the types
     * for the corresponding bind variables:
     * <table>
     * Type specification chars
     * <tr valign="top">
     * <td>Character</td>
     * <td>Description</td>
     * </tr>
     * <tr valign="top">
     * <td>i</td>
     * <td>corresponding variable has type integer</td>
     * </tr>
     * <tr valign="top">
     * <td>d</td>
     * <td>corresponding variable has type double</td>
     * </tr>
     * <tr valign="top">
     * <td>s</td>
     * <td>corresponding variable has type string</td>
     * </tr>
     * <tr valign="top">
     * <td>b</td>
     * <td>corresponding variable is a blob and will be sent in packets</td>
     * </tr>
     * </table>
     * </p>
     * @param mixed $var1 <p>
     * The number of variables and length of string
     * types must match the parameters in the statement.
     * </p>
     * @param mixed $_ [optional]
     * @return \mysqli_result
     * @throws \Exception
     */
    public function prepare_bind_query(string $query, string $types, &$var1, &...$_)
    {
        $stmt = $this->_conn->prepare($query);
        if ($stmt === false) throw new \Exception("数据库查询失败。检查语句拼写。");
        $stmt->bind_param($types, $var1, ...$_);
        $stmt->execute();
        if ($stmt->errno) throw new \Exception("数据库查询失败。{$stmt->errno}: {$stmt->error}");
        $result = $stmt->get_result();
        if ($result === false) throw new \Exception("数据库查询失败。语句可能不返回结果。");
        $stmt->close();

        return $result;
    }

    /**
     * Returns the auto generated id used in the last query
     * @link http://php.net/manual/en/mysqli.insert-id.php
     * @return int|string The value of the AUTO_INCREMENT field that was updated by the previous query. Returns zero if there was no previous query on the connection or if the query did not update an AUTO_INCREMENT value.
     * If the number is greater than maximal int value, mysqli_insert_id() will return a string.
     */
    public function last_insert_id()
    {
        return $this->_conn->insert_id;
    }
}