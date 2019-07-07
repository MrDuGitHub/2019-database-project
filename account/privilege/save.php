<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/26
 * Time: 16:55
 */
namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_teacher_admin();

$target_uid = intval($_POST['uid'] ?? 0);
Session::require_condition($target_uid !== 0);

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>

    <script>
        $(document).ready(function () {
            $('#button-div').hide();
            $.post('/api/account/privilege/save.php',<?=json_encode(json_encode($_POST))?>, function (result) {
                let type = result.result;
                if (type === 'failure') type = 'danger';

                $('#alert-box').removeClass('alert-info').addClass('alert-' + type).text(result.message);
                $('#button-div').show();
            }, "json");
        });
    </script>
    <title>保存权限</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="page-header">
                <h1>保存权限</h1>
            </div>
            <div class="alert alert-info" id="alert-box">
                请稍等…
            </div>
            <div class="pull-right" id="button-div">
                <button type="button" class="btn btn-default"
                        onclick="window.location.href='/account/privilege/index.php?uid=<?= $target_uid ?>'">
                    <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;返回
                </button>
            </div>
        </div>
    </div>

</div>

<?php
Template::write_standard_footer();
?>
</body>
</html>
