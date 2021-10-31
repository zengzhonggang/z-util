<?php

namespace ZZG\ZUtil\Lib\Random;

use ZZG\ZUtil\Lib\Arr\Arr;

class AverageRandom
{
    public static function random($len, $range, $option = [])
    {
        $range = array_unique(str_split($range));
        $res = [];
        $rangeCount = count($range);
        $arrayUtil = new Arr($option);
        if ($arrayUtil->existAndEq('repeat',false)) {
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
        if ($arrayUtil->existAndEq('return_type','array')) {
            return $res;
        } else {
            return implode('',$res);
        }
    }
}