<?php

namespace ZZG\ZUtil\Lib\Random;

use ZZG\ZUtil\Helper\ArrayHelper;

class AverageRandom
{
    const RETURN_TYPE_ARRAY = 1;
    const RETURN_TYPE_STRING = 2;
    /**
     * 平均随机
     * @param integer $len 长度
     * @param string $range 随机字符串范围
     * @param array $option 其他配置参数。
     * return_type:返回结果类型;AverageRandom::RETURN_TYPE_ARRAY 数组，AverageRandom::RETURN_TYPE_STRING 字符串
     * repeat：随机字符串是否可以重复；0：否，1：是
     * @return array|string
     */
    public static function random($len, $range, $option = [])
    {
        $range = array_unique(str_split($range));
        $res = [];
        $rangeCount = count($range);
        $arrayHelper = new ArrayHelper($option);
        if ($arrayHelper->existAndEq('repeat',false)) {
            if ($rangeCount < $len) {
                throw new \OverflowException('可选字符串长度不足');
            }
            while ($len--) {
                $index = NumericRandom::random(0,$rangeCount);
                $res[] = $range[$index];
                unset($range[$index]);
                $range = array_values($range);
                $rangeCount--;
            }
        } else {
            while ($len--) {
                $index = NumericRandom::random(0,$rangeCount);
                $res[] = $range[$index];
            }
        }
        if ($arrayHelper->existAndEq('return_type',self::RETURN_TYPE_ARRAY)) {
            return $res;
        } else {
            return implode('',$res);
        }
    }
}