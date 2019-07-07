<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 11:47
 */

namespace orgName\xxSystem\Privilege;

interface IPrivilege
{
    /**
     * 获得用户友好的权限显示名称。
     * @return string
     */
    public static function get_privilege_name(): string;

    /**
     * 获得用于数据库的字段名称。
     * @return string
     */
    public static function get_key(): string;

    /**
     * 给定用户 UID，初始化对象。不检查用户是否存在。
     * @param int $uid
     * @throws \Exception
     */
    public function __construct(int $uid);

    /**
     * 为该用户设置新的权限。写入数据库。
     * @param bool $value
     * @throws \Exception
     */
    public function set_value(bool $value): void;

    /**
     * 得到该用户的权限。不一定从数据库中读取。
     * @return bool
     * @throws \Exception
     */
    public function get_value(): bool;

}