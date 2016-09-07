<?php
namespace LuffyZhao\Library;

class Verify
{
    const REG_HANDSET = "/^((86)[\+-]?)?^1\d{10}$/i";
    const REG_EMAIL   = "/^[_\.0-9a-z-a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/i";
    const REG_MONEY   = "/^\d+(\.\d{1,2})?$/";
    /**
     * 判断范围
     * @param  [type] $number [description]
     * @param  [type] $min    [description]
     * @param  [type] $max    [description]
     * @return [type]         [description]
     */
    public static function range($input, $string)
    {
        if (strpos($string, ',') === false) {
            $min = 0;
            $max = $string;
        } else {
            list($min, $max) = explode(',', $string, 2);
        }

        return ($input >= $min && $input <= $max) ? true : false;
    }

    /**
     * 判断两个值是否相等【值和类型都必须相等】
     * @param  [type] $str1 [description]
     * @param  [type] $str2 [description]
     * @return [type]       [description]
     */
    public static function equal($input, $string, $params)
    {
        if (strpos($string, '@') !== false) {
            $key  = substr($string, 1);
            $str2 = $params[$key];
        } else {
            $str2 = $string;
        }

        return ($input == $str2) ? true : false;
    }

    /**
     * 验证长度
     * @param  [type]  $input [description]
     * @param  integer $min   [description]
     * @param  integer $max   [description]
     * @return [type]         [description]
     */
    public static function length($input, $string)
    {
        if (strpos($string, ',') === false) {
            $min = 0;
            $max = $string;
        } else {
            list($min, $max) = explode(',', $string, 2);
        }

        return (strlen($input) >= $min && strlen($input) <= $max) ? true : false;
    }

    /**
     * in
     * @param  [type] $input  [description]
     * @param  [type] $string [description]
     * @return [type]         [description]
     */
    public static function in($input, $string)
    {
        $array = explode(',', $string);
        return (in_array($input, $array)) ? true : false;
    }

    /**
     * 正则验证
     * @param  [type] $input [description]
     * @param  [type] $preg   [description]
     * @return [type]         [description]
     */
    public static function preg($input, $preg)
    {
        return preg_match($preg, $input) ? true : false;
    }

    /**
     * 是否有效金额 2 位小数
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function money($input)
    {
        return preg_match(Verify::REG_MONEY, $input) ? true : false;
    }

    /**
     * 验证url
     * @param  [type] $input [description]
     * @return [type]        [description]
     */
    public static function url($input)
    {
        return (filter_var($input, FILTER_VALIDATE_URL) === false) ? false : true;
    }

    /**
     * 验证手机号码
     *
     * @param string $input 手机号码
     * @return bool  验证成功后返回true失败后返回false
     */
    public static function handset($input)
    {
        return preg_match(Verify::REG_HANDSET, $input) ? true : false;
    }
    /**
     * 验证邮箱
     *
     * @param string $input 邮箱
     * @return bool  验证成功后返回true失败后返回false
     */
    public static function email($input)
    {
        return preg_match(Verify::REG_EMAIL, $input) ? true : false;
    }
}
