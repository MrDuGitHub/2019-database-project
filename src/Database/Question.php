<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2019/6/30
 * Time: 21:49
 */

namespace orgName\xxSystem\Database;


use orgName\xxSystem\MySQL;

class Question
{
    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $description;
    /** @var string
     * 以分号分割的多条 SQL 语句。
     */
    public $preload_sql;
    /** @var string */
    public $answer_hash;

    /** @var string */
    public $table_digest;

    private function __construct()
    {
    }

    /**
     * @param int $id
     * @return Question
     * @throws \Exception
     */
    public static function load_from_id(int $id): self
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT database_question_name,database_question_description,database_question_preload_sql,database_question_answer_hash,database_question_table_digest FROM database_question WHERE database_question_id = ? LIMIT 1',
            'i', $id
        );
        if ($result->num_rows === 0) throw new \Exception('找不到题目 ' . $id . '。');
        $row = $result->fetch_assoc();

        $instance = new self;
        $instance->id = $id;
        $instance->name = $row['database_question_name'];
        $instance->description = $row['database_question_description'];
        $instance->preload_sql = $row['database_question_preload_sql'];
        $instance->answer_hash = $row['database_question_answer_hash'];
        $instance->table_digest = $row['database_question_table_digest'];

        return $instance;
    }
}