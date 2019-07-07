<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/6/30
 * Time: 22:43
 */

namespace orgName\xxSystem\Database;

use orgName\xxSystem\MySQL;

class Answer
{
    /** @var int */
    public $question_id;
    /** @var int */
    public $uid;
    /** @var string
     * 记录学生【第一次】成功提交的正确答案
     */
    public $answer_sql;
    /** @var bool */
    public $answer_is_correct;

    /** @var string
     * 记录学生【第一次】成功提交正确答案的时间
     * 如果学生还没有成功提交答案，则对应最后一次提交时间
     * 此属性只读。修改无效。
     */
    public $answer_submit_time;

    private function __construct()
    {
    }


    /**
     * @param int $uid
     * @param int $question_id
     * @return Answer
     * @throws \Exception
     */
    public static function load_answer(int $uid, int $question_id): self
    {
        $v_time_zone = TIME_ZONE;

        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT database_answer_sql,database_answer_is_correct,CONVERT_TZ(database_answer_submit_time,\'+00:00\',?) FROM database_answer WHERE uid = ? AND database_question_id = ? LIMIT 1',
            'sii', $v_time_zone, $uid, $question_id
        );
        if ($result->num_rows === 0) throw new \Exception('找不到用户 ' . $uid . '对题目 ' . $question_id . ' 的作答情况。');
        $row = $result->fetch_assoc();
        $instance = new self();
        $instance->question_id = $question_id;
        $instance->uid = $uid;
        $instance->answer_is_correct = intval($row['database_answer_is_correct']);
        $instance->answer_sql = $row['database_answer_sql'];
        $instance->answer_submit_time = $row['CONVERT_TZ(database_answer_submit_time,\'+00:00\',?)'];
        return $instance;
    }

    /**
     * @throws \Exception
     */
    public function save(): void
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query('SELECT database_answer_is_correct FROM database_answer WHERE uid = ? AND database_question_id = ? LIMIT 1',
            'ii', $this->uid, $this->question_id
        );
        $v_is_correct = ($this->answer_is_correct) ? 1 : 0;
        if ($result->num_rows === 0) {
            //插入
            $conn->prepare_bind_execute(
                'INSERT INTO database_answer (uid, database_question_id, database_answer_sql,database_answer_is_correct,database_answer_submit_time) VALUES (?,?,?,?, UTC_TIMESTAMP() )',
                'iisi', $this->uid, $this->question_id, $this->answer_sql, $v_is_correct
            );

        } else {
            $row = $result->fetch_assoc();
            $prev_answer_is_correct = intval($row['database_answer_is_correct']) === 0 ? false : true;
            //更新
            if ($prev_answer_is_correct) {
                //do nothing
            } else {
                $conn->prepare_bind_execute(
                    'UPDATE database_answer SET database_answer_sql=?, database_answer_is_correct=?,database_answer_submit_time=UTC_TIMESTAMP() WHERE uid = ? AND database_question_id = ? LIMIT 1',
                    'siii', $this->answer_sql, $v_is_correct, $this->uid, $this->question_id
                );
            }
        }
    }

    /**
     * @param int $uid
     * @param int $question_id
     * @param string $database_answer_sql
     * @param bool $database_answer_is_correct
     * @return Answer
     * @throws \Exception
     */
    public static function new_answer(int $uid, int $question_id, string $database_answer_sql, bool $database_answer_is_correct): self
    {
        $instance = new self;
        $instance->uid = $uid;
        $instance->question_id = $question_id;
        $instance->answer_sql = $database_answer_sql;
        $instance->answer_is_correct = $database_answer_is_correct;
        $instance->save();
        return self::load_answer($uid, $question_id);
    }


    /**
     * @return Answer[]
     * @throws \Exception
     */
    public static function get_all(): array
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_no_bind_query(
            'SELECT uid,database_question_id,database_answer_sql,database_answer_is_correct FROM database_answer'
        );
        $array = array();
        while ($row = $result->fetch_assoc()) {
            $instance = new self;
            $instance->uid = intval($row['uid']);
            $instance->question_id = intval($row['database_question_id']);
            $instance->answer_sql = intval($row['database_answer_sql']);
            $instance->answer_is_correct = intval($row['database_answer_is_correct']);
            array_push($array, $instance);
        }
        return $array;
    }
}