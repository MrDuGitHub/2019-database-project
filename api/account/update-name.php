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
    $target_name = $input['name'] ?? '';
    //设置
    $user = new User($target_uid);
    $user->set_name($target_name);
    //报告
    $message = '用户 ' . $target_uid . ' 的名称已被更改。如果该用户已登录，下次登录时才能看到名称的更新。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}

