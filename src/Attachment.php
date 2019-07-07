<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/2
 * Time: 3:00
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class Attachment
{

    /**
     * 附件的ID。
     * @var int
     */
    public $id;
    /**
     * 对应文件的File对象。
     * @var File
     */
    public $file;
    /**
     * 附件所属的用户。
     * @var int
     */
    public $uid;

    /**
     * 附件对应的文件名。
     * @var string
     */
    public $name;

    //多个attachment可指向一个file。
    //若一个file引用计数变成了0，该file不会立即被删除。
    //若用户不存在，该attachment也不会立即被删除。
    //因为检测是否为0是需要遍历数据库的。
    //这些会变成垃圾数据，之后再推出清理功能。

    private function __construct()
    {
    }

    /**
     * 给定用户 uid 、文件名、一个 File 对象，将其作为该用户名下的新的附件。返回 Attachment 对象。
     * @param int $uid
     * @param string $name 文件名。
     * @param File $file 已存在的 File 对象。
     * @return Attachment
     * @throws \Exception 以下情况可能抛出异常：SQL 语句执行失败。
     */
    public static function new_attachment_from_file(int $uid, string $name, File $file): Attachment
    {
        $attachment = new Attachment();
        $attachment->uid = $uid;
        $attachment->file = $file;
        $attachment->name = $name;

        //写入数据库，并得到自增ID
        $conn = MySQL::get_instance();

        $conn->prepare_bind_execute(
            'INSERT INTO attachment(attachment_uid,attachment_name, attachment_file_id) VALUES (?,?,?)',
            'iss', $uid, $name, $file->id
        );

        $attachment_id = $attachment->id = $conn->last_insert_id();
        $file_id = $file->id;
        //核实一遍确实是刚才的ID
        $result = $conn->prepare_bind_query(
            'SELECT attachment_id FROM attachment WHERE attachment_id = ? AND attachment_uid = ? AND attachment_file_id = ?',
            'iis', $attachment_id, $uid, $file_id
        );

        if ($result->num_rows === 0) throw new \Exception('数据库查询失败。');

        return $attachment;
    }


    /**
     * 给定用户 uid，文件路径和文件名，将该文件存至文件存储，并且作为该用户名下的一个附件。返回 Attachment 对象。
     * @param int $uid
     * @param string $filename 文件路径
     * @param string $name 文件名
     * @return Attachment
     * @throws \Exception 以下情况可能抛出异常：文件不存在；没有合适的权限；SQL 语句执行失败。
     */
    public static function new_attachment_from_filename(int $uid, string $filename, string $name): Attachment
    {
        $file = File::new_file($filename);
        return self::new_attachment_from_file($uid, $name, $file);
    }

    /**
     * 给定一个已存在的 attachment_id，重新构建 Attachment 对象
     * @param int $attachment_id
     * @throws \Exception 以下情况可能抛出异常：附件 ID 不存在；文件 ID 在数据库中不存在；文件 ID 在文件系统中不存在；SQL 语句执行失败。
     * @return Attachment
     */
    public static function load_attachment_from_attachment_id(int $attachment_id): Attachment
    {
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT attachment_uid,attachment_file_id,attachment_name FROM attachment WHERE attachment_id = ? LIMIT 1',
            'i', $attachment_id
        );

        if ($result->num_rows === 0) throw new \Exception('附件 ID ' . $attachment_id . ' 不存在。');
        $row = $result->fetch_assoc();
        $attachment = new Attachment();
        $attachment->id = $attachment_id;
        $attachment->uid = intval($row['attachment_uid']);
        $attachment->file = File::load_file(trim($row['attachment_file_id']));
        $attachment->name = $row['attachment_name'];
        return $attachment;
    }

    /**
     * 从数据库中删除此 Attachment。将函数参数置为'confirmed'以确认此操作。
     * 但是对应的 File 对象不会被删除，哪怕它的引用计数为 0。通过专门的 GC 来回收这样的 File。
     * @param string $confirmed
     * @throws \Exception
     */

    public function delete_attachment(string $confirmed)
    {
        if ($confirmed === 'confirmed') {
            $conn = MySQL::get_instance();
            $conn->prepare_bind_execute(
                'DELETE FROM attachment WHERE attachment_id = ? LIMIT 1',
                'i', $this->id
            );
        }
    }

}