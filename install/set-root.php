<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 15:27
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

//用完这段代码后记得把die()解除注释
//die();

if (($_GET['key'] ?? '') != 'JFI88VhLbg0uCyAfiyyV') {
    die();
}
$uid = 192168000000;

try {
    $user = new User($uid);

    if (!$user->is_existed()) {
        $v_uid = $uid;
        $v_password = '';
        $v_is_banned = 0;
        $v_email = 'mail@example.com';
        $v_name = 'ROOT 教师';
        $v_is_student = 0;
        //建立该用户
        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute(
            'INSERT INTO user(uid,password,is_banned,email,name,is_student) VALUES (?,?,?,?,?,?)',
            'isissi', $v_uid, $v_password, $v_is_banned, $v_email, $v_name, $v_is_student
        );
    }

    (new Privilege\IsRootAdmin($uid))->set_value(true);
    (new Privilege\IsStudentAdmin($uid))->set_value(true);
    (new Privilege\IsTeacherAdmin($uid))->set_value(true);
    (new User($uid))->set_password('025f41c16c808eea');


    $target_is_banned = $user->is_banned();
    $target_name = $user->get_name();
    $target_email = $user->get_email();
    $target_is_student = $user->is_student();
    $target_is_root_admin = (new Privilege\IsRootAdmin($uid))->get_value();
    $target_is_student_admin = (new Privilege\IsStudentAdmin($uid))->get_value();
    $target_is_teacher_admin = (new Privilege\IsTeacherAdmin($uid))->get_value();

    Template::write_alert_page('success', '成功', <<<EOF
UID：$uid

最高管理员：$target_is_root_admin

学生管理员：$target_is_student_admin

教师管理员：$target_is_teacher_admin
EOF
    );
} catch (\Exception $e) {
    Template::panic($e);
}