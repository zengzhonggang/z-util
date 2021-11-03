<?php


namespace ZZG\ZUtil\Utils;


class ArrayUtil
{
    /**
     * 通过使用array的值作为键和值，组建一个新的数组
     * keyField和valueField 可以使用"."指定多维数组的值
     * @param array $array 原始数组
     * @param string $keyField 作为新数组的键
     * @param string $valueField 作为新数组的值
     * @return array
     */
    public static function buildKeyValueArray(array $array, $keyField, $valueField)
    {
        $result = [];
        if (empty($keyField)) {
            throw new \UnexpectedValueException('键值字段不能为空');
        }
        $keyFieldArray = explode('.', $keyField);
        $valueFieldArray = [];
        if (!empty($valueField)) {
            $valueFieldArray = explode('.', $valueField);
        }
        foreach ($array as $val) {
            $key = $val;
            $value = $val;
            foreach ($keyFieldArray as $item) {
                if (isset($key[$item])) {
                    $key = $key[$item];
                } else {
                    throw new \UnexpectedValueException('键值字段不存在：' . $item);
                }
            }
            foreach ($valueFieldArray as $item) {
                if (isset($value[$item])) {
                    $value = $value[$item];
                } else {
                    throw new \UnexpectedValueException('值字段不存在：' . $item);
                }
            }
            if (is_string($key) || is_numeric($key)) {
                $result[$key] = $value;
            } else {
                throw new \UnexpectedValueException('键值字段的值不能作为数组键');
            }
        }
        return $result;
    }

    /**
     * 指定数组的值作为数组的键
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function useValueAsKey(array $array, $key)
    {
        return self::buildKeyValueArray($array, $key, '');
    }

    /**
     * 用户自定义排序
     * @param $array
     * @param string $sortKey 排序的字段
     * @param array $sortArray 自定排序数组
     * @return array
     */
    public static function customSort($array, $sortKey, $sortArray)
    {
        $len = count($array);
        $array = array_values($array);
        if ($len > 1) {
            for ($i = 0; $i < $len - 1; $i++) {
                for ($j = $i + 1; $j < $len; $j++) {
                    if (array_search($array[$i][$sortKey], $sortArray) === false || array_search($array[$i][$sortKey], $sortArray) > array_search($array[$j][$sortKey], $sortArray)) {
                        list($array[$i], $array[$j]) = [$array[$j], $array[$i]];
                    }
                }
            }
        }
        return $array;
    }

    /**
     * 过滤数组中特定值
     * @param $array
     * @param string | array $filterValue 要过滤的值
     * @param bool $ignoreValueType 是否验证值类型
     * @return array
     */
    public static function filterValue($array, $filterValue, $ignoreValueType = true)
    {
        if (!is_array($filterValue)) {
            $filterValue = [$filterValue];
        }
        return array_filter($array, function ($v) use ($filterValue, $ignoreValueType) {
            return !in_array($v, $filterValue, !$ignoreValueType);
        });
    }

    /**
     * 多维数组排序
     * @param $array
     * @param string $filed 排序字段，用“.”指定多维数组字段
     * @param int $flag
     * @return mixed
     */
    public static function multiDimensionalSort($array, $filed, $flag = SORT_ASC)
    {
        $filedArray = explode('.', $filed);
        $flag = $flag == SORT_DESC ? 1 : -1;
        $sortFunc = function ($first, $next) use ($filedArray, $flag) {
            $firstValue = $first;
            $nextValue = $next;
            foreach ($filedArray as $value) {
                if (!isset($firstValue[$value]) || !isset($nextValue[$value])) {
                    throw new \OutOfRangeException('数组不存在键值：' . $value);
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
        uasort($array, $sortFunc);
        return $array;
    }

    /**
     * 从数组中挑选符合条件的数组
     * @param array $arr
     * @param string $field 条件键值
     * @param string $opt 条件符号
     * @param mixed $value 条件值
     * @return array
     */
    public static function cherryPick(array $arr, $field, $opt, $value)
    {
        $result = array_filter($arr, function ($item) use ($field, $opt, $value) {
            $res = false;
            if (isset($item[$field])) {
                switch ($opt) {
                    case '==':
                        $res = $item[$field] == $value;
                        break;
                    case '===':
                        $res = $item[$field] === $value;
                        break;
                    case '>':
                        $res = $item[$field] > $value;
                        break;
                    case '>=':
                        $res = $item[$field] >= $value;
                        break;
                    case '<':
                        $res = $item[$field] < $value;
                        break;
                    case '<=':
                        $res = $item[$field] <= $value;
                        break;
                    case '<>':
                        $res = $item[$field] <> $value;
                        break;
                }
            }
            return $res;
        });
        return array_values($result);
    }

    /**
     * 组建树形数组
     * @param array $array 原始数组
     * @param string $masterKey
     * @param string $slaveKey
     * @return array
     */
    public static function buildTree($array, $masterKey, $slaveKey)
    {
        $treeIdKey = [];
        $tree = [];
        foreach ($array as $item) {
            isset($item[$masterKey]) && $treeIdKey[$item[$masterKey]] = $item;
        }
        foreach ($treeIdKey as $value) {
            if (isset($value[$slaveKey])) {
                if (isset($treeIdKey[$value[$slaveKey]])) {
                    $treeIdKey[$value[$slaveKey]]['nodes'][] = &$value;
                } else {
                    $tree[] = &$value;
                }
            }
        }
        unset($treeIdKey);
        return $tree;
    }

    /**
     * 分解多维数据键值
     * @param string $key 键值组合字符串
     * @return array
     */
    public static function decomposeDepthArrayKey($key)
    {
        $keys = [];
        $splitFunc = function ($key,$step = 0) use (&$splitFunc,&$keys) {
            $index = strpos($key,'.',$step);
            if ($index === false) {
                $keys[] = $key;
            } elseif ($index-1 >=0 && $key[$index-1] == '\\') {
                $splitFunc($key,++$step);
            } else {
                $k = substr($key,0,$index);
                $key = str_replace($k.'.','',$key);
                $keys[] = $k;
                $splitFunc($key);
            }
        };
        $splitFunc($key);
        return $keys;
    }
}