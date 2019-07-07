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
    //获得value
    $target_email = $input['email'] ?? '';
    //设置
    $user = new User($target_uid);
    $user->set_email($target_email);

    //报告
    $message = '用户 ' . $target_uid . ' 的 E-mail 已被更改。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}

