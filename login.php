<?php

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
if (Session::is_logged_in()) {
    Template::die_302('/index.php');
}

assert(PHP_INT_SIZE >= 8, '必须使用 64 位 PHP。');

$target_uid = intval($_POST['uid'] ?? 0);

if ($target_uid !== 0){
    try {
        $is_succeed = 1;
        $message = '';

        $user = new User($target_uid);
        if (!$user->is_existed()) {
            $is_succeed = 0;
            $message = '用户 ' . $target_uid . ' 不存在。';
        } elseif ($user->is_banned()) {
            $is_succeed = 0;
            $message = '用户 ' . $target_uid . ' 已被禁止登录。';
        } elseif (!$user->is_password_matched($_POST['password'])) {
            $is_succeed = 0;
            $message = '密码错误。';
        }

        //记录日志
        $client_ip = Session::get_client_address();
        $ua = base64_encode($_SERVER['HTTP_USER_AGENT']);
        $conn = MySQL::get_instance();
        $conn->prepare_bind_execute('INSERT INTO log_login(uid,ip,result,login_time,ua) VALUES (?,?,?,UTC_TIMESTAMP(),?)',
            'isis', $target_uid, $client_ip, $is_succeed, $ua);

        //返回结果
        if ($is_succeed === 0) throw new \Exception('登录失败。' . $message);

        Session::load_session($target_uid);
        Template::die_302('/index.php');
    } catch (\Exception $e) {
        Template::write_alert_page('danger', '登录失败', '<p>' . htmlentities($e->getMessage()) . '</p>', '
<div class="pull-right">
    <button type="button" class="btn btn-primary" onclick="history.go(-1);">
        <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;返回
    </button>
    <button type="button" class="btn btn-danger" onclick="window.close();">
        <span class="glyphicon glyphicon-log-in"></span>&nbsp;关闭页面
    </button>
</div>');
    }

}else{
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head() ?>
    <title>登录 - <?= WEBSITE_TITLE ?></title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="page-header">
                <h1><?= WEBSITE_TITLE ?></h1>
            </div>

            <form action="login.php" method="post">
                <fieldset>
                    <legend>登录</legend>

                    <div class="form-group">
                        <label for="uid" class="control-label">账号</label>
                        <input type="text" class="form-control" id="uid" name="uid" maxlength="255"
                               autocomplete="username" required/>
                    </div>

                    <div class="form-group">
                        <label for="password" class="control-label">密码</label>
                        <input type="password" class="form-control" id="password" name="password"
                               autocomplete="current-password" maxlength="255" required/>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <span class="glyphicon glyphicon-log-in"></span>&nbsp;登录
                        </button>
                        <button type="button" class="btn btn-default"
                                onclick="window.location.href='/register.php'">
                            <span class="glyphicon glyphicon-user"></span>&nbsp;注册
                        </button>
                    </div>
                    <span class="help-block"></span>
                </fieldset>
            </form>
        </div>
    </div>
</div>


<?php
Template::write_standard_footer();
?>
</body>
</html>
<?php
}

