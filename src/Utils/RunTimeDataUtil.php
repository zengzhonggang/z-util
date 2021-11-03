<?php


namespace ZZG\ZUtil\Utils;


use ZZG\ZUtil\Helper\ArrayHelper;

class RunTimeDataUtil
{
    private static $temp;

    /**
     * 获取存储数组
     * @return ArrayHelper
     */
    private static function getStore()
    {
        if (!(self::$temp instanceof ArrayHelper)) {
            self::$temp = new ArrayHelper();
        }
        return self::$temp;
    }

    /**
     * 获取临时数组
     * @param string $key 临时数据键值
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get($key,$default = false)
    {
        if (self::has($key)) {
            return self::getStore()->offsetGet($key);
        }
        return $default;
    }

    /**
     * 设置临时数据
     * @param string $key 键
     * @param mixed $data 数据
     */
    public static function set($key,$data)
    {
        self::getStore()->offsetSet($key,$data);
    }

    /**
     * 删除临时数据
     * @param $key
     */
    public static function del($key)
    {
        self::getStore()->offsetUnset($key);
    }

    /**
     * 检查是否存在指定键值的临时数据是否存在
     * @param string $key 键
     * @return bool
     */
    public static function has($key)
    {
        return self::getStore()->offsetExists($key);
    }

    /**
     *  魔术方法获取临时数据，数据不存在时，通过匿名函数创建数据。
     * @param $key
     * @param \Closure $createFunc 如果不存在，创建值的方法
     * @return false|mixed
     */
    public static function magicGet($key,\Closure $createFunc){
        if (!self::has($key)) {
            self::set($key,$createFunc());
        }
        return self::get($key);
    }

}