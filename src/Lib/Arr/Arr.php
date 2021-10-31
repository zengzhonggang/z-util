<?php

namespace ZZG\ZUtil\Lib\Arr;

class Arr
{
    private $arr;
    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }

    public function existAndEq($key,$eqValue)
    {
        return isset($this->arr[$key]) && $this->arr[$key] == $eqValue;
    }

    public function values()
    {
        return array_values($this->arr);
    }

    public function keys()
    {
        return array_keys($this->arr);
    }

    public function count()
    {
        return count($this->arr);
    }
    public function del($key,$refresh=false)
    {
        unset($this->arr[$key]);
        if ($refresh) {
            $this->arr = array_values($this->arr);
        }
    }
}