<?php
/**
 * DateTimeUtil.php
 *
 * @description : 时间工具类
 *
 * @author : liaobw <liaobw@mingyuanyun.com>
 * @create date : 2019/7/23
 */

namespace core\extensions\utils;


use DateTime;

class DateTimeUtil
{
    /**
     * @return string
     */
    public static function now()
    {
        date_default_timezone_set("PRC");
        return date('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    public static function date()
    {
        date_default_timezone_set("PRC");
        return date('Y-m-d');
    }

    /**
     * 获取当天 20120102
     * @return bool|string
     */
    public static function today()
    {
        date_default_timezone_set("PRC");
        return date('Ymd');
    }

    public static function currYear()
    {
        date_default_timezone_set("PRC");
        return intval(date('Y'));
    }

    /**
     * 获取最近几年年份
     * @param $num int 数量
     * @return array
     */
    public static function currYears($num = 0)
    {
        $thisYear = DateTimeUtil::currYear();
        $currYears = [];
        for ($i = 0; $i < $num; $i++) {
            $currYears[] = $thisYear;
            $thisYear--;
        }
        return $currYears;
    }

    /**
     * 获取 *月*日
     * @return string
     */
    public static function MonthDay()
    {
        date_default_timezone_set("PRC");
        return date('m月d日');
    }

    public static function CompareDateTime($date1, $date2)
    {
        return strtotime($date1) - strtotime($date2);
    }

    public static function GetTimeSpan($date1, $date2, $unit = 'd')
    {
        $timeSpan = strtotime($date1) - strtotime($date2);
        $unit = strtolower($unit);

        if ($unit == 'd') {
            $timeSpan = $timeSpan / 60 / 60 / 24;
            $timeSpan = ceil($timeSpan);
        }

        return $timeSpan;
    }

    /**
     * @param $date mixed|string
     * @param $format string
     * @return string
     */
    public static function ConvertToString($date, $format = 'Y-m-d H:i:s')
    {
        $returnStr = "";
        try {
            if (!empty($date)) {
                if (is_string($date)) {
                    $dateTime = new DateTime($date);
                    $returnStr = date_format($dateTime, $format);
                } else {
                    $returnStr = date_format($date, $format);
                }
            }
        } catch (\Exception $ex) {
            \Yii::info($ex->getMessage());
        }
        return $returnStr;
    }

    public static function ConvertMongoDateToString($date, $format = 'Y-m-d H:i:s')
    {
        if ($date instanceof \MongoDate) {
            return date($format, $date->sec);
        } else {
            return static::ConvertToString($date, $format);
        }
    }

    public static function ConvertToDateTime($dateStr)
    {
        $date = new DateTime($dateStr);
        return $date;
    }

    public static function ConvertToMongoDate($dataStr)
    {
        return new \MongoDate(strtotime($dataStr));
    }

    /**
     * 查询基于当前系统时间某天的日期的最小时间和最大时间
     * @param int $day 基于系统时间的第几天 如：昨天 -1； 今天：0 明天 +1
     * @return array ["dayMinDate"=>'2015-06-27 00:00:00', "dayMaxDate"=>'2015-06-27 23:59:59']
     */
    public static function getDayMinAndMaxDateTime($day = 0)
    {
        //当天开始时间
        $todayDate = date('Y-m-d' . ' 00:00:00', time());
        //转换成“开始”的时间戳
        $todayStartTime = strtotime($todayDate);
        $oneday_count = 3600 * 24;  //一天有多少秒

        //当天的 0点和当天23点59分59秒
        $dayStart = $todayStartTime + $oneday_count * $day;    //当天开始日期
        $dayEnd = $todayStartTime + $oneday_count * ($day + 1) - 1;  //当天结束日期

        return ["dayMinDate" => date("Y-m-d H:i:s", $dayStart), "dayMaxDate" => date("Y-m-d H:i:s", $dayEnd)];
    }

    /**
     * 查询基于当前系统时间某天的日期的最小时间
     * @param int $day 基于系统时间的第几天 如：昨天 -1； 今天：0 明天 +1
     * @return string 如：2015-06-27 00:00:00
     */
    public static function getBaseNowMinDate($day = 0)
    {
        return static::getDayMinAndMaxDateTime($day)["dayMinDate"];
    }

    /**
     * 查询基于当前系统时间某天的日期的最大时间
     * @param int $day 基于系统时间的第几天 如：昨天 -1； 今天：0 明天 +1
     * @return string 如：2015-06-27 23:59:59
     */
    public static function getBaseNowMaxDate($day = 0)
    {
        return static::getDayMinAndMaxDateTime($day)["dayMaxDate"];
    }

    /**
     * @param int $next 基于当前系统时间的第几个月 如：上一个月：-1，当月:0 ，下一个月：+1
     * @return array
     */
    private static function getMonthFirstAndLastDate($next = 0)
    {
        $nowDate = DateTimeUtil::now();
        $nextTime = strtotime($nowDate . " " . $next . " month");

        $firstDay = date('Y-m-01 00:00:00', $nextTime);
        $lastDay = date('Y-m-d 23:59:59', strtotime("$firstDay +1 month -1 day"));

        return ["firstDate" => $firstDay, "lastDate" => $lastDay];
    }

    /**
     * 查询某月的最小日期时间
     * @param int $next 基于当前系统时间的第几个月 如：上一个月：-1，当月:0 ，下一个月：+1
     * @return string 返回格式：Y-m-01 00:00:00
     */
    public static function getMonthFirstDate($next = 0)
    {
        return DateTimeUtil::getMonthFirstAndLastDate($next)['firstDate'];
    }

    /**
     * 查询某月的最大日期时间
     * @param int $next 基于当前系统时间的第几个月 如：上一个月：-1，当月:0 ，下一个月：+1
     * @return string 返回格式：Y-m-d 23:59:59
     */
    public static function getMonthLastDate($next = 0)
    {
        return DateTimeUtil::getMonthFirstAndLastDate($next)['lastDate'];
    }

    /**
     * 查询给定日期距离当前时间还有多少天多少小时
     * @param $date
     * @return string
     */
    public static function getRemainingTime($date)
    {
        $now = strtotime(DateTimeUtil::now());
        $time = strtotime($date);
        $diff = $time - $now;

        if ($diff <= 0) {
            return "0天0小时";
        }
        $msOfHour = 60 * 60;
        $msOfDay = $msOfHour * 24;
        $diffDay = floor($diff / $msOfDay);
        $diffHour = floor(floor($diff % $msOfDay) / $msOfHour);
        return $diffDay . "天" . $diffHour . "小时";
    }

    /**
     * java的时间格式转php时间格式
     * @param $date
     * @return array|string
     */
    public static function convertTimeZone($date)
    {
        $date = preg_replace("/Z/", "", $date);
        return $date;
    }

    public static function formatDate($time)
    {
        date_default_timezone_set("PRC");
        if (is_string($time)) {
            $time = strtotime($time);
        }
        //获取今天凌晨的时间戳
        $day = strtotime(date('Y-m-d', time()));
        //获取昨天凌晨的时间戳
        $pday = strtotime(date('Y-m-d', strtotime('-1 day')));
        //获取现在的时间戳
        $nowtime = time();
        $tc = $nowtime - $time;
        if ($time < $pday) {
            if ($time > strtotime(date('Y-m-d', strtotime('-3 day')))) {
                $str = "三天内";
            } else if ($time > strtotime(date('Y-m-d', strtotime('-7 day')))) {
                $str = "一周内";
            } else if ($time > strtotime(date('Y-m-d', strtotime('-30 day')))) {
                $str = '一个月内';
            } else {
                $str = '一个月前';//date('Y-m-d', $time);
            }
        } elseif ($time < $day && $time > $pday) {
            $str = "昨天";
        } elseif ($tc > 60 * 60) {
            $str = floor($tc / (60 * 60)) . "小时前";
        } elseif ($tc > 60) {
            $str = floor($tc / 60) . "分钟前";
        } else {
            $str = "一分钟内";
        }
        return $str;

    }

    /**
     * 转化为格林尼治标准时间
     * @param $time
     * @return string
     */
    public static function gmt_iso8601($time)
    {
        $dtStr = gmdate("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    /**
     * 返回字符串的毫秒数时间戳
     * @return array|string
     */
    public static function get_total_millisecond()
    {
        $time = explode(" ", microtime());
        $time = $time[1] . (substr($time[0], 2, 3));
        return $time;
    }

    public static function getTodayRemainingTime()
    {
        return strtotime(\date('Y-m-d') . ' 23:59:59') - time();
    }

    public static function checkDateTime($time)
    {
        return (bool)strtotime($time);
    }

    public static function timeIsPass($time)
    {
        return strtotime($time) < time();
    }
}