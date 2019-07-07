<?php
if (!(is_ie())) die_302('/index.php');
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- 国产浏览器开启Webkit内核 -->
    <meta name="renderer" content="webkit"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/js/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"/>
    <!-- Bootstrap 主题 -->
    <link rel="stylesheet" href="/js/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"/>
    <!-- jQuery -->
    <script src="/js/jquery/3.2.1/jquery.min.js"
            integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f"
            crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="/js/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
    <title>不支持的浏览器</title>
</head>
<body>
<br/>
<div class="container">
    <div class="jumbotron">
        <div class="panel panel-danger">
            <div class="panel-heading"><h1 style="font-size:xx-large">不支持的浏览器</h1></div>
        </div>
        <p>您仍在使用 Internet Explorer。请使用现代的浏览器打开，例如 Chrome, Firefox, Edge 等。</p>
        <p>如果使用了国产浏览器，请将内核调至极速模式(Chromium 内核)。</p>

        <?php if (isset($_SERVER['HTTP_USER_AGENT']) && ((strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 5') !== false))) { ?>
            <p>Firefox 浏览器下载（适用于古董系统）：<a target="_blank"
                                         href='https://download-installer.cdn.mozilla.net/pub/firefox/releases/52.9.0esr/win32/zh-CN/Firefox%20Setup%2052.9.0esr.exe'>下载
                    52.9.0 ESR</a></p>
        <?php } else {
            ?>
            <p>Firefox 浏览器下载：<a target="_blank" href="https://www.mozilla.org/zh-CN/firefox/download/thanks/">点击下载</a>
            </p>
            <?php
        } ?>
        <img src="ie-switch-kernel.png"/>

    </div>
</div>
</body>
</html><?php

function is_ie(): bool
{
    return isset($_SERVER['HTTP_USER_AGENT']) &&
        (
            (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) ||
            (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0') !== false)
        );
}

function die_302(string $url): void
{
    header("Location:$url");
    die();
}
