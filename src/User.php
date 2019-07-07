<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/26
 * Time: 22:20
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class User
{
    public $uid;

    /**
     * 给定 uid，返回 User 对象。<br/>
     * 注意，该方法不会抛出异常，即使用户不存在。<br/>
     * 使用 is_existed() 去检查用户是否存在。<br/>
     * 新建用户时也可利用 User 对象。<br/>
     * 但是新建用户的代码目前不在这个类中。<br/>
     * 我当初为什么写了这种逻辑？
     * @param int $uid
     */
    public function __construct(int $uid)
    {
        $this->uid = $uid;
    }

    /**
     * 为用户设置新的名称，并更新数据库。
     * @param string $name 要设置的名称。
     * @throws \Exception 当名称不合法时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function set_name(string $name): void
    {
        $name_field = new Field\NameField();
        $name_field->check_and_throw($name);

        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'UPDATE user SET name = ? WHERE uid = ? LIMIT 1',
            'si', $name, $this->uid
        );

    }

    /**
     * 返回用户的名称。
     * @return string
     * @throws \Exception 当用户不存在时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function get_name(): string
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            "SELECT name FROM user WHERE uid=? LIMIT 1",
            'i', $this->uid);
        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");

        $row = $result->fetch_assoc();

        return $row['name'] ?? '';
    }


    /**
     * 为用户设置新的密码，并更新数据库。
     * @param string $password 要设置的新密码。
     * @throws \Exception 当密码不合法时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function set_password(string $password): void
    {
        (new Field\PasswordField())->check_and_throw($password);

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'UPDATE user SET password=? WHERE uid=? LIMIT 1',
            'si', $hashed_password, $this->uid
        );

    }

    /**
     * 为用户设置新的邮箱地址，并更新数据库。
     * @param string $email 要设置的邮箱地址。
     * @throws \Exception 当邮箱地址不合法时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function set_email(string $email): void
    {
        $email_field = new Field\EmailField();
        $email_field->check_and_throw($email);

        $v = $email;
        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'UPDATE user SET email = ? WHERE uid = ? LIMIT 1',
            'si', $v, $this->uid
        );

    }

    /**
     * 返回该用户在数据库中是否存在。
     * @return bool
     * @throws \Exception 当 SQL 语句执行失败时抛出异常。
     */
    public function is_existed(): bool
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT is_banned FROM user WHERE uid=? LIMIT 1',
            'i', $this->uid
        );
        return ($result->num_rows !== 0);
    }

    /**
     * 返回该用户是否已被禁止登录。
     * @return bool
     * @throws \Exception 当用户不存在时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function is_banned(): bool
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT is_banned FROM user WHERE uid=? LIMIT 1',
            'i', $this->uid
        );

        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");
        $row = $result->fetch_assoc();
        return $row['is_banned'] != 0;
    }

    /**
     * 设置用户是否被封禁
     * @param bool $value
     * @throws \Exception
     */
    public function set_is_banned(bool $value): void
    {
        $v = ($value ? 1 : 0);
        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'UPDATE user SET is_banned = ? WHERE uid=? LIMIT 1',
            'ii', $v, $this->uid
        );
    }

    /**
     * 设置用户是否为游客
     * @param bool $value
     * @throws \Exception
     */
    public function set_is_visitor(bool $value): void
    {
        $v = ($value ? 1 : 0);
        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'UPDATE user SET is_visitor = ? WHERE uid=? LIMIT 1',
            'ii', $v, $this->uid
        );
    }

    /**
     * 给定一个明文的密码，检查是否正确。<br/>
     * 密码并未在数据库中明文存储，几乎不可能从数据库中逆推出原密码。
     * @param string $password 要检查的密码。
     * @return bool 密码是否匹配。
     * @throws \Exception 当用户不存在时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function is_password_matched(string $password): bool
    {
        //取得单向加密后的密码
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT password FROM user WHERE uid=? LIMIT 1',
            'i', $this->uid
        );

        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");
        $row = $result->fetch_assoc();

        return password_verify($password, $row['password']);
    }


    /**
     * 返回用户的邮件地址。
     * @return string
     * @throws \Exception 当用户不存在时抛出异常。<br/>当 SQL 语句执行失败时抛出异常。
     */
    public function get_email(): string
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            "SELECT email FROM user WHERE uid=? LIMIT 1",
            'i', $this->uid);
        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");

        $row = $result->fetch_assoc();

        return $row['email'] ?? '';
    }

    /**
     * 如果用户是游客，返回True。
     * @return bool
     * @throws \Exception
     */
    public function is_visitor(): bool
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT is_visitor FROM user WHERE uid = ? LIMIT 1',
            'i', $this->uid
        );
        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");
        return ($result->fetch_row()[0] == 1);
    }

    /**
     * 如果用户是学生，返回True。
     * @return bool
     * @throws \Exception
     */
    public function is_student(): bool
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT is_student FROM user WHERE uid = ? LIMIT 1',
            'i', $this->uid
        );
        if ($result->num_rows === 0) throw new \Exception("用户 {$this->uid} 不存在。");
        return ($result->fetch_row()[0] == 1);
    }

    /**
     * 如果用户是教师，返回True。
     * @return bool
     * @throws \Exception
     */
    public function is_teacher(): bool
    {
        return !$this->is_student();
    }

    /**
     * 从数据库中删除此用户。将函数参数置为'confirmed'以确认此操作。
     * @param string $confirmed
     * @throws \Exception
     */
    public function delete_user(string $confirmed): void
    {

        if ($confirmed === 'confirmed') {
            $conn = MySQL::get_instance();

            //从user表中删除此用户
            $conn->prepare_bind_execute(
                'DELETE FROM user WHERE uid = ? LIMIT 1',
                'i', $this->uid
            );
            //从user_privilege表中删除用户的所有权限
            $conn->prepare_bind_execute(
                'DELETE FROM user_privilege WHERE uid = ?',
                'i', $this->uid
            );
        }


    }

    /**
     * @return User[]
     * @throws \Exception
     */
    public static function get_all_students()
    {
        $conn = MySQL::get_instance();

        $result = $conn->prepare_no_bind_query(
            'SELECT uid FROM user WHERE is_student = 1'
        );

        $ret = array();

        while ($row = $result->fetch_assoc()) {
            $uid = intval($row['uid']);
            $ret[$uid] = new User($uid);
        }

        return $ret;
    }

    /**
     * @return User[]
     * @throws \Exception
     */
    public static function get_all_teachers()
    {
        $conn = MySQL::get_instance();

        $result = $conn->prepare_no_bind_query(
            'SELECT uid FROM user WHERE is_student = 0'
        );

        $ret = array();

        while ($row = $result->fetch_assoc()) {
            $uid = intval($row['uid']);
            $ret[$uid] = new User($uid);
        }

        return $ret;
    }
}