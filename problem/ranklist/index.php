<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/7/1
 * Time: 1:57
 */


namespace orgName\xxSystem;

use orgName\xxSystem\Database\Answer;
use orgName\xxSystem\Database\Question;
use orgName\xxSystem\Database\QuestionFit;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::require_non_ie();
Session::require_non_visitor();

try {
    $all_answers = Answer::get_all();
    $questions = QuestionFit::get_all();

    $student_answers = array();
    $student_sum = array();

    foreach ($all_answers as $answer) {
        if (!array_key_exists($answer->uid, $student_answers)) {
            $student_answers[$answer->uid] = array();
        }
        $student_answers[$answer->uid][$answer->question_id] = $answer->answer_is_correct;
        if ($answer->answer_is_correct) {
            if (!array_key_exists($answer->uid, $student_sum)) {
                $student_sum[$answer->uid] = 1;
            } else {
                //1题1分
                $student_sum[$answer->uid] += 1;
            }
        }
    }

    $excel = new ExcelWriter();
    $line1 = array('学号', '姓名', '总答对题数');
    $line2 = array();
    foreach ($questions as $question) {
        array_push($line2, $question->name);
    };
    $excel->write_line(...$line1, ...$line2);

    foreach ($student_answers as $uid => $student_answer) {
        try{
            $student_name = (new User($uid))->get_name();
        }catch (\Exception $e){
            $student_name=$uid;
        }


        $line1 = array($uid, $student_name, $student_sum[$uid] ?? 0);
        $line2 = array();
        foreach ($questions as $question) {
            if (($student_answer[$question->id] ?? false)) {
                //不考虑多语言
                array_push($line2, '√');
            } else {
                array_push($line2, ' ');
            }
        }
        $excel->write_line(...$line1, ...$line2);
    }

    Template::header_xlsx_file(time());
    $excel->save('php://output');

} catch (\Exception $e) {
    Template::panic($e);
}