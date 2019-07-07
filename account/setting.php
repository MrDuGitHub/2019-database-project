<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 22:13
 */
namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_logged_in();

try {
    $target_uid = intval($_GET['uid'] ?? 0);
    Session::require_condition($target_uid !== 0);

    Session::require_is_able_to_manage_user_level_1($target_uid);

    $target_user = new User($target_uid);
    $target_email = $target_user->get_email();
    $target_name = $target_user->get_name();

} catch (\Exception $e) {
    Template::write_alert_page('danger', '错误', htmlentities($e->getMessage()));
    die();
}

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head() ?>
    <title>账户设置 - <?= $target_uid ?></title>
    <script>
        $(document).ready(function () {
            $('#update-password-button').click(function () {
                let password = $('#password').val();
                let uid = <?= $target_uid ?>;
                $('#update-password-button').hide();
                $('#alert-box').show();
                $.post('/api/account/update-password.php', JSON.stringify({
                    uid: uid,
                    password: password
                }), function (result) {
                    let type = result.result;
                    if (type === 'failure') type = 'danger';

                    $('#alert-box').removeClass('alert-info').removeClass('alert-success').removeClass('alert-danger').addClass('alert-' + type).text(result.message);
                    $('#update-password-button').show();
                }, "json");
            });

            $('#update-email-button').click(function () {
                let email = $('#email').val();
                let uid = <?= $target_uid ?>;
                $('#update-email-button').hide();
                $('#alert-box').show();
                $.post('/api/account/update-email.php', JSON.stringify({uid: uid, email: email}), function (result) {
                    let type = result.result;
                    if (type === 'failure') type = 'danger';

                    $('#alert-box').removeClass('alert-info').removeClass('alert-success').removeClass('alert-danger').addClass('alert-' + type).text(result.message);
                    $('#update-email-button').show();
                }, "json");
            });

            $('#update-name-button').click(function () {
                let name = $('#name').val();
                let uid = <?= $target_uid ?>;
                $('#update-name-button').hide();
                $('#alert-box').show();
                $.post('/api/account/update-name.php', JSON.stringify({uid: uid, name: name}), function (result) {
                    let type = result.result;
                    if (type === 'failure') type = 'danger';

                    $('#alert-box').removeClass('alert-info').removeClass('alert-success').removeClass('alert-danger').addClass('alert-' + type).text(result.message);
                    $('#update-name-button').show();
                }, "json");
            });
        });
    </script>
</head>
<body>
<?php Template::write_navbar_div() ?>

<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>账户设置
                <small><?= $target_uid ?></small>
            </h1>
        </div>
    </div>
    <div class="row">
        <div class="form-inline">
            <div class="form-group col-xs-12 col-sm-6 col-lg-4">

                <label for="name" class="control-label">姓名</label>
                <input type="text" maxlength="20" class="form-control" id="name" name="name"
                       value="<?= htmlentities($target_name) ?>" required/>
                <button type="button" id="update-name-button" class="btn btn-default"><span
                            class="glyphicon glyphicon-floppy-disk"></span>&nbsp;保存
                </button>

                <!--<span class="help-block"></span>-->
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group col-xs-12 col-sm-6 col-lg-4">

                <label for="email" class="control-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="254"
                       pattern="<?= Field\EmailField::EMAIL_PREG_HTML ?>"
                       value="<?= htmlentities($target_email) ?>" required/>
                <button type="button" id="update-email-button" class="btn btn-default"><span
                            class="glyphicon glyphicon-floppy-disk"></span>&nbsp;保存
                </button>

                <span class="help-block">建议不要填写 Gmail</span>
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group col-xs-12 col-sm-12 col-lg-4">
                <label for="password" class="control-label">密码</label>
                <input type="password" id="password" name="password" maxlength="<?= MAXIMUM_PASSWORD_LENGTH ?>"
                       minlength="<?= MINIMUM_PASSWORD_LENGTH ?>" autocomplete="new-password" class="form-control"
                       required/>
                <button type="button" id="update-password-button" class="btn btn-default"><span
                            class="glyphicon glyphicon-floppy-disk"></span>&nbsp;保存
                </button>

                <span class="help-block">
                    <?= MINIMUM_PASSWORD_LENGTH ?>-<?= MAXIMUM_PASSWORD_LENGTH ?> 个字符，至少包括字母、数字、符号中的两种
                </span>
            </div>
        </div>


    </div>
    <div class="row">

        <div class="alert alert-info" id="alert-box" style="display: none;">
            请稍等…
        </div>
    </div>


</div>


<?php
Template::write_standard_footer();
?>
</body>
</html>