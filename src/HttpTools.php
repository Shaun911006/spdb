<?php
/**
 * Author:Shaun·Yang
 * Date:2020/6/15
 * Time:上午9:12
 * Description:
 */

namespace spdb;


class HttpTools
{
    //数组转xml
    /**
     * POST请求https接口返回内容
     * @param string $url [请求的URL地址]
     * @param string $post [请求的参数]
     * @param string $contentType
     * @return  string
     */
    public static function post_curls($url, $post,$contentType = 'INFOSEC_SIGN/1.0')
    {
        $headers = array(
            "Content-type: ".$contentType,
            "Content-Length: ".strlen($post),
        );
        $curl    = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // 设置请求头
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $res = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            return '';
        }
        curl_close($curl); // 关闭CURL会话
        return $res; // 返回数据，json格式
    }
}