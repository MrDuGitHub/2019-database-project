# 部署

## 注记符
`<www>` 表示网站所在目录。本文件应位于 `<www>/install/deployment.md`。

## 配置要求
1. 必须安装 Linux，推荐 CentOS 8。
2. 必须安装 HTTP 服务器，推荐 Nginx；必须配置 HTTPS，推荐配置 HTTP 2.0 及 HSTS。
3. 必须安装 64 位的 PHP 7.1 或更高版本。推荐 PHP 7.2 或更高版本。
4. 必须安装 MySQL，推荐 MariaDB。数据库的编码必须设置为 `utf8mb4`。
5. 以下文件夹必须设置为对 HTTP 服务器可读写，且必须对浏览器返回 403：
   - PHP 函数 `sys_temp_dir()` 返回的临时目录；
   - `<www>/private` 目录。
6. 推荐配置 `Zend Opcache` 以加速。 

## 开始部署

1. 把工作目录切换到 `<www> `目录

   ```shell
   cd <www>
   ```

2. 把 Composer 的 PHP 可执行文件安装到当前目录

   ```shell
   curl -sS https://getcomposer.org/installer | php
   ```

   完成后，当前目录中会得到 ``composer.phar`` 文件。

3. 让 Composer 安装依赖组件

   ```shell
   php composer.phar install
   ```

4. 将 `database.sql` 文件导入数据库。

5. 将 `<www>/config-example` 文件夹移动到 `<www>/config`，并根据实际情况修改里面的 PHP 文件。

6. 把工作目录切换到 `<www>/private` 目录，下载 GeoLite2 数据库，并解压。可直接复制下面这条命令完成。

   ```bash
   (dir="/tmp" && name="$dir/$(wget http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz -O - | gunzip | tar xvf - -C "$dir" | grep GeoLite2-City\\.mmdb )" && [ "$(echo "$name" | wc -l)" = "1" ] && [ ! "$dir/" = "$name" ] && mv "$name" . && rm -r "$(dirname "$name")")
   ```
   
   检查 `GeoLite2-City.mmdb` 文件是否出现。
   
   
   
   如果上面的命令没有执行成功，备用的命令如下。
   
   ```shell
   pushd /tmp
   wget http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz -O - | gunzip | tar xvf -
   ```
   找到解压得到的 `GeoLite2-City.mmdb` 文件，将其移动至 `<www>/private/GeoLite2-City.mmdb`。
   
7. 必须将 `<www>/private` 目录设置为 403。此项已在“配置要求”中给出，再强调一次。
Nginx 配置文件示例：
```
location /private {
    deny all;
}
```

8. 将 `<www>/install` 目录删除，或设置为 403。

9. 可将以下文件或目录设置为 403：
   - `<www>/composer.lock`
   - `<www>/composer.json`
   - `<www>/vendor`
   - `<www>/.*`
   - `<www>/README.md`
   - `<www>/install`

10. 可为除 `.php` 和目录外的 URL 配置缓存。
