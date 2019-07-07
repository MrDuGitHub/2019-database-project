<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/2/10
 * Time: 14:40
 */

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class ExcelWriter
{
    private $excel;

    private $row_id_of_sheet = array();

    public function __construct()
    {
        $this->excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->row_id_of_sheet[$this->excel->getActiveSheetIndex()] = 0;
    }

    /**
     * 切换到指定名称的工作表。若不存在，则新建之。
     * @param string $name
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function switch_sheet(string $name)
    {
        //若当前表为空，则不保留
        if ($this->row_id_of_sheet[$this->excel->getActiveSheetIndex()] === 0) {
            $this->excel->removeSheetByIndex(
                $this->excel->getActiveSheetIndex()
            );
        }

        $sheet = $this->excel->getSheetByName($name);
        if ($sheet === null) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->excel, $name);
            $this->excel->addSheet($sheet);
        }

        $this->excel->setActiveSheetIndex($this->excel->getIndex($sheet));
    }

    /**
     * 输出xlsx文件内容到指定路径（或php://output）。
     * @param $output
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save($output)
    {
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->excel, 'Xlsx')->save($output);
    }

    /**
     * 将这个数组的内容以文本形式写入表格的下一行。
     * @param mixed ...$texts
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function write_line(...$texts)
    {
        $col_id = 0;
        ++$this->row_id_of_sheet[$this->excel->getActiveSheetIndex()];
        foreach ($texts as $v) {
            $col_letter = $this->col_id_to_letter(++$col_id);

            $this->excel->getActiveSheet()->setCellValueExplicit($col_letter . $this->row_id_of_sheet[$this->excel->getActiveSheetIndex()], $v, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        }
    }

    private function col_id_to_letter(int $col_id)
    {
        $str = '';
        while ($col_id > 0) {
            $modulo = ($col_id - 1) % 26;
            $str = chr(65 + $modulo) . $str;
            $col_id = intdiv($col_id - $modulo, 26);
        }
        return $str;
    }
}