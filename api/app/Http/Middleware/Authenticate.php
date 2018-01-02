<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //获取参数
        $token = $request->input('token');
        $url = $request->input('_url');

        if(!$token){
            return reJson(false, '400', '请求参数错误！');
        }

        //调用登录接口
        $ext_api = 'authorization/';
        $body_data = [
            'token' => $token,
            //'sign' => $url,
        ];

        $api_data = ssoPost($ext_api, $body_data);

        if(!isset($api_data['code']) ||  $api_data['code']!=200){
            return reJson(false, '400', 'token无效或过期');
        }

        if(!isset($api_data['result']['userInfo']) || empty($api_data['result']['userInfo'])){
            return reJson(false, '400', '用户信息出错！');
        }

        $userInfo = $api_data['result']['userInfo'];
        $user_id = $userInfo['user_id'];
        $org_id = $userInfo['org_id'];
        $emp_id = $userInfo['emp_id'];
        $full_name = $userInfo['full_name'];
        $mobile = $userInfo['mobile'];
        $last_login_time = $userInfo['last_login_time'];

        //用户数据
        //访问：app('user_info')
        $app = app();
        $app['user_info'] = [
            'token' => $token,
            'user_id' => $user_id,
            'org_id' => $org_id,
            'user_name' => $full_name
        ];

        return $next($request);
    }
}
