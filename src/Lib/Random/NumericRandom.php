<?php

namespace ZZG\ZUtil\Lib\Random;

class NumericRandom
{
    /**
     * 随机数
     * @param integer | float $min 最小值
     * @param integer | float $max 最大值
     * @param int $precision 精确小数位数
     * @return float|int
     */
    public static function random($min,$max,$precision = 0)
    {
       if ($precision == 0) {
           return self::randomInt($min,$max);
       } else {
           return self::randomFloat($min,$max,$precision);
       }
    }
    private static function randomFloat($min,$max,$precision)
    {
        $maxMinSub = bcsub((string)$max,(string)$min,$precision);
        return (float)bcadd((string)$min,bcmul($maxMinSub,(string)self::randomBaseFloat(),$precision),$precision);
    }

    private static function randomInt($min=0,$max=null)
    {
        if (function_exists('random_int')) {
            $max = $max === null ? PHP_INT_MAX : $max;
            return random_int($min,$max);
        } else {
            $max = $max === null ? mt_getrandmax() : $max;
            return mt_rand($min,$max);
        }
    }

    private static function randomBaseFloat()
    {
        if (function_exists('random_int')) {
            return bcdiv((string)random_int(0,PHP_INT_MAX),(string)PHP_INT_MAX,10);
        } else {
            return bcdiv((string)mt_rand(),(string)mt_getrandmax(),10);
        }
    }
}