<?php

namespace core\extensions\utils;

class StringUtil
{
    /**
     * 截取字符指定长度
     * @param $str
     * @param $len
     * @param string $fill
     * @return string
     */
    public static function subStr($str, $len, $fill = '…')
    {
        if (empty($str)) {
            return $str;
        }

        $strLen = mb_strlen($str);
        if ($strLen > $len) {
            $str = mb_substr($str, 0, $len, 'utf-8') . $fill;
        }

        return $str;
    }

    /*
     * 使用正则表达式拆分字符串，默认使用中英文空格、逗号进行拆分
     * @param $str
     * @return array
     */
    public static function splitString($str, $pattern = '/[\s,　、]+/u')
    {
        $words = [];
        $arr = preg_split($pattern, trim($str));
        foreach ($arr as $i => $word) {
            if (!empty($word)) {
                array_push($words, $word);
            }
        }
        return $words;
    }

    /*
     * 高亮字符串
     * @param $str
     * @param $keys
     * @param $prefix
     * @param $suffix
     * @return string
     */
    public static function highlightString($str, $keys, $prefix = '<em class="search_highlight">', $suffix = '</em>')
    {
        if (empty($str)) {
            return '';
        }
        if (empty($keys)) {
            return $str;
        }

        $result = $str;
        foreach ($keys as $word) {
            $word = static::escapeSolrQueryChars($word);
            $result = preg_replace("'" . $word . "'", $prefix . $word . $suffix, $result);
        }
        $result = preg_replace("'" . $suffix . $prefix . "'", '', $result);

        return $result;
    }

    /*
     * 分割高亮字段，将高亮部分提前显示
     * @param string $str
     * @param string $mark
     * @param int $maxPos
     * @param int $maxLen
     * @return string
     */
    public static function splitHlField($str, $mark = '<em', $maxPos = 20, $maxLen = 50)
    {
        if (empty($str)) {
            return '';
        }

        // 得到第一次出现高亮标记的位置
        $firstFlagIndex = mb_stripos($str, $mark, 0, 'utf-8');
        if ($firstFlagIndex < $maxPos) {  // 没有高亮或高亮字符串在范围之内则直接返回
            return $str;
        }

        $subStrEndIndex = mb_strlen($str, 'utf-8');
        // 从高亮标记位置截取到字符末尾，如果不够 $maxLen 则再向前取相应长度的字符
        $withoutHtmlStr = static::replaceHtml(mb_substr($str, $firstFlagIndex, $subStrEndIndex - $firstFlagIndex, 'utf-8'));
        $prefixShowLen = $maxLen - mb_strlen($withoutHtmlStr, 'utf-8');
        $prefixShowLen = $prefixShowLen < 0 ? 0 : $prefixShowLen;

        $subStrStartIndex = ($firstFlagIndex >= $prefixShowLen) ? $firstFlagIndex - $prefixShowLen : 0;
        $prefix = mb_substr($str, $subStrStartIndex, $firstFlagIndex - $subStrStartIndex, 'utf-8');

        return ($subStrStartIndex == 0 ? '' : '...') . $prefix . mb_substr($str, $firstFlagIndex, $subStrEndIndex - $firstFlagIndex, 'utf-8');
    }

    /*
     * 替换 HTML 标签
     */
    public static function replaceHtml($str)
    {
        if (empty($str)) {
            return '';
        }
        return preg_replace('/<[^>]*>/i', '', $str);
    }

