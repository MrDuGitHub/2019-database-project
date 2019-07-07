<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 20:24
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_condition(isset($_GET['action']));
Session::require_condition($_GET['action'] === 'ban' || $_GET['action'] === 'unban');

$target_uid = intval($_GET['uid'] ?? 0);
Session::require_condition($target_uid !== 0);

try {
    Session::require_is_able_to_manage_user_level_2($target_uid);
    $target_ban = ($_GET['action'] === 'ban') ? 1 : 0;


} catch (\Exception $e) {
    Template::panic($e);
    die();
}


?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>

    <script>
        $(document).ready(function () {
            $('#button-div').hide();
            $.post('/api/account/update-is-banned.php',<?=json_encode(json_encode(array("uid" => $target_uid, "ban" => $target_ban)))?>, function (result) {
                let type = result.result;
                if (type === 'failure') type = 'danger';

                $('#alert-box').removeClass('alert-info').addClass('alert-' + type).text(result.message);
                $('#button-div').show();
            }, "json");
        });
    </script>
    <title>封禁解禁</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="page-header">
                <h1>封禁解禁</h1>
            </div>
            <hr>
            <div class="alert alert-info" id="alert-box">
                请稍等…
            </div>
        </div>
    </div>
</div>

<?php
Template::write_standard_footer();
?>
</body>
</html>
