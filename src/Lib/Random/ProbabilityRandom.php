<?php

namespace ZZG\ZUtil\Lib\Random;

use ZZG\ZUtil\Helper\ArrayHelper;

class ProbabilityRandom
{
    const RETURN_TYPE_ARRAY = 1;
    const RETURN_TYPE_STRING = 2;
    //随机计算小数位数
    private static $scale = 30;

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
    public static function random($len, $range, $option = [])
    {
        $optionHelper = new ArrayHelper($option);
        $range = self::buildRange($range);
        $res = [];
        if ($optionHelper->existAndEq('repeat',false)) {
            $rangeCount = count($range);
            if ($len > $rangeCount) {
                throw new \OverflowException('可选字符串长度不足');
            }
            while ($len--) {
                $key = static::randomCherryPick($range);
                $res[] = $range[$key]['k'];
                unset($range[$key]);
                $nRange =[];
                foreach ($range as $item) {
                    $nRange[$item['k']] = $item['v'];
                }
                $range = static::buildRange($nRange);
            }
        } else {
            while ($len--) {
                $key = static::randomCherryPick($range);
                $res[] = $range[$key]['k'];
            }
        }
        if ($optionHelper->existAndEq('return_type','array')) {
            return $res;
        } else {
            return implode('',$res);
        }
    }
    private static function buildRange($range)
    {
        $sum = 0;
        $result = [];
        foreach ($range as $val) {
            $sum = bcadd((string)$val,(string)$sum,self::$scale);
        }
        $index = 0;
        foreach ($range as $key => $val) {
            $v = bcdiv((string)$val,$sum,self::$scale);
            $lastV = isset($result[(string)($index-1)])?$result[(string)($index-1)]['v']:'0';
            $v = bcadd($v,$lastV,self::$scale);
            $result[(string)$index++] = [
                'k' => $key,
                'v' => $v
            ];
        }
        return $result;
    }

    private static function randomCherryPick($range)
    {
        $r = NumericRandom::random(0,1,self::$scale);
        $keys = array_keys($range);
        $key = static::binaryFind($keys, $r, $range);
        if ($key === false) {
            return static::randomCherryPick($range);
        }
        return $key;
    }

    private static function binaryFind($subKeys, $float, $range) {
        if (empty($subKeys)) {
            return  false;
        }
        $count = count($subKeys);
        $middleIndex = floor($count/2);
        $middleKey = $subKeys[$middleIndex];
        if (bccomp((string)$range[$middleKey]['v'] , (string)$float,self::$scale) === 1) {
            if (!isset($subKeys[$middleIndex-1])) {
                return $middleKey;
            }
            if (bccomp((string)$range[$subKeys[$middleIndex-1]]['v'], (string)$float,self::$scale) === -1) {
                return $middleKey;
            }
            return static::binaryFind(array_slice($subKeys, 0, $middleIndex), $float, $range);
        } elseif (bccomp((string)$range[$middleKey]['v'] , (string)$float,self::$scale) === 0) {
            return $middleKey;
        } else {
            return static::binaryFind(array_slice($subKeys, $middleIndex), $float, $range);
        }
    }
}