    /**
     * 剪切html字符串
     *
     * @param $str
     * @param $maxLen
     * @return string
     */
    public static function cutHtmlStr($str, $maxLen)
    {
        //$str = mb_convert_encoding($str, "utf-8");
        $end = '...';
        $result = '';
        $n = 0;
        $isCode = false; // 是不是HTML代码
        $isHTML = false; // 是不是HTML特殊字符,如&nbsp;
        for ($i = 0; $i < mb_strlen($str); $i++) {
            $char = mb_substr($str, $i, 1, 'utf-8');
            if ($char == '<') {
                $isCode = true;
            } elseif ($char == '&') {
                $isHTML = true;
            } elseif ($char == '>' && $isCode) {
                $n = $n - 1;
                $isCode = false;
            } elseif ($char == ';' && $isHTML) {
                $isHTML = false;
            }
            if (!$isCode && !$isHTML) {
                $n = $n + 1;
            }
            $result = $result . $char;
            if ($n >= $maxLen) {
                break;
            }
        }
        // 取出截取字符串中的HTML标记
        $temp_result = preg_replace('/(>)[^<>]*(<?)/', '$1$2', $result);
        // 去掉不需要结素标记的HTML标记
        $temp_result = preg_replace('#</?(AREA|BASE|BASEFONT|BODY|BR|COL|COLGROUP|DD|DT|FRAME|HEAD|HR|HTML|IMG|INPUT|ISINDEX|LI|LINK|META|OPTION|P|PARAM|TBODY|TD|TFOOT|TH|THEAD|TR|area|base|basefont|body|br|col|colgroup|dd|dt|frame|head|hr|html|img|input|isindex|li|link|meta|option|p|param|tbody|td|tfoot|th|thead|tr)[^<>]*/?>#', '', $temp_result);
        // 去掉成对的HTML标记
        $temp_result = preg_replace('#<([a-zA-Z]+)[^<>]*>(.*?)</\\1>#', '$2', $temp_result);

        // 用正则表达式取出标记
        if (preg_match('#<([a-zA-Z]+)[^<>]*>#', $temp_result, $m)) {
            foreach ($m as $item) {
                $result = $result . '</' . $item . '>';
            }
        }
        // 结尾添加...
        if (mb_strlen(preg_replace('/<[^>]*>/', '', $str)) > $maxLen) {
            $result = $result . $end;
        }
        return $result;
    }

    /**
     * 剪切普通字符串
     *
     * @param $str
     * @param $length
     * @param string $ellipsis
     * @return string
     */
    public static function cutStr($str, $length, $ellipsis = '...')
    {
        $cutStr = mb_substr($str, 0, $length, 'utf-8');
        if (mb_strlen($str, 'utf-8') == mb_strlen($cutStr, 'utf-8')) {
            return $cutStr;
        }
        return $cutStr . $ellipsis;
    }

    public static function getFirstLetter($str)
    {
        $fchar = ord($str{0});
        if ($fchar >= ord('A') and $fchar <= ord('z')) {
            return strtoupper($str{0});
        }

        $str = iconv('UTF-8', 'gb2312', $str);//如果程序是gbk的，此行就要注释掉
        if (preg_match('/^[\x7f-\xff]/', $str)) {
            $a = $str;
            $val = ord($a{0}) * 256 + ord($a{1}) - 65536;
            if ($val >= -20319 and $val <= -20284) return 'A';
            if ($val >= -20283 and $val <= -19776) return 'B';
            if ($val >= -19775 and $val <= -19219) return 'C';
            if ($val >= -19218 and $val <= -18711) return 'D';
            if ($val >= -18710 and $val <= -18527) return 'E';
            if ($val >= -18526 and $val <= -18240) return 'F';
            if ($val >= -18239 and $val <= -17923) return 'G';
            if ($val >= -17922 and $val <= -17418) return 'H';
            if ($val >= -17417 and $val <= -16475) return 'J';
            if ($val >= -16474 and $val <= -16213) return 'K';
            if ($val >= -16212 and $val <= -15641) return 'L';
            if ($val >= -15640 and $val <= -15166) return 'M';
            if ($val >= -15165 and $val <= -14923) return 'N';
            if ($val >= -14922 and $val <= -14915) return 'O';
            if ($val >= -14914 and $val <= -14631) return 'P';
            if ($val >= -14630 and $val <= -14150) return 'Q';
            if ($val >= -14149 and $val <= -14091) return 'R';
            if ($val >= -14090 and $val <= -13319) return 'S';
            if ($val >= -13318 and $val <= -12839) return 'T';
            if ($val >= -12838 and $val <= -12557) return 'W';
            if ($val >= -12556 and $val <= -11848) return 'X';
            if ($val >= -11847 and $val <= -11056) return 'Y';
            if ($val >= -11055 and $val <= -10247) return 'Z';
        } else {
            return false;
        }

        return '';
    }

