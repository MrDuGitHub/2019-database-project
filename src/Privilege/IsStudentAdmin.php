<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 12:22
 */

namespace orgName\xxSystem\Privilege;


class IsStudentAdmin implements IPrivilege
{
    use SqlPrivilegeTrait;

    public static function get_privilege_name(): string
    {
        return '学生管理员';
    }

    public static function get_key(): string
    {
        return 'is_student_admin';
    }
}