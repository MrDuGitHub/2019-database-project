<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/23
 * Time: 15:13
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class GeoIP
{
    private static $reader;

    private function __construct()
    {
    }

    /**
     * 以简体中文语言初始化一个 GeoIP 对象。
     * 当在此段代码里写死的 GeoIP 数据库文件不存在时，抛出异常。
     * @return \GeoIp2\Database\Reader
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public static function get_instance()
    {
        if (!(self::$reader instanceof \GeoIp2\Database\Reader)) {
            self::$reader = new \GeoIp2\Database\Reader(
                $_SERVER['DOCUMENT_ROOT'] . '/private/GeoLite2-City.mmdb',
                ['zh-CN']
            );
        }
        return self::$reader;
    }

    /**
     * 给定一个 IPv4 或 IPv6 地址，返回它所在区域。结果比较粗略，仅供参考。<br/>
     * 当 GeoIP 数据库文件不存在时抛出异常。
     * @param string $ip
     * @return string
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public static function get_location_name(string $ip): string
    {
        //判断IP是否合法
        if (filter_var($ip, FILTER_VALIDATE_IP)) {

            //判断是否是公网IPv4或IPv6
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $reader = self::get_instance();
                try {
                    $record = $reader->city($ip);
                    $result = $record->country->name . ' ' . $record->city->name;
                } catch (\Exception $e) {
                    $result = '未知公网地址';
                }
                return $result;
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                //是IPv4
                return '非公网 IPv4';
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
                //是IPv6
                return '非公网 IPv6';
            } else {
                return "不是有效地址";
            }
        } else {
            return "不是有效地址";
        }
    }
}