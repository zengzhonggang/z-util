<?php


namespace ZZG\ZUtil\Utils;


class DateUtil
{
    /**
     * 友好时间格式
     * @param string | integer $time 时间戳
     * @return false|string
     */
    public static function timestampToFriendlyFormat($time)
    {
        $time = $time === NULL || $time > time() ? time() : intval($time);
        $timestampDifference = time() - $time; //时间差 （秒）
        $yearDifference = date('Y', $time) - date('Y', time());//是否跨年
        $monthDifference = (date('m', $time) - date('m', time()))+$yearDifference*12;//是否月
        $oneHourTimestamp = 60*60;
        $oneDayTimestamp = $oneHourTimestamp*24;
        $oneWeekTimestamp = $oneDayTimestamp*7;
        switch ($timestampDifference) {
            case $timestampDifference == 0:
                $text = '刚刚';
                break;
            case $timestampDifference < 60:
                $text = $timestampDifference . '秒前'; // 一分钟内
                break;
            case $timestampDifference < $oneHourTimestamp:
                $text = floor($timestampDifference / 60) . '分钟前'; //一小时内
                break;
            case $timestampDifference < $oneDayTimestamp:
                $text = floor($timestampDifference / $oneHourTimestamp) . '小时前'; // 一天内
                break;
            case $timestampDifference < $oneDayTimestamp * 3:
                $text = ($time - strtotime(date("Y-m-d",strtotime("-1 day")))) > 0 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
                break;
            case $timestampDifference >= $oneDayTimestamp * 3 && $timestampDifference < $oneWeekTimestamp:
                $text = '三天前'; //三天前
                break;
            case $timestampDifference >= $oneWeekTimestamp && $timestampDifference < $oneWeekTimestamp*2:
                $text = '一周前'; //三天前
                break;
            case $timestampDifference >= $oneWeekTimestamp*2 && $timestampDifference < $oneWeekTimestamp*3:
                $text = '两周前'; //三天前
                break;
            case $timestampDifference >= $oneWeekTimestamp*3 && $timestampDifference < $oneWeekTimestamp*4:
                $text = '三周前'; //三天前
                break;
            case $yearDifference==0 && $monthDifference ==0 :
                $text = date('m月d日', $time); //一个月内
                break;
            case $yearDifference == 0:
                $text = date('m月d日', $time); //一年内
                break;
            default:
                $text = date('Y年m月d日', $time); //一年以前
                break;
        }

        return $text;
    }

    /**
     * 时间差
     * @param integer | string $time 时间戳
     * @param integer | string $contrastTime 对比时间戳
     * @param string $contrastType 时间差类型；s:秒;i:分;h:时;w:周;m:月;y:年
     * @return float|int|string
     */
    public static function timestampDifference($time,$contrastTime,$contrastType = 'd')
    {
        switch ($contrastType) {
            case 'i':
                $difference = ($time - $contrastTime)/60;
                break;
            case 'h':
                $difference = ($time - $contrastTime)/(60*60);
                break;
            case 'd':
                $difference = ($time - $contrastTime)/(60*60*24);
                break;
            case 'w':
                $difference = ($time - $contrastTime)/(60*60*24*7);
                break;
            case 'm':
                $yearDifference = date('y',$time)-date('y',$contrastTime);
                $difference = (date('m',$time)-date('m',$contrastTime)) + $yearDifference*12;
                break;
            case 'y':
                $yearDifference = date('y',$time)-date('y',$contrastTime);
                $difference = (date('m',$time)-date('m',$contrastTime)) + $yearDifference*12;
                $difference = $difference/12;
                break;
            default:
            case 's':
                $difference = $time - $contrastTime;
                break;
        }
        return $difference;
    }
}