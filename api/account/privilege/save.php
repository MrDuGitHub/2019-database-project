<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/8
 * Time: 11:14
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
    Session::require_is_able_to_manage_user_level_2($target_uid);
    //获取权限
    $privileges = array();

    if (Session::is_root_admin()) {
        array_push($privileges, new Privilege\IsStudentAdmin($target_uid));
        array_push($privileges, new Privilege\IsTeacherAdmin($target_uid));
    }

    /*
    array_push($privileges, new Privilege\IsResearchTrainingAdmin($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingJudge($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingParticipant($target_uid));
    array_push($privileges, new Privilege\IsResearchTrainingAccessible($target_uid));
    */
    //设置新的权限
    foreach ($privileges as $privilege) {
        $key = $privilege->get_key();
        if (isset($input[$key])) {
            $value = ($input[$key] == '1');
            $privilege->set_value($value);
        }
    }

    //报告
    $message = '用户 ' . $target_uid . ' 的权限设置成功。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));

} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}