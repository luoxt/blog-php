<?php

/**
 * 调试方法
 * @author luoxt
 * @date 2017-9-13 15:56:17
 */
if(!function_exists('debug')){
    function debug(...$args)
    {
        echo '<pre>';print_r($args);exit;
    }
}
/**
 * @brief 多返回值
 */
if ( ! function_exists('remulti') )
{
    function reMulti(...$param)
    {
        return $param;
    }
}

/**
 * 返回Json格式数据方法
 * @param  boolean $status [成功/失败]
 * @param  string  $code   [code状态码]
 * @param  string  $msg    [消息]
 * @param  array   $data   [数据]
 * @return [json]          [description]
 */
if(!function_exists('reJson')){
    function reJson($status = true, $code = '', $msg = '', $data = [], $headerCode = 200)
    {
        $code_msg = '';
        if (!empty($code)) {
            $StatusCode = config('config.StatusCode');
            $code_msg = isset($StatusCode[$code]) ? $StatusCode[$code] : '';
        }
        if (is_array($msg) && !array_filter($msg)) {
            $msg = '';
        }else if (is_string($msg) && empty(trim($msg))) {
            $msg = $code_msg;
        }

        $res = [
            'status' => $status,
            'code'   => intval($code),
            'msg'    => $msg,
            'data'    => $data,
        ];
        return response()->json($res, $headerCode);
    }
}

/**
 * 判断是否json格式（此方法必须在PHP7以上版本使用）
 * @param  [string] $string []
 * @return [bool]       []
 * @author zicai
 * @date 2017-8-14 16:12:09
 */
if(!function_exists('isJson')){
    function isJson($string)
    {
    	if(is_numeric($string)){
    		return false;
    	}
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('ssoPost')) {
    function ssoPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //url
        $base_url = env('SSO_API_HOST');
        $url = $base_url.$url;

        //body
        $base_body = [];
        $body = array_merge($base_body,$body);

        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }
}

/**
 * POST请求
 * 文档说明：https://github.com/php-curl-class/php-curl-class
 * @author zicai
 * @date 2017-7-20 14:29:20
 */
if (!function_exists('platformPost')) {
    function platformPost($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $curl = new \Curl\Curl();

        //apikey
        $key_name = env('PLATFORM_KEY_NAME');
        $key_value = env('PLATFORM_KEY_VALUE');
        $base_header = [$key_name=>$key_value];

        $header = array_merge($base_header,$header);

        //url
        $base_url = env('PLATFORM_API_HOST');
        $url = $base_url.$url;

        //body
        $base_body = [];
        $body = array_merge($base_body,$body);

        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url, $body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;

        if ($curl->error) {
            $msg = 'CURL Platform Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Platform Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array
        $res = json_decode(json_encode($curl->response), true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);

        return $res;
    }
}

if (!function_exists('requestGet')) {
    function requestGet($url = '', array $header = [], $timeout = 10)
    {
        $res = [];
        $curl = new \Curl\Curl();
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->get($url);

        $post_options['url'] = $url;
        $post_options['header'] = $header;

        if ($curl->error) {
            $msg = 'CURL Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response),true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);
        return $res;
    }
}

/**
 * POST请求
 * 文档说明：https://github.com/php-curl-class/php-curl-class
 * @author zicai
 * @date 2017-7-20 14:29:20
 */
if (!function_exists('requestPOST')) {
    function requestPOST($url = '', array $body = [], array $header = [], $timeout = 10)
    {
        $res = [];
        $curl = new Curl();
        $curl->setHeaders($header);
        $curl->setConnectTimeout($timeout);
        $curl->post($url,$body);

        $post_options['url'] = $url;
        $post_options['header'] = $header;
        $post_options['body'] = $body;

        if ($curl->error) {
            $msg = 'CURL Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            $msg .= json_encode($post_options) . "\n";
        } else {
            $msg = 'CURL Success: ' . "\n" . json_encode($post_options) . "\n";
        }

        //strClass to array, don`t del this code. --zicai
        $res = json_decode(json_encode($curl->response),true);

        $msg .= 'result:'.json_encode($res);
        app()['Log']::debug($msg);
        return $res;
    }
}

/**
 * @brief 将一个字符串拼接至一个不包含自身的字符串中
 * @author luoxt
 * @date 2017-08-04
 */
if( !function_exists('str_append'))
{
    function str_append($string, $appendStr)
    {
        if($string === '*'){
            return $string;
        }
        $str_arr = explode(',',$string);
        $append_arr = explode(',',$appendStr);

        //合并去重
        $arr = array_merge($str_arr, $append_arr);
        $arr = array_unique(array_filter($arr));

        return $arr;
    }
}

/**
 * @brief 根据传入的数组和数组中值的键值，将对数组的键进行替换
 * @param array $array
 * @param string $key
 */
if ( ! function_exists( 'array_bind_key' ) )
{
    function array_bind_key($array, $key )
    {
        foreach( (array)$array as $value )
        {
            if( !empty($value[$key]) )
            {
                $k = $value[$key];
                $result[$k] = $value;
            }
        }
        return $result;
    }
}
