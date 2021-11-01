<?php


namespace ZZG\ZUtil\Helper;


class ArrayHelper extends \ArrayIterator
{
    /**
     * 多维数组排序
     * @param string $filed 排序字段，用“.”指定多维数组字段
     * @param int $flag
     * @return void
     */
    public function multiDimensionalSort($filed,$flag = SORT_ASC)
    {
        $filedArray = explode('.',$filed);
        $flag = $flag == SORT_DESC ? 1 : -1;
        $sortFunc = function ($first,$next) use ($filedArray,$flag) {
            $firstValue = $first;
            $nextValue = $next;
            foreach ($filedArray as $value) {
                if (!isset($firstValue[$value]) || !isset($nextValue[$value])) {
                    throw new \OutOfRangeException('数组不存在键值：'.$value);
                }
                $firstValue = $firstValue[$value];
                $nextValue = $nextValue[$value];
            }
            if ($firstValue == $nextValue) {
                return 0;
            }
            if ($firstValue > $nextValue) {
                return -$flag;
            } else {
                return $flag;
            }
        };
        $this->uasort($sortFunc);
    }

    /**
     * 判断数组是否存在指定键值，并且定于指定的值
     * @param $key
     * @param $eqValue
     * @return bool
     */
    public function existAndEq($key,$eqValue)
    {
        return $this->offsetExists($key) && $this->offsetGet($key) == $eqValue;
    }
}