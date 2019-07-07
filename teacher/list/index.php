<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 22:53
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_teacher_admin();

try {
    $conn = MySQL::get_instance();

    $result = $conn->prepare_no_bind_query(
        'SELECT uid,email,name FROM user WHERE is_student = 0'
    );

} catch (\Exception $e) {
    Template::panic($e);
    die();
}

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>
    <title>教师管理</title>
    <script>
        $(document).ready(function () {
            $('#nav-item-list_teachers').addClass('active');
        });
    </script>
</head>
<body>

<?php
Template::write_navbar_div();
?>

<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>教师管理</h1>
        </div>
    </div>
    <div class="row">
        <button type="button" class="btn btn-default" onclick="window.location.href='batch-add.php'">
            <span class="glyphicon glyphicon-plus"></span>&nbsp;批量添加
        </button>
    </div>
    <br/>
    <div class="row">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>工号</th>
                <th>姓名</th>
                <th>E-mail</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <form method="post" target="_self" action="/account/add-teacher.php" class="form-inline">

                <!--suppress HtmlUnknownTag -->
                <tr>
                    <td>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input type="text" class="form-control" id="uid" name="uid" pattern="\d*" required
                               minlength="6" maxlength="16"/>
                    </td>
                    <td>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input type="text" maxlength="20" class="form-control" id="name" name="name" required/>
                    </td>
                    <td>
                        <input type="email" class="form-control" id="email" name="email" maxlength="254"
                               pattern="<?= Field\EmailField::EMAIL_PREG_HTML ?>" placeholder="选填"/>
                    </td>
                    <td>
                        <button class="btn btn-default" type="submit">
                            <span class="glyphicon glyphicon-plus"></span>&nbsp;添加用户
                        </button>
                    </td>
                </tr>
            </form>
            <?php

            if ($result->num_rows === 0) {
                ?>
                <tr>
                    <td>无结果。</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
            } else {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?= intval($row['uid']) ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><a href="/account/manage.php?uid=<?= intval($row['uid']) ?>"
                               target="_blank"><span class="glyphicon glyphicon-user"></span>&nbsp;管理</a></td>
                    </tr>
                    <?php
                }
            }

            ?>

            </tbody>
        </table>

    </div>
</div>


<?php
Template::write_standard_footer();
?>
</body>
</html>
