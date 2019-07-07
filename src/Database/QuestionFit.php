<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/6/30
 * Time: 21:49
 */

namespace orgName\xxSystem\Database;


use orgName\xxSystem\MySQL;

class QuestionFit
{
    /** @var int */
    public $id;
    /** @var string */
    public $name;

    private function __construct()
    {
    }

    /**
     * @return QuestionFit[]
     * @throws \Exception
     */
    public static function get_all(): array
    {
        $array = array();
        $conn = MySQL::get_instance();
        $result = $conn->prepare_no_bind_query('SELECT database_question_id,database_question_name FROM database_question');
        while ($row = $result->fetch_assoc()) {
            $instance = new self;
            $instance->id = intval($row['database_question_id']);
            $instance->name = $row['database_question_name'];
            $array[$instance->id] = $instance;
        }
        return $array;
    }

    /**
     * @param int $id
     * @return QuestionFit
     * @throws \Exception
     */
    public static function load_from_id(int $id): self
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT database_question_name FROM database_question WHERE database_question_id = ? LIMIT 1',
            'i', $id
        );
        if ($result->num_rows === 0) throw new \Exception('找不到题目 ' . $id . '。');
        $row = $result->fetch_assoc();

        $instance = new self;
        $instance->id = $id;
        $instance->name = $row['database_question_name'];

        return $instance;
    }
}