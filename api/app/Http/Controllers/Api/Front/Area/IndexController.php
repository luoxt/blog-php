<?php
namespace App\Http\Controllers\Api\Area;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

/**
 * 地区管理
 * @package App\Http\Controllers\Api\Organize
 */
class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $app = app();
        $app['user_info'] = [
            'user_id' => '',
            'org_id' => ''
        ];

        parent::__construct($request);
    }

    /**
     * @brief 下级区域
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'area_id' => 'required|integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'area/subArea';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

    /**
     * @brief 区域信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info()
    {
        //验证请求数据
        $params = $this->request->input();

        $rule = [
            'area_id' => 'required|integer|min:1',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'area/info';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

}
