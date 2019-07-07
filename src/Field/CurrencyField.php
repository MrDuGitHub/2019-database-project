<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:14
 */

namespace orgName\xxSystem\Field;

class CurrencyField implements IField
{
    public function get_field_name(): string
    {
        return '金额';
    }

    public function check_and_throw(string $value): void
    {
        if (!is_numeric($value)) throw new \Exception($this->get_field_name() . '不是数字。');
    }
}