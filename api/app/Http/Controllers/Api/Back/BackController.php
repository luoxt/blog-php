<?php

namespace App\Http\Controllers\Back;

use Log;
use App\Base\Plugins\Validators\Validate;
use Laravel\Lumen\Routing\Controller as BaseController;

class BackController extends BaseController
{
    use Validate;

    protected $request = null;
    protected $user_info = [];
    protected $user_id = 0;
    protected $org_id = 0;

    public function __construct($request)
    {
        $this->request = $request;

        $this->user_info = app('user_info');
        $this->user_id = $this->user_info['user_id'];
        $this->org_id = $this->user_info['org_id'];
    }

    public function request($params)
    {
        return $this->request->input($params);
    }

    public function postData($params, $rule)
    {
        $body_data = [];
        $post_params = array_intersect_key($params, $rule);
        foreach ($post_params as $pkey => $pval) {
            if($pval!=''){
                $body_data[$pkey] = $pval;
            }
        }
        return $body_data;
    }

    public function platformPost($ext_api, $body_data)
    {
        try{

            $api_data = platformPost($ext_api, $body_data);
            //debug($api_data, $ext_api);

            //数据判断
            if(!isset($api_data['ret'])){
                return reJson('false', 404, '平台接口数据错误', $api_data);
            }

            //请求失败
            if ($api_data['ret'] != '200' || !isset($api_data['data']['code'])){
                return reJson('false', $api_data['ret'], $api_data['msg'], $api_data);
            }

            //操作失败
            if($api_data['data']['code']!='0'){
                return reJson('false', $api_data['data']['code'], $api_data['data']['msg'],$api_data);
            }

            //成功
            return reJson('true', '200', $api_data['data']['msg'], $api_data['data']['info']);

        } catch (\Exception $exception) {

            $code = $exception->getCode();
            $msg = $exception->getMessage();
            return reJson(false, $code, $msg);
        }
        Log::info('调用平台接口');

        return reJson('false', '400', '平台接口返回出错');
    }

    public function ssoPost($ext_api, $body_data)
    {
        try{
            $api_data = ssoPost($ext_api, $body_data);

            if(!isset($api_data['code'])){
                return reJson(false, '400', 'token无效或格式出错', $api_data);
            }

            if(!isset($api_data['result'])){
                return reJson(false, '400', '接口返回格式出错！', $api_data);
            }

            if($api_data['code']!=200){
                return reJson(false, $api_data['code'], $api_data['msg'], $api_data);
            }

            //成功
            return reJson('true', '200', '操作成功', $api_data['result']);

        } catch (\Exception $exception) {

            $code = $exception->getCode();
            $msg = $exception->getMessage();
            return reJson(false, $code, $msg);
        }
        Log::info('调用登录接口');

        return reJson('false', '400', '登录接口返回出错');
    }


}
