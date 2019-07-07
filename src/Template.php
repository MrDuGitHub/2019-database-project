<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/9
 * Time: 16:40
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class Template{

private function __construct()
{
}

public static function panic(\Exception $e): void
{
    Template::die_alert_page('danger', '错误', htmlentities($e->getMessage()));
}

public static function die_alert_page(string $type, string $title, string $content, string $addition = NULL): void
{
    self::write_alert_page($type, $title, $content, $addition);
    die();
}

public static function write_alert_page(string $type, string $title, string $content, string $addition = NULL):void
{
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <?php self::write_standard_head(); ?>
    <title><?= $title ?></title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <div class="row">
                <div class="page-header">
                    <h1><?= $title ?></h1>
                </div>
            </div>
            <?php self::write_alert_div($type, $content);
            if ($addition) echo $addition; ?>
        </div>
    </div>
</div>

<?php
self::write_standard_footer();
?>
</body>
</html>
<?php
}

public static function write_alert_div(string $type, string $content): void
{
    if ($type === 'success')
        echo '<div class="alert alert-success">';
    else if ($type === 'info')
        echo '<div class="alert alert-info">';
    else if ($type === 'danger')
        echo '<div class="alert alert-danger">';
    else //if($template_alert_type==='warning')
        echo '<div class="alert alert-warning">';
    echo $content;
    echo '</div>';
}

public static function write_standard_footer(): void
{
}

public static function write_standard_head(): void
{
    ?>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- 国产浏览器开启Webkit内核 -->
    <meta name="renderer" content="webkit"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/js/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous"/>
    <!-- integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"-->

    <!-- Bootstrap 主题 -->
    <link rel="stylesheet" href="/js/bootstrap/3.3.7/css/bootstrap-theme.min.css" crossorigin="anonymous"/>
    <!-- integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"-->

    <!-- jQuery -->
    <script src="/js/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
    <!-- integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f"-->

    <!-- Bootstrap JS -->
    <script src="/js/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <!-- integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"-->

    <!-- Underscore JS -->
    <script src="/js/underscore/1.9.0/underscore-min.js" crossorigin="anonymous"></script>
    <!-- integrity="sha384-oU4t3DBS3lahlQlnSn6pAA4VSON9MpgREETn6LHzpSZOrvQr6G1JNStk+sNFkiLB"-->

    <?php
}

public static function die_403(): void
{
    http_response_code(403);
    die();
}

public static function die_302(string $url): void
{
    header("Location:$url");
    die();
}

public static function die_json(string $content): void
{
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Encoding: UTF-8');
    header('Content-Type:application/json');//这个类型声明非常关键
    header('Expires: 0');
    header('Pragma: no-cache');

    echo($content);
    exit();
}

public static function header_xlsx_file(string $name_without_extension): void
{
    $filename_urlencoded = urlencode($name_without_extension . '.xlsx');

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename* = UTF-8\'\'' . $filename_urlencoded);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
}

public static function header_zip_file(string $name_without_extension): void
{
    $filename_urlencoded = urlencode($name_without_extension . '.zip');

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename* = UTF-8\'\'' . $filename_urlencoded);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
}

public static function header_docx_file(string $name_without_extension): void
{
    $filename_urlencoded = urlencode($name_without_extension . '.docx');

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename* = UTF-8\'\'' . $filename_urlencoded);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
}

public static function header_force_download(string $name_with_extension): void
{
    $filename_urlencoded = urlencode($name_with_extension);

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Encoding: UTF-8');
    header('Content-Type: application/force-download');
    header('Content-Disposition: attachment; filename* = UTF-8\'\'' . $filename_urlencoded);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
}


/**
 * 给定一个文件路径，令其下载到浏览器。支持大文件。
 * @param string $filename
 * @param string $name
 */
public static function force_download_header_and_readfile(string $filename, string $name): void
{
    assert(file_exists($filename));

    self::header_force_download($name);
    header('Content-Length: ' . filesize($filename));

    $handle = fopen($filename, 'rb');

    while (!feof($handle)) {
        echo fread($handle, CHUNK_SIZE);
        flush();
    }

    fclose($handle);
}


public static function write_navbar_div(): void
{
    try {
        ?>
        <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <span class="navbar-brand"><?= WEBSITE_TITLE ?></span>
                </div>

                <ul class="nav navbar-nav">
                    <li class="nav-item" id="nav-item-homepage">
                        <a class="nav-link" href="/index.php">主页</a>
                    </li>

                    <li class="nav-item" id="nav-item-problem">
                        <a class="nav-link" href="/problem/index.php">题库</a>
                    </li>
                    <li class="nav-item" id="nav-item-exercise">
                        <a class="nav-link" href="/problem/exercise/index.php">练习</a>
                    </li>
                    <?php
                    if (Session::is_student_admin()) {
                        ?>
                        <li class="nav-item" id="nav-item-list_students">
                            <a class="nav-link" href="/student/list/index.php">学生管理</a>
                        </li>
                        <?php
                    }

                    if (Session::is_teacher_admin()) {
                        ?>
                        <li class="nav-item" id="nav-item-list_teachers">
                            <a class="nav-link" href="/teacher/list/index.php">教师管理</a>
                        </li>
                        <?php
                    }

                    ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <span class="glyphicon glyphicon-user"></span>
                            <?php echo htmlentities(Session::get_user_name()); ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="/account/setting.php?uid=<?= Session::get_user_uid() ?>">
                                    <span class="glyphicon glyphicon-cog"></span>&nbsp;账户设置
                                </a>
                            </li>
                            <li>
                                <a href="/account/logging.php?limit=10&uid=<?= Session::get_user_uid() ?>">
                                    <span class="glyphicon glyphicon-list-alt"></span>&nbsp;登录日志
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="/logout.php?redirect=false">
                                    <span class="glyphicon glyphicon-log-out"></span>&nbsp;登出系统
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    <?php } catch (\Exception $e) {
        Template::write_alert_div('danger', htmlentities($e->getMessage()));
    }
}


}