<?php

/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 12:28
 */

namespace orgName\xxSystem\Privilege;

use orgName\xxSystem\MySQL;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

trait SqlPrivilegeTrait
{
    public $uid;
    public $value;
    private $value_exists;

    /**
     * 给定用户 UID，初始化对象。不检查用户是否存在。
     * @param int $uid
     * @throws \Exception
     */
    public function __construct(int $uid)
    {
        $this->uid = $uid;

        //从数据库中读取
        $k = $this->get_key();
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT privilege_value FROM user_privilege WHERE uid = ? AND privilege_name = ? LIMIT 1',
            'is', $uid, $k
        );

        //如果不存在，置为0
        if ($result->num_rows === 0) {
            $this->value = 0;
            $this->value_exists = false;
        } else {
            //忽略对象类型的比较
            $this->value = ($result->fetch_array()[0] == 1);
            $this->value_exists = true;
        }

    }

    /**
     * 为该用户设置新的权限。写入数据库。
     * @param bool $value
     * @throws \Exception
     */
    public function set_value(bool $value): void
    {
        $k = $this->get_key();
        $v = ($value == true) ? 1 : 0;
        $conn = MySQL::get_instance();
        if ($this->value_exists) {
            $conn->prepare_bind_execute(
                'UPDATE user_privilege SET privilege_value = ? WHERE uid = ? AND privilege_name = ?',
                'iis', $v, $this->uid, $k
            );
        } else {
            $conn->prepare_bind_execute(
                'INSERT INTO user_privilege (uid, privilege_name, privilege_value) VALUES (?,?,?)',
                'isi', $this->uid, $k, $v
            );
        }

    }

    /**
     * 得到该用户的权限。不一定从数据库中读取。
     * @return bool
     * @throws \Exception
     */
    public function get_value(): bool
    {
        return $this->value;
    }

}