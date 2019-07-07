<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 23:55
 */
namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
Session::require_non_ie();

$target_uid = intval($_GET['uid'] ?? 0);
Session::require_condition($target_uid !== 0);
/*
 * 此页面是管理员管理其他用户的页面。也可管理自己。
 */

try {
    Session::require_is_able_to_manage_user_level_2($target_uid);
    $user = new User($target_uid);
    $target_is_banned = $user->is_banned();
    $target_name = $user->get_name();
    $target_email = $user->get_email();
    $target_is_student = $user->is_student();
    $target_is_root_admin = (new Privilege\IsRootAdmin($target_uid))->get_value();
    $target_is_student_admin = (new Privilege\IsStudentAdmin($target_uid))->get_value();
    $target_is_teacher_admin = (new Privilege\IsTeacherAdmin($target_uid))->get_value();
} catch (\Exception $e) {
    Template::panic($e);
    die();
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head() ?>
    <title>用户管理 - <?= $target_uid ?></title>
</head>
<body>
<?php
Template::write_navbar_div();
?>
<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>用户管理
                <small><?= $target_uid ?></small>
            </h1>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>属性</th>
                <th>值</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>账号</td>
                <td><?= $target_uid; ?></td>
            </tr>
            <tr>
                <td>姓名</td>
                <td><?= htmlentities($target_name); ?></td>
            </tr>

            <tr>
                <td>邮箱</td>
                <td><?php echo htmlentities(($target_email)); ?></td>
            </tr>
            <tr>
                <td>组</td>
                <td>
                    <?= ($target_is_student ? '学生&nbsp;' : '教师&nbsp;') ?>
                    <?= ($target_is_student_admin ? '学生管理员&nbsp;' : '') ?>
                    <?= ($target_is_teacher_admin ? '教师管理员&nbsp;' : '') ?>
                    <?= ($target_is_root_admin ? '超级管理员&nbsp;' : '') ?>
                    <?= ($target_is_banned ? '禁止登录&nbsp;' : '') ?>
                </td>
            </tr>
            <tr>
                <td>操作</td>
                <td>
                    <a class="btn btn-default" href="/account/logging.php?limit=10&uid=<?= $target_uid ?>"
                       target="_blank">
                        <span class="glyphicon glyphicon-list-alt"></span>&nbsp;登录日志
                    </a>

                    <a class="btn btn-default" href="/account/reset-password.php?uid=<?= $target_uid ?>"
                       target="_blank">
                        <span class="glyphicon glyphicon-wrench"></span>&nbsp;重置密码
                    </a>

                    <a class="btn btn-default" href="/account/setting.php?uid=<?= $target_uid ?>"
                       target="_blank">
                        <span class="glyphicon glyphicon-wrench"></span>&nbsp;修改姓名或邮箱
                    </a>

                    <a class="btn btn-default"
                       href="/account/ban-unban.php?uid=<?= $target_uid ?>&action=<?= ($target_is_banned ? 'unban' : 'ban') ?>"
                       target="_blank">
                        <span class="glyphicon glyphicon-ban-circle"></span>&nbsp;<?= ($target_is_banned ? '解封用户' : '封禁用户') ?>
                    </a>

                    <?php
                    //TODO: 删除用户
                    try {
                        if (Session::is_able_to_manage_user_level_2($target_uid)) {
                            ?>
                            <a class="btn btn-default"
                               href="/account/privilege/index.php?uid=<?= $target_uid ?>"
                               target="_blank">
                                <span class="glyphicon glyphicon-tower"></span>&nbsp;权限管理
                            </a>
                            <?php

                        }
                    } catch (\Exception $e) {
                        Template::write_alert_div('danger', htmlentities($e->getMessage()));
                    }

                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
