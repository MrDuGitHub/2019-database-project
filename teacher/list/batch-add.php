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

$upload = $_GET['upload'] ?? '' === 'excel';
if ($upload) Session::require_condition(($_FILES['file']['error'] ?? 1) === 0);

?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head(); ?>
    <title>教师管理 - 批量添加</title>
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
            <h1>教师管理 - 批量添加</h1>
        </div>
    </div>

    <?php
    if ($upload) {
    try {
        $input_file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($_FILES['file']['tmp_name']);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($input_file_type);
        //$reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);

        //从最开头5行内找到同一行内的"工号"/"学号"/"学工号"和“姓名”和“邮箱”/E-mail/Email三列
        $uid_col_id = 0;
        $name_col_id = 0;
        $email_col_id = 0;

        for ($row_id = 1; $row_id <= 5; ++$row_id) {
            for ($col_id = 1; ; ++$col_id) {
                $cell_value = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($col_id, $row_id)->getValue();

                if ($cell_value === null) break;
                if ($uid_col_id === 0) {
                    if ($cell_value === '工号' || $cell_value === '学号' || $cell_value === '学工号') {
                        $uid_col_id = $col_id;
                        continue;
                    }
                }
                if ($name_col_id === 0) {
                    if ($cell_value === '姓名') {
                        $name_col_id = $col_id;
                        continue;
                    }
                }
                if ($email_col_id === 0) {
                    if ($cell_value === '邮箱' || $cell_value === 'E-mail' || $cell_value === 'Email') {
                        $email_col_id = $col_id;
                        continue;
                    }
                }

            }

            //邮箱可选
            if ($name_col_id === 0 || $uid_col_id === 0 /*|| $email_col_id === 0*/) {
                $uid_col_id = 0;
                $name_col_id = 0;
                $email_col_id = 0;
            } else {
                break;
            }

        }

        //邮箱可选
        if ($name_col_id === 0 || $uid_col_id === 0 /*|| $email_col_id === 0*/) {
            throw new \Exception('没有找到名为工号、姓名和邮箱的表头。');
        }

        $emails = array();//uid=>email
        $names = array();
        $to_delete = array();
        //var_dump($row_id);
        for (++$row_id; ; ++$row_id) {
            $target_uid = intval($spreadsheet->getActiveSheet()->getCellByColumnAndRow($uid_col_id, $row_id)->getValue() ?? 0);
            if ($target_uid === 0) break;
            try {
                $names[$target_uid] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($name_col_id, $row_id)->getValue() ?? '';
                //邮箱可选
                if ($email_col_id !== 0) {
                    $emails[$target_uid] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($email_col_id, $row_id)->getValue() ?? '';
                }
            } catch (\Exception $e) {
                throw new \Exception('解析第 ' . $row_id . ' 行时出现如下错误：' . $e->getMessage());
            }
        }
        unset($target_uid);
        unset($col_id);
        unset($row_id);

        if (count($names) === 0) throw new \Exception('没有找到一条有效的数据。');


        ?>
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
                <?php
                $conn = MySQL::get_instance();

                foreach ($names as $target_uid => $target_name) {
                    assert(isset($emails[$target_uid]));

                    $result = $conn->prepare_bind_query('SELECT uid FROM user WHERE uid = ?',
                        'i', $target_uid);
                    $exists = $result->num_rows === 1;

                    ?>
                    <tr <?= ($exists ? 'class="danger"' : '') ?>>
                        <td><?= $target_uid ?></td>
                        <td><?= htmlentities($target_name) ?></td>
                        <td><?= htmlentities($emails[$target_uid] ?? '') ?></td>
                        <td><?= ($exists ? '账户已存在，忽略' : '即将创建') ?></td>
                    </tr>

                    <?php
                    if ($exists) {
                        array_push($to_delete, $target_uid);
                    }
                }
                foreach ($to_delete as $target_uid) {
                    unset($emails[$target_uid]);
                    unset($names[$target_uid]);
                }

                ?>

                </tbody>
            </table>
        </div>
    <br/>
        <div class="row" id="button-div">
            <button type="button" class="btn btn-default" onclick="window.location.href='/teacher/list/index.php'">
                <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;返回
            </button>
            <button type="button" class="btn btn-primary" id="confirm-button">
                <span class="glyphicon glyphicon-check"></span>&nbsp;确认
            </button>
        </div>
        <div id="results">
        </div>
        <script>
            $(document).ready(function () {
                $('#confirm-button').click(
                    function () {
                        $('#button-div').hide();

                        let names = <?=json_encode($names)?>;
                        let emails = <?=json_encode($emails)?>;

                        let i = 0;
                        for (let uid in names) {
                            add_teacher_later(uid, emails[uid], names[uid], i * 1000);
                            ++i;
                        }
                    });
            });

            function add_teacher_later(uid, email, name, delay = 0) {
                setTimeout(function () {
                    $.post('/api/account/teacher/add-teacher.php', JSON.stringify({
                        'uid': uid,
                        'email': email,
                        'name': name
                    }), function (result) {
                        let type = result['result'];
                        if (type === 'failure') type = 'danger';

                        $('#results').append('<div class="row"><div class="alert alert-' + type + '">' + _.escape(result['message']) + '</div>');

                    }, 'json');
                }, delay);
            }
        </script>
    <?php
    } catch (\Exception $e) {
    ?>
        <div class="row"><?php
        Template::write_alert_div('danger', htmlentities($e->getMessage()));
        ?></div><?php
    }
    ?>

    <?php } else { ?>

        <div class="row">
            <?php Template::write_alert_div('info', '确保上传的表格含有名为“工号”“姓名”“邮箱”的表头。“邮箱”可留空，但表头必须存在。'); ?>
        </div>
        <div class="row">
            <form class="form-horizontal" role="form" method="post" target="_self" id="upload-form"
                  action="batch-add.php?upload=excel" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="col-sm-1">
                        <label for="file" class="control-label">上传表格</label>
                    </div>
                    <div class="col-sm-9">
                        <input type="file"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                               class="form-control" id="file" name="file" required/>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-default form-control">
                            <span class="glyphicon glyphicon-cloud-upload"></span>&nbsp;上传
                        </button>
                    </div>
                </div>
            </form>
        </div>
    <hr/>
        <div class="row">
            <button type="button" class="btn btn-default" onclick="window.location.href='/teacher/list/index.php'">
                <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;返回
            </button>
        </div>
    <?php } ?>

</div>


<?php
Template::write_standard_footer();
?>
</body>
</html>
