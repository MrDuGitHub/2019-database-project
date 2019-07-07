<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 15:47
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class UserAgent
{
    private function __construct()
    {
    }

    /**
     * 给定浏览器提供的 UA，推测对应的操作系统信息。
     * @param string $ua UA
     * @return string 操作系统的名称、版本号、体系结构。
     */
    public static function get_operating_system(string $ua): string
    {
        $dd = new \DeviceDetector\DeviceDetector($ua);

        $dd->parse();
        if ($dd->isBot()) {
            $bot_info = $dd->getBot();
            return $bot_info['category'];
        } else {
            $os_info = $dd->getOs();
            return $os_info['name'] . ' ' . $os_info['version'] . ' ' . $os_info['platform'] . ' ';
        }
    }

    /**
     * 给定浏览器提供的 UA，推测对应的浏览器信息。
     * @param string $ua UA
     * @return string 浏览器名称、版本
     */
    public static function get_browser(string $ua): string
    {

        $dd = new \DeviceDetector\DeviceDetector($ua);

        $dd->parse();
        if ($dd->isBot()) {
            $bot_info = $dd->getBot();
            return $bot_info['name'];
        } else {
            $client_info = $dd->getClient();
            return $client_info['name'] . ' ' . $client_info['version'];
        }

    }
}