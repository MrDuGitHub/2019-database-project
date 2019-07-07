<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 15:43
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
error_reporting(E_ERROR);
try {
    //接收数据
    $raw_data = file_get_contents('php://input');
    $input = json_decode($raw_data, true, 2);
    Session::require_condition($input !== null);
    //获得uid
    $target_uid = intval($input['uid'] ?? 0);
    Session::require_condition($target_uid !== 0);
    //检查权限
    Session::require_is_able_to_manage_user_level_1($target_uid);
    if (!Session::is_oneself($target_uid)) throw new \Exception('管理员只能为其他用户重置为随机密码，不能为其设定任意的密码。');
    //获得value
    $target_password = $input['password'] ?? '';
    //设置
    $user = new User($target_uid);
    $user->set_password($target_password);
    //报告
    $message = '用户 ' . $target_uid . ' 的密码已被更改为 ' . strlen($target_password) . ' 字节的新密码。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}

