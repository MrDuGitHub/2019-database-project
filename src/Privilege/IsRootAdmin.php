<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 12:55
 */

namespace orgName\xxSystem\Privilege;


class IsRootAdmin implements IPrivilege
{
    use SqlPrivilegeTrait;

    public static function get_privilege_name(): string
    {
        return '最高管理员';
    }

    public static function get_key(): string
    {
        return 'is_root_admin';
    }
}