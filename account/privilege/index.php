<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 11:36
 */
namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();

$target_uid = intval($_GET['uid'] ?? 0);
Session::require_condition($target_uid !== 0);
/*
 * 这个页面只对管理员有效。用处是展示target_uid的权限，可以修改。
 *
 * 此页面要求level_2权限：
 * 学生管理员有权管理学生的权限。
 * 教师管理员有权管理教师的权限。
 * 非管理员不能管理自己
 *
 * 特别地：
 * 三个特殊权限必须由 root 管理员进行设置：是否为 root 管理员；是否为学生管理员；是否为教师管理员
 *
 */

try {
    Session::require_is_able_to_manage_user_level_2($target_uid);

    //在/api/account/privilege/save.php中有类似的代码
    //获取权限
    $privileges = array();
    /*
    array_push($privileges, new Privilege\IsResearchTrainingAdmin($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingJudge($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingParticipant($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingAccessible($target_uid));
    */
    $user = new User($target_uid);


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
    <title>权限管理 - <?= $target_uid ?></title>
</head>
<body>

<?php Template::write_navbar_div() ?>
<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>权限管理
                <small><?= $target_uid ?></small>
            </h1>
        </div>
    </div>
    <form action="save.php" method="post" target="_self">
        <input type="hidden" name="uid" value="<?= $target_uid ?>">
        <div class="row">
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;保存
            </button>

            <br/>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>权限</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= htmlentities(Privilege\IsRootAdmin::get_privilege_name()) ?></td>
                    <td>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" autocomplete="off"
                                       checked> <?= ($target_is_root_admin ? '开' : '关') ?>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><?= htmlentities(Privilege\IsStudentAdmin::get_privilege_name()) ?></td>
                    <td>
                        <?php if (Session::is_root_admin()) {
                            ?>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?= ($target_is_student_admin ? ' active' : '') ?>">
                                    <input type="radio" name="<?= Privilege\IsStudentAdmin::get_key() ?>" value="1"
                                           autocomplete="off" <?= ($target_is_student_admin ? ' checked' : '') ?> > 开
                                </label>
                                <label class="btn btn-default <?= ($target_is_student_admin ? '' : ' active') ?>">
                                    <input type="radio" name="<?= Privilege\IsStudentAdmin::get_key() ?>" value="0"
                                           autocomplete="off" <?= ($target_is_student_admin ? '' : ' checked') ?> > 关
                                </label>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" autocomplete="off"
                                           checked> <?= ($target_is_student_admin ? '开' : '关') ?>
                                </label>
                            </div>
                            <?php

                        } ?>
                    </td>
                </tr>
                <tr>
                    <td><?= htmlentities(Privilege\IsTeacherAdmin::get_privilege_name()) ?></td>
                    <td>
                        <?php if (Session::is_root_admin()) {
                            ?>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?= ($target_is_teacher_admin ? ' active' : '') ?>">
                                    <input type="radio" name="<?= Privilege\IsTeacherAdmin::get_key() ?>" value="1"
                                           autocomplete="off" <?= ($target_is_teacher_admin ? ' checked' : '') ?> > 开
                                </label>
                                <label class="btn btn-default <?= ($target_is_teacher_admin ? '' : ' active') ?>">
                                    <input type="radio" name="<?= Privilege\IsTeacherAdmin::get_key() ?>" value="0"
                                           autocomplete="off" <?= ($target_is_teacher_admin ? '' : ' checked') ?> > 关
                                </label>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default active">
                                    <input type="radio" autocomplete="off"
                                           checked> <?= ($target_is_teacher_admin ? '开' : '关') ?>
                                </label>
                            </div>
                            <?php

                        } ?>
                    </td>
                </tr>
                <?php
                foreach ($privileges as $privilege) {
                    $value = $privilege->get_value();
                    ?>
                    <tr>
                        <td><?= htmlentities($privilege->get_privilege_name()) ?></td>
                        <td>
                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-default <?= ($value ? ' active' : '') ?>">
                                    <input type="radio" name="<?= $privilege->get_key() ?>" value="1"
                                           autocomplete="off" <?= ($value ? ' checked' : '') ?> > 开
                                </label>
                                <label class="btn btn-default <?= ($value ? '' : ' active') ?>">
                                    <input type="radio" name="<?= $privilege->get_key() ?>" value="0"
                                           autocomplete="off" <?= ($value ? '' : ' checked') ?> > 关
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>


        </div>
    </form>
</div>

<?php
Template::write_standard_footer();
?>
</body>
</html>
