<?php

namespace ZZG\ZUtil\Lib\Random;

use ZZG\ZUtil\Lib\Arr\Arr;

class ProbabilityRandom
{
    private static $scale = 30;
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
    public static function random($len, $range, $option = [])
    {
        $optionUtil = new Arr($option);
        $range = self::buildRange($range);
        $res = [];
        if ($optionUtil->existAndEq('repeat',false)) {
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
        if ($optionUtil->existAndEq('return_type','array')) {
            return $res;
        } else {
            return implode('',$res);
        }
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