<?php
namespace orgName\xxSystem;
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
Session::require_non_ie();
Session::require_logged_in();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head() ?>
    <title><?= WEBSITE_TITLE ?></title>
    <script>
        $(document).ready(function () {
            $('#nav-item-homepage').addClass('active');
        });
    </script>
    <link type="text/css" href="/css/index.css" rel="stylesheet">
</head>
<body>

<?php
Template::write_navbar_div();
?>
<div class="container">
    <div class="jumbotron">
        <h1 class="text-center">SQL交互式在线学习系统</h1>
        <br/>
        <div class="row">
            <div class="text-center">
                <a href="/problem/index.php" class="btn btn-primary btn-lg">进入题库</a>
                <a href="/problem/exercise/index.php" class="btn btn-success btn-lg">课堂练习</a>
                <a href="/problem/ranklist/index.php" class="btn btn-info btn-lg">学生排名</a>
            </div>
        </div>
    </div>
</div>

<?php
Template::write_standard_footer();
?>
</body>
</html>