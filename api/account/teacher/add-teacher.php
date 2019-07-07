<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/5/20
 * Time: 0:45
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
error_reporting(E_ERROR);
try {
    //检查权限
    Session::require_teacher_admin();
    //接收数据
    $raw_data = file_get_contents('php://input');
    $input = json_decode($raw_data, true, 2);
    Session::require_condition($input !== null);
    //获得uid
    $target_uid = intval($input['uid'] ?? 0);
    Session::require_condition($target_uid !== 0);
    //获得value
    $target_email = $input['email'] ?? '';
    $target_name = $input['name'] ?? '';
    //准备新建教师
    $email_field = new Field\EmailField();
    $name_field = new Field\NameField();

    //email允许为空
    if (!empty($target_email)) {
        $email_field->check_and_throw($target_email);
    }


    $name_field->check_and_throw($target_name);

    $v_password = '';
    $v_email = $target_email;
    $v_name = $target_name;
    $v_is_student = 0;
    $v_is_banned = 0;
    $v_uid = $target_uid;

    //建立该用户
    $conn = MySQL::get_instance();
    $conn->prepare_bind_execute(
        'INSERT INTO user(uid,password,is_banned,email,name,is_student) VALUES (?,?,?,?,?,?)',
        'isissi', $v_uid, $v_password, $v_is_banned, $v_email, $v_name, $v_is_student
    );

    $user = new User($target_uid);

    //生成并设置随机密码
    $password = (new Field\PasswordField())->get_random_password();
    $user->set_password($password);

    $message = '用户 ' . $target_uid . ' 的密码是 ' . $password . '。请告知用户初始密码。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
    /*
    //发邮件通知
    try {
        $email = $user->get_email();
        $date_str = gmdate(DATE_ISO8601);
        SMTP::send_html_to_address($email, '注册成功 - 泰山学堂学生信息管理系统',
            '<p>您的初始登录密码是 <i>' . htmlentities($password) . '</i>，共 ' . strlen($password) . ' 个字符。</p>
<p>由于 E-mail 本身是明文投递的，出于安全考虑，建议您修改密码。</p>
<br/>
<p>若要更新个人资料、修改密码或进行公共查询，请<a href="' . HOMEPAGE_URI . '">登录</a>泰山学堂学生信息管理系统。如有任何疑问，可以咨询辅导员老师。</p>
<hr/>
<p style="font-size: smaller;">发送时间 ' . $date_str . '</p>',
            '您的初始登录密码是 ' . $password . '，共' . $password . '个字符。' . "\n" .
            '由于 E-mail 本身是明文投递的，出于安全考虑，建议您修改密码。' . "\n" . "\n" .
            '若要更新个人资料、修改密码或进行公共查询，请登录泰山学堂学生信息管理系统。如有任何疑问，可以咨询辅导员老师。' . "\n" .
            '登录地址: ' . HOMEPAGE_URI . "\n" .
            '发送时间 ' . $date_str);

        $message = '用户 ' . $target_uid . ' 的密码是 ' . $password . '。已将密码发至邮箱 ' . $email . '。';
        Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
    } catch (\Exception $e) {
        $message = '用户 ' . $target_uid . ' 的密码是 ' . $password . '。但是发送密码至邮箱 ' . ($email ?? '') . ' 失败，因为' . $e->getMessage() . '。请亲自告知初始密码，并将问题向网站管理员反馈。';
        Template::die_json(json_encode(array('result' => 'warning', 'message' => $message)));
    }*/

} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}