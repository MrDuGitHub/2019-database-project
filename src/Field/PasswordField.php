<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:14
 */

namespace orgName\xxSystem\Field;

class PasswordField implements IField
{
    use GeneralStringTrait;

    public function get_field_name(): string
    {
        return '密码';
    }

    /**
     * 按 /include/constants.php 中设置的密码策略生成随机密码。
     * 当系统熵不足时会抛出异常。平时不需要考虑这种情况。
     * @return string
     * @throws \Exception
     */
    public function get_random_password(): string
    {
        return bin2hex(random_bytes(intdiv(DEFAULT_PASSWORD_LENGTH + 1, 2)));
    }

    public function check_and_throw(string $value): void
    {
        function is_special_char($char)
        {
            return
                (
                    (ord($char) <= 47 && ord($char) >= 36)
                    || (ord($char) >= 58 && ord($char) <= 64)
                    || (ord($char) >= 91 && ord($char) <= 96)
                    || (ord($char) >= 123 && ord($char) <= 126)
                    || (ord($char) === 32)
                );
        }

        function is_digit($char)
        {
            return (ord($char) >= 48 && ord($char) <= 57);
        }

        function is_uppercase($char)
        {
            return (ord($char) >= 65 && ord($char) <= 90);
        }

        function is_lowercase($char)
        {
            return (ord($char) >= 97 && ord($char) <= 122);
        }

        $this->require_string($value);
        $this->require_mb_strlen($value, MINIMUM_PASSWORD_LENGTH, MAXIMUM_PASSWORD_LENGTH);

        //检查密码复杂性，无视多字节字符
        $has_digit = $has_letter = $has_special_char = $has_other_char = false;
        for ($i = 0; $i < strlen($value); ++$i) {
            if (is_digit($value[$i])) $has_digit = true;
            else if (is_uppercase($value[$i]) || is_lowercase($value[$i])) $has_letter = true;
            else if (is_special_char($value[$i])) $has_special_char = true;
            else $has_other_char = true;

            $check = 0;
            if ($has_digit) ++$check;
            if ($has_letter) ++$check;
            if ($has_special_char) ++$check;
            if ($check >= 2) {
                return;
            } elseif ($has_other_char) {

                return;
                // '您的密码含有特殊字符，不保证在任何情况下可用。另外，密码开头和结尾的不可见字符会被过滤。Use at your own risk.';
            }
        }

        throw new \Exception($this->get_field_name() . '不符合复杂性要求。');
    }

}