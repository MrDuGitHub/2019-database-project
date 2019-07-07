<?php
/**
 * Created by PhpStorm.
 * User: SadPencil
 * Date: 2018/10/4
 * Time: 8:12
 */

namespace orgName\xxSystem\Field;

trait GeneralStringTrait
{

    /**
     * 要求该变量为字符串，否则抛出异常。
     * @param $value
     * @throws \Exception
     */
    function require_string($value): void
    {
        if (!isset($value) || $value === '') throw new \Exception('缺少' . $this->get_field_name());
        if (!is_string($value)) throw new \Exception($this->get_field_name() . '不是字符串。');
    }

    /**
     * 要求该字符串的字符个数位于闭区间[min,max]内，否则抛出异常。
     * 注：多于1字节的字符也是一个字符。
     * @param $value
     * @param int $min
     * @param int $max
     * @throws \Exception
     */
    function require_mb_strlen(string $value, int $min, int $max)
    {
        $len = mb_strlen($value);
        if ($len < $min || $len > $max) throw new \Exception($this->get_field_name() . '过长或过短。');
    }

    /**
     * 要求该字符串的字节数位于闭区间[min,max]内，否则抛出异常。
     * @param $value
     * @param int $min
     * @param int $max
     * @throws \Exception
     */
    function require_strlen(string $value, int $min, int $max)
    {
        $len = strlen($value);
        if ($len < $min || $len > $max) throw new \Exception($this->get_field_name() . '过长或过短。');
    }

    /**
     * 要求该字符串满足给定的正则表达式，否则抛出异常。
     * @param string $value
     * @param string $regex
     * @throws \Exception
     */
    function require_regex(string $value, string $regex)
    {
        if (preg_match($regex, $value) !== 1) throw new \Exception($this->get_field_name() . '格式不正确。');
    }
}