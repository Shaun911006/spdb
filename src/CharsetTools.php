<?php
/**
 * Author:Shaun·Yang
 * Date:2020/6/16
 * Time:上午11:47
 * Description:
 */

namespace spdb;


class CharsetTools
{
    public static function gbkToUtf8($str){
        $charset = mb_detect_encoding($str,array('UTF-8','GBK','GB2312'));
        $charset = strtolower($charset);
        if('cp936' == $charset){
            $charset='GBK';
        }
        if("utf-8" != $charset){
            $str = iconv($charset,"UTF-8//IGNORE",$str);
        }
        return $str;
    }

    public static function utf8ToGbk($str){
        $charset = mb_detect_encoding($str,array('UTF-8','GBK','GB2312'));
        $charset = strtolower($charset);
        if('cp936' == $charset){
            $charset='GBK';
        }
        if("GBK" != $charset){
            $str = iconv($charset,"gbk//TRANSLIT",$str);
        }
        return $str;
    }
    public static function utf8ToGb2312($str){
        $charset = mb_detect_encoding($str,array('UTF-8','GBK','GB2312'));
        $charset = strtolower($charset);
        if("cp936" != $charset){
            $str = iconv($charset,"gb2312//TRANSLIT",$str);
        }
        return $str;
    }
}