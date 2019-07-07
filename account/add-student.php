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
Session::require_student_admin();

$target_uid = intval($_POST['uid'] ?? 0);
Session::require_condition($target_uid !== 0);

$target_email = $_POST['email'] ?? '';
$target_name = $_POST['name'] ?? '';

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>

    <script>
        $(document).ready(function () {
            $('#button-div').hide();
            $.post('/api/account/student/add-student.php',<?=json_encode(json_encode(array("uid" => $target_uid, "email" => $target_email, "name" => $target_name)))?>, function (result) {
                let type = result.result;
                if (type === 'failure') type = 'danger';

                $('#alert-box').removeClass('alert-info').addClass('alert-' + type).text(result.message);
                $('#button-div').show();
            }, "json");
        });
    </script>
    <title>添加学生</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="page-header">
                <h1>添加学生</h1>
            </div>
            <div class="alert alert-info" id="alert-box">
                请稍等…
            </div>
            <div class="pull-right" id="button-div">
                <button type="button" class="btn btn-default" onclick="window.location.href='/student/list/index.php'">
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
