<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:06
 */

namespace orgName\xxSystem\Field;

interface IField
{
    /**
     * 返回该 Field 对应的属性名称。如：姓名、性别等。
     * @return string
     */
    public function get_field_name(): string;

    /**
     * 检查某个变量是否符合该 Field 的格式要求，否则抛出异常。
     * @param $value
     * @throws \Exception
     */
    public function check_and_throw(string $value): void;
}