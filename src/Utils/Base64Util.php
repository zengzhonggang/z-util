<?php

namespace ZZG\ZUtil\Utils;

class Base64Util
{
    /**
     * @param string $string
     * @param array $option
     * @return string
     */
    public static function encode( $string,array $option = [])
    {
        $string = base64_encode($string);
        if (isset($option['upset_replace']['from'],$option['upset_replace']['to'])) {
            $string = self::_upSetReplace($string,$option['upset_replace']['from'],$option['upset_replace']['to']);
        }
        return $string;
    }
    public static function decode( $string,array $option = [])
    {
        if (isset($option['upset_replace']['from'],$option['upset_replace']['to'])) {
            $string = self::_upSetReplace($string,$option['upset_replace']['to'],$option['upset_replace']['from']);
        }
        return base64_decode($string);
    }

    public static function urlSafeEncode( $string,array $option = [])
    {
        return self::_urlStringProcess(self::encode($string,$option),0);
    }

    public static function urlSafeDecode( $string,array $option = [])
    {
        return self::_urlStringProcess(self::encode($string,$option),1);
    }

    /**
     * 处理url安全的base64字符串
     * @param $base64String
     * @param int $type 0:encode;1:decode
     * @return array|mixed|string|string[]
     */
    private static function _urlStringProcess( $base64String,$type = 0)
    {
        $replace = ['+/','-_'];
        if ($type == 0) {
            $base64String = strtr($base64String,$replace[0],$replace[1]);
            $base64String = str_replace('=','',$base64String);
        } elseif ($type == 1) {
            $base64String = strtr($base64String,$replace[1],$replace[0]);
            $needPadNum = strlen($base64String)%4;
            if ($needPadNum !== 0) {
                $shouldStringLen = strlen($base64String) + (4 - $needPadNum);
                $base64String = str_pad($base64String,$shouldStringLen,'=',STR_PAD_RIGHT);
            }
        }
        return $base64String;
    }

    /**
     * 混淆替换base64字符串
     * @param string $string base64字符串
     * @param string $from 需要替换的字符串
     * @param string $to 对应被替换的字符串
     * @return string
     */
    private static function _upSetReplace($string,$from,$to)
    {
        $base64characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
        if (strlen($from) != strlen($to)) {
            throw new \UnexpectedValueException('替换字符串长度不等');
        }
        $fromArray = str_split($from);
        $toArray = str_split($to);
        $replaceArray = [];
        foreach ($fromArray as $index => $item) {
            if (isset($replaceArray[$item]) && $replaceArray[$item] != $toArray[$index]) {
                throw new \UnexpectedValueException('替换字符串from['.$item.']重复');
            }
            if (in_array($toArray[$index],$replaceArray)) {
                throw new \UnexpectedValueException('替换字符串to['.$toArray[$index].']重复');
            }
            $replaceArray[$item] = $toArray[$index];
        }
        foreach ($toArray as $item) {
            if (strpos($base64characters,$item) !== false && !isset($replaceArray[$item])) {
                throw new \UnexpectedValueException('替换字符串to字符['.$item.']没有对应的替换');
            }
        }
        return strtr($string,$from,$to);
    }
}