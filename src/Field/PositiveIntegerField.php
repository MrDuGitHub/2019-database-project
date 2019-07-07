<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:13
 */

namespace orgName\xxSystem\Field;

trait PositiveIntegerField
{
    use GeneralStringTrait;

    /**
     * 检查某个变量是否为不含前导0的正整数组成的字符串，如果不是，抛出异常。
     * @param string $value
     * @throws \Exception
     */
    public function check_and_throw(string $value): void
    {
        $this->require_string($value);
        $this->require_strlen($value, 1, 20);
        $this->require_regex($value, '/^[1-9]\d*$/');
    }
}