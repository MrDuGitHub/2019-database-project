<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 22:41
 */
namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_logged_in();

$target_uid = intval($_GET['uid'] ?? 0);
Session::require_condition($target_uid !== 0);

try {
    Session::require_is_able_to_manage_user_level_1($target_uid);

    $action = ($_GET['action'] ?? null === 'download') ? 'download' : null;

    $limit = intval($_GET['limit'] ?? 0);

    $time_zone = TIME_ZONE;

    $conn = MySQL::get_instance();

    if ($limit > 0) {
        $result = $conn->prepare_bind_query(
            "SELECT CONVERT_TZ(login_time,'+00:00',?),ip,result,FROM_BASE64(ua) FROM log_login WHERE uid=? ORDER BY login_time DESC LIMIT ?",
            'sii', $time_zone, $target_uid, $limit
        );
    } else {
        $result = $conn->prepare_bind_query(
            "SELECT CONVERT_TZ(login_time,'+00:00',?),ip,result,FROM_BASE64(ua) FROM log_login WHERE uid=? ORDER BY login_time DESC",
            'si', $time_zone, $target_uid
        );
    }


} catch (\Exception $e) {
    Template::write_alert_page('danger', '错误', htmlentities($e->getMessage()));
    die();
}

if ($action === 'download'){
    ob_start();

    try {
        $writer = new ExcelWriter();
        $writer->write_line('登录时间 (时区 ' . $time_zone . ' )', '登录结果', 'IP 地址', '参考位置', '操作系统', '浏览器', 'UA 标识');
        while ($row = $result->fetch_row()) {
            $login_time = $row[0];
            $ip = $row[1];
            $login_result = $row[2];
            $ua = $row[3];

            $writer->write_line(
                $login_time, ($login_result === 0 ? '失败' : '成功'),
                $ip,
                GeoIP::get_location_name($ip),
                UserAgent::get_operating_system($ua),
                UserAgent::get_browser($ua),
                $ua
            );
        }


        ob_end_clean();
        ob_start();
        error_reporting(E_ERROR);
        $writer->save('php://output');
        Template::header_xlsx_file(time());
        ob_end_flush();
    } catch (\Exception $e) {
        ob_end_flush();
        Template::write_alert_page('danger', '错误', htmlentities($e->getMessage()));
        die();
    }


}else{
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php Template::write_standard_head() ?>
    <title>登录日志 - <?= $target_uid ?></title>
    <script>
        $(document).ready(function () {
            $('#nav-item-user_login_log').addClass('active');
        });
    </script>
</head>
<body>

<?php Template::write_navbar_div() ?>


<div class="container">
    <div class="row">
        <div class="page-header">
            <h1>登录日志
                <small><?= $target_uid ?></small>
            </h1>
        </div>
    </div>

    <div class="row">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#" data-target="#quick_view" data-toggle="tab">速览</a>
            </li>
            <li>
                <a href="#" data-target="#download" data-toggle="tab">下载日志</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="quick_view">

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>登录时间 (时区<?= $time_zone ?> )</th>
                        <th>登录结果</th>
                        <th>IP 地址</th>
                        <th>参考位置</th>
                        <th>操作系统</th>
                        <th>浏览器</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $result->fetch_row()) {
                        $login_time = $row[0];
                        $ip = $row[1];
                        $login_result = $row[2];
                        $ua = $row[3];
                        echo '<tr' . ($login_result ? '' : ' class="danger" ') . '>';
                        echo '<td>' . htmlentities(trim($login_time)) . '</td>';
                        echo '<td>' . ($login_result ? '成功' : '失败') . '</td>';
                        echo '<td>' . htmlentities(trim($ip)) . '</td>';
                        /** @noinspection PhpUnhandledExceptionInspection */
                        echo '<td>' . htmlentities(GeoIP::get_location_name($ip)) . '</td>';
                        echo '<td>' . htmlentities(UserAgent::get_operating_system($ua)) . '</td>';
                        echo '<td>' . htmlentities(UserAgent::get_browser($ua)) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>

            </div>
            <div class="tab-pane" id="download">
                <div class="container">
                    <div class="form-group">
                        <div class="radio">
                            <label class="form-inline">
                                <input type="radio" name="download_option" id="download_limit"
                                       value="download_limit"
                                       checked/>
                                <span>下载最近</span>
                                <input type="text" maxlength="10" id="download_limit_input" pattern="[1-9]\d*"
                                       value="10" class="form-control"/>
                                <span>条</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input type="radio" name="download_option" id="download_all" value="download_all">
                                下载全部数据
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary"
                            onclick="window.open('logging.php?action=download&uid=<?= $target_uid ?>&limit='+($('#download_all').prop('checked')?'0':$('#download_limit_input').val()))">
                        下载
                    </button>
                </div>
            </div>
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