    public static function htmlToText($text)
    {
        // 将</p>和</div>替换为换行标签<br/>
        $text = preg_replace('<</p.*?>>', '<br/>', $text);
        $text = preg_replace('<</div.*?>>', '<br/>', $text);
        //移除HTML标记，保留<br/>标签
        $text = strip_tags($text, '<br>');
        $text = htmlspecialchars_decode($text);
        return $text;
    }

    /**
     * 转义solr查询时的特殊字符
     * @param $query
     * @return mixed
     */
    public static function escapeSolrQueryChars($query)
    {
        $luceneReservedCharacters = preg_quote('+-&|!(){}[]^"~*?:\\');
        $query = preg_replace_callback(
            '/([' . $luceneReservedCharacters . '])/',
            function ($matches) {
                return '\\' . $matches[0];
            },
            $query);
        return $query;
    }


    public static function rand($min = 0, $max = 100)
    {
        return rand($min, $max);
    }

    public static function htmlToTextarea($text)
    {
        $result = static::htmlToText($text);
        $result = str_replace('<br/>', '', $result);
        $result = str_replace('<br>', '', $result);
        return $result;
    }

    /*
     * 去除字符串/r/n
     */
    public static function strReplaceJsonRN($str)
    {
        $str = str_replace('\\r\\n', '', $str);
        $str = str_replace('\\r', '', $str);
        $str = str_replace('\\n', '', $str);
        return $str;
    }

    /**
     * 36位GUID
     * @return string
     */
    public static function uuid()
    {
        list($usec, $sec) = explode(' ', microtime(false));
        $usec = (string)($usec * 10000000);
        $timestamp = bcadd(bcadd(bcmul($sec, '10000000'), (string)$usec), '621355968000000000');
        $ticks = bcdiv($timestamp, 10000);
        $maxUint = 4294967295;
        $high = bcdiv($ticks, $maxUint) + 0;
        $low = bcmod($ticks, $maxUint) - $high;
        $highBit = (pack('N*', $high));
        $lowBit = (pack('N*', $low));
        $guid = str_pad(dechex(ord($highBit[2])), 2, '0', STR_PAD_LEFT) . str_pad(dechex(ord($highBit[3])), 2, '0', STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[0])), 2, '0', STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[1])), 2, '0', STR_PAD_LEFT) . '-' . str_pad(dechex(ord($lowBit[2])), 2, '0', STR_PAD_LEFT) . str_pad(dechex(ord($lowBit[3])), 2, '0', STR_PAD_LEFT) . '-';
        $chars = 'abcdef0123456789';
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= '-';
        for ($i = 0; $i < 4; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }
        $guid .= '-';
        for ($i = 0; $i < 12; $i++) {
            $guid .= $chars[mt_rand(0, 15)];
        }

        return $guid;
    }


    /**
     * @param $str
     * @return int
     */
    public static function chkChinese($str)
    {
        return preg_match('/[\x80-\xff]./', $str);
    }

    /**
     * json字符串转化成数组，如果是则返回数组，否则返回false
     * @param $str
     * @return bool|mixed
     */
    public static function jsonStr2Arr($str)
    {
        if (!is_string($str) || is_numeric($str)) {
            return false;
        }
        $data = json_decode($str, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $data;
        }
        return false;
    }

    public static function strUnderline2SmallHump($str, $isSmall = true)
    {
        $strArr = explode('_', $str);
        $newStr = array_map(function ($v, $k) use ($isSmall) {
            if ($k != 0 && $isSmall) {
                return $v;
            }
            return ucfirst($v);
        }, $strArr);
        return implode('', $newStr);
    }

    /**
     * 下划线转驼峰
     * 思路:
     * step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     */
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
     * 下划线转驼峰
     * 驼峰命名转下划线命名
     * 思路:
     * 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     */
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    public static function replaceTemplateData(&$content, $params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $item) {
                $search = [
                    '${' . $key . '}',
                    '{' . $key . '}',
                    '{$' . $key . '}',
                ];
                is_array($item) && $item = implode(',', $item);
                $content = str_replace($search, $item, $content);
            }
        }
    }
}
