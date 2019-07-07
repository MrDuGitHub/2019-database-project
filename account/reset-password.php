<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/19
 * Time: 21:08
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
try {

    Session::require_non_ie();

    $target_uid = intval($_GET['uid'] ?? 0);
    Session::require_condition($target_uid !== 0);

    Session::require_is_able_to_manage_user_level_2($target_uid);

    if (($_GET['confirmed'] ?? '') !== 'true') {
        Template::write_alert_page('info', '重置密码', '将要重置用户 ' . $target_uid . ' 的密码。确定吗？',
            '<div class="pull-right" id="buttons">
    <button type="button" class="btn btn-primary" id="action" onclick="window.location.href=\'reset-password.php?uid=' . $target_uid . '&confirmed=true\';$(\'#buttons\').hide();">
        <span class="glyphicon glyphicon-check"></span>&nbsp;确定
    </button>
    <button type="button" class="btn btn-danger" onclick="window.close();">
        <span class="glyphicon glyphicon-remove"></span>&nbsp;取消
    </button>
</div>');
    } else {
        //生成并设置随机密码
        $password = (new Field\PasswordField())->get_random_password();

        $user = new User($target_uid);
        $user->set_password($password);

        Template::write_alert_page('success', '重置成功', '用户 ' . $target_uid . ' 的新密码是 ' . htmlentities($password) . '。请告知用户新的密码。');

        /*
        try {
            $email = SMTP::get_user_email($target_uid);
            $date_str = gmdate(DATE_ISO8601);
            SMTP::send_html_to_address($email, '重置密码 - 泰山学堂学生信息管理系统',
                '<p>您的登录密码已被管理员重置。新的登录密码是 <i>' . htmlentities($password) . '</i>，共 ' . strlen($password) . ' 个字符。</p>
<p>若要更新个人资料、修改密码或进行公共查询，请<a href="' . HOMEPAGE_URI . '">登录</a>泰山学堂学生信息管理系统。如有任何疑问，可以咨询辅导员老师。</p>
<hr/>
<p style="font-size: smaller;">发送时间 ' . $date_str . '</p>',
                '您的登录密码已被管理员重置。新的登录密码是 ' . $password . '，共' . strlen($password) . '个字符。' . "\n" .
                '若要更新个人资料、修改密码或进行公共查询，请登录泰山学堂学生信息管理系统。如有任何疑问，可以咨询辅导员老师。' . "\n" .
                '登录地址: ' . HOMEPAGE_URI . "\n" .
                '发送时间 ' . $date_str);

            Template::write_alert_page('success', '重置成功', '用户 ' . $target_uid . ' 的新密码是 ' . htmlentities($password) . '。已将密码发至邮箱 ' . htmlentities($email) . '。');
        } catch (\Exception $e) {
            Template::write_alert_page('warning', '重置成功', '用户 ' . $target_uid . ' 的新密码是 ' . htmlentities($password) . '。但是发送密码至邮箱 ' . htmlentities($email ?? '') . ' 失败，因为' . htmlentities($e->getMessage()) . ' 请手动告知用户新的密码，并将问题向网站管理员反馈。');
        }
*/

    }
} catch (\Exception $e) {
    Template::panic($e);
}