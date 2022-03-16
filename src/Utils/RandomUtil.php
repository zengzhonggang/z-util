<?php

namespace ZZG\ZUtil\Utils;

use ZZG\ZUtil\Lib\Random\AverageRandom;
use ZZG\ZUtil\Lib\Random\NumericRandom;
use ZZG\ZUtil\Lib\Random\ProbabilityRandom;

class RandomUtil
{
    /**
     * 概率随机
     * @param integer $len 随机长度
     * @param array $range 随机范围；[[值=>计数(int|float)]]
     * 例：[['a'=>2,'b'=>3]],a的概率为40%，b的概率为60%
     * @param array $option 其他配置参数
     * return_type:返回结果类型;ProbabilityRandom::RETURN_TYPE_ARRAY 数组，ProbabilityRandom::RETURN_TYPE_STRING 字符串
     * repeat：随机字符串是否可以重复；0：否，1：是
     * @return array|string
     */
    public static function probability($len, $range, $option = [])
    {
        return ProbabilityRandom::random($len,$range,$option);
    }

    /**
     * 平均随机
     * @param integer $len 长度
     * @param string $range 随机字符串范围
     * @param array $option 其他配置参数。
     * return_type:返回结果类型;AverageRandom::RETURN_TYPE_ARRAY 数组，AverageRandom::RETURN_TYPE_STRING 字符串
     * repeat：随机字符串是否可以重复；0：否，1：是
     * @return array|string
     */
    public static function average($len, $range, $option = [])
    {
        return AverageRandom::random($len,$range,$option);
    }

    /**
     * 随机数
     * @param integer | float $min 最小值
     * @param integer | float $max 最大值
     * @param int $precision 精确小数位数
     * @return float|int
     */
    public static function numeric($min,$max,$precision = 0)
    {
        return NumericRandom::random($min,$max,$precision);
    }

    /**
     * 随机字符串
     * @param $len
     * @param int $mode 0:数字字，1：小写字母，2：大写字母，3：随机字母，4：数字字母混合
     * @return array|string
     */
    public static function str($len,$mode = 0)
    {
        $numberString = '0123456789';
        $lowercaseAlphabetString = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseAlphabetString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        if ($mode == 0) {
            $string = $numberString;
        } elseif ($mode == 1) {
            $string = $lowercaseAlphabetString;
        } elseif ($mode == 2) {
            $string = $uppercaseAlphabetString;
        } elseif ($mode == 3) {
            $string = $uppercaseAlphabetString.$lowercaseAlphabetString;
        } elseif ($mode == 4) {
            $string = $numberString.$uppercaseAlphabetString.$lowercaseAlphabetString;
        }
        return self::average($len,$string);
    }

}