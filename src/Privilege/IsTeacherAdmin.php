<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 12:57
 */

namespace orgName\xxSystem\Privilege;


class IsTeacherAdmin implements IPrivilege
{
    use SqlPrivilegeTrait;

    public static function get_privilege_name(): string
    {
        return '教师管理员';
    }

    public static function get_key(): string
    {
        return 'is_teacher_admin';
    }
}