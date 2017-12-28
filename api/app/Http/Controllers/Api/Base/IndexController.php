<?php
namespace App\Http\Controllers\Api\Base;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

/**
 * 基础接口
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

    public function org_type()
    {
        //验证请求数据
        $params = $this->request->input();
        $rule = [];

        $ext_api = 'organization/getOrgType';
        $body_data = $this->postData($params, $rule);


        return $this->platformPost($ext_api, $body_data);
    }

    public function info()
    {


    }

}
