<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/7/1
 * Time: 0:26
 */

//获取第一个题目的ID，跳转
namespace orgName\xxSystem;
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_non_visitor();

try {
    $conn = MySQL::get_instance();
    $result = $conn->prepare_no_bind_query(
        'SELECT database_question_id FROM database_question LIMIT 1'
    );

    if ($result->num_rows === 0) {
        throw new \Exception('题库中不存在试题。');
    }
    $row = $result->fetch_assoc();
    Template::die_302(
        './problem/index.php?question=' . intval($row['database_question_id'])
    );

} catch (\Exception $e) {
    Template::panic($e);
}