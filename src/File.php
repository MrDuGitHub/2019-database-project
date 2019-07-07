<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/1
 * Time: 7:36
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class File
{
    public $id;
    public $filename;

    private function __construct()
    {
    }


    /**
     * 从ID加载一个现有的文件存储
     * @param string $id
     * @return File
     * @throws \Exception 以下情况可能抛出异常：文件 ID 在数据库中不存在；文件 ID 在文件系统中不存在；SQL 语句执行失败。
     */
    public static function load_file(string $id): File
    {
        //$filename = $_SERVER['DOCUMENT_ROOT'] . '/private/file-storage/' . $id;
        $file = new File();
        $file->id = $id;

        //从数据库中获取信息
        $conn = MySQL::get_instance();
        $result = $conn->prepare_bind_query(
            'SELECT file_storage_path FROM file_storage WHERE file_id=? LIMIT 1',
            's', $id
        );
        if ($result->num_rows !== 1) throw new \Exception('文件 ID ' . $id . ' 不存在于数据库。');
        $row = $result->fetch_assoc();
        $file->filename = trim($row['file_storage_path']);
        $result->close();

        if (!file_exists($file->filename)) throw new \Exception('断言失败：文件 ID ' . $id . ' 不存在于文件系统。');

        return $file;
    }


    /**
     * 从某个文件新建一个文件存储
     * @param string $filename
     * @return File
     * @throws \Exception 以下情况可能抛出异常：文件不存在；没有合适的权限；SQL 语句执行失败。
     */
    public static function new_file(string $filename): File
    {
        /* 计算sha256，同文件按“sha256-序号”依次排列构成单链表，序号从0开始，没有前导0
         * sha256按最通常的两个字节表示一个字节的方式只有64字节，如果换用base64会更少
         * linux的大多数文件系统对文件名有255字节限制，对路径有4096字节限制，足够
         * 特别地，若该sha256已经存在，与0号文件进行一一比较，如果相同，则不会新增序号，而是按现有文件处理
         * 如果不同，那么发生了sha256碰撞，为了节约时间也不会继续往下比较而是直接新增序号（这种情况理论上是有可能的）
        */

        $file = new File();
        if (!file_exists($filename)) throw new \Exception('文件 ' . $filename . ' 不存在。');
        $sha256_str = hash_file('sha256', $filename, false);
        for ($i = 0; ; ++$i) {
            $file->id = $sha256_str . '-' . $i;
            $file->filename = $_SERVER['DOCUMENT_ROOT'] . '/private/file-storage/' . $file->id;
            if (!file_exists($file->filename)) {
                //创建文件的硬链接。这个函数的参数名有点不合常理
                //注意，如果临时目录和file-storage目录不在一个分区下，则此函数会报错。无需关注。
                if (!@link($filename, $file->filename)) {
                    //如果失败，尝试用普通的复制
                    if (!copy($filename, $file->filename)) {
                        throw new \Exception('文件上传失败。原因：创建硬链接失败，并且复制文件失败。');
                    }
                }

                //文件信息写入数据库
                $conn = MySQL::get_instance();
                $conn->prepare_bind_execute(
                    'INSERT INTO file_storage (file_id, file_storage_path) VALUES (?,?)',
                    'ss', $file->id, $file->filename
                );


                break;
            } else {
                //如果发生了sha256碰撞，且是第0号，则验证是否为同一个文件
                if ($i == 0 && self::compare_files($filename, $file->filename)) {
                    //如果文件系统和数据库不一致，这里会报错。报错时，重新写入数据库。

                    try {
                        $file = self::load_file($file->id);
                    } catch (\Exception $e) {
                        //假定，报错一定是因为数据库中不存在此ID导致的

                        //文件信息写入数据库
                        $conn = MySQL::get_instance();
                        $conn->prepare_bind_execute(
                            'INSERT INTO file_storage (file_id, file_storage_path) VALUES (?,?)',
                            'ss', $file->id, $file->filename
                        );

                        $file = self::load_file($file->id);
                    }

                    return $file;
                }
                //若不是同一个文件，则继续循环，此后不再一一比较文件
            }
        }
        return $file;
    }

    /**
     * 对两个文件进行一一比较，判断文件内容是否完全相同。（不计算哈希）
     * @param string $filename_1
     * @param string $filename_2
     * @param int $bs <p>每次比较的块大小（字节）</p>
     * @return bool
     */
    private static function compare_files(string $filename_1, string $filename_2, int $bs = 8192): bool
    {
        //若文件大小不同则肯定不同
        if (filesize($filename_1) !== filesize($filename_2)) return false;
        //打开文件
        $file1 = fopen($filename_1, 'rb');
        $file2 = fopen($filename_2, 'rb');
        $result = true;

        while (!feof($file1)) {
            if (fread($file1, $bs) != fread($file2, $bs)) {
                $result = false;
                break;
            }
        }

        fclose($file1);
        fclose($file2);

        return $result;
    }


    /**
     * 获取当前 PHP 环境允许的文件上传大小（字节）。
     * @return float
     */
    public static function file_upload_max_size(): float
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = self::parse_size(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    private static function parse_size($size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }


}