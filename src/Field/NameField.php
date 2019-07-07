<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:13
 */

namespace orgName\xxSystem\Field;

class NameField implements IField
{
    use GeneralStringTrait;

    public function get_field_name(): string
    {
        return '姓名';
    }

    public function check_and_throw(string $value): void
    {
        self::require_string($value);
        self::require_mb_strlen($value, 1, 20);
    }
}