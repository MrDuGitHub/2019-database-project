<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/6/30
 * Time: 23:04
 */

namespace orgName\xxSystem;

use orgName\xxSystem\Database\Answer;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');
error_reporting(E_ERROR);
try {
    //接收数据
    $raw_data = file_get_contents('php://input');
    $input = json_decode($raw_data, true, 2);
    Session::require_condition($input !== null);

    //检查权限
    Session::require_non_visitor();

    //获得value
    $target_uid = Session::get_user_uid();
    $target_question_id = intval($input['question_id'] ?? 0);
    $target_answer = $input['answer'] ?? '';
    $target_answer_is_correct = ($input['answer_is_correct'] ?? 0) === 0 ? false : true;

    //写入数据库
    $answer = Answer::new_answer(
        $target_uid, $target_question_id, $target_answer, $target_answer_is_correct
    );
    //报告
    $message = '提交成功。';
    Template::die_json(json_encode(array('result' => 'success', 'message' => $message)));
} catch (\Exception $e) {
    $message = $e->getMessage();
    Template::die_json(json_encode(array('result' => 'failure', 'message' => $message)));
}
