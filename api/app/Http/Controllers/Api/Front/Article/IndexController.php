<?php
namespace App\Http\Controllers\Api\Front\Article;

use App\Http\Controllers\Api\Front\Controller;
use Illuminate\Http\Request;

/**
 * 文章管理
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
        parent::__construct($request);
    }

    /**
     * @brief 文章列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer', //	是	string	user_id

            'role_id' => 'integer', //	否	string	角色ID
            //'ids' => ['regex:/^([1-9][0-9]*[,]?[0-9])+$/'],
            'org_id' => 'required|integer', //	是	string	当前组织id
            'page' => 'integer', //	否	string	页数
            'status' => 'in:0,1', //	否	string	状态 1开启 0关闭
            'keyword' => 'alpha_num', //	否	string	搜索关键字（可搜索手机号、工号、名字）
            'limit' => 'integer', //	否	string	每页条数 默认10
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }


        return reJson(false, 200, 'ok');

    }

    public function info()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer', //	是	string	user_id
            'id' => 'required|integer', //	是	string	当前组织id
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'org/user/info';
        $body_data = $this->postData($params, $rule);

        $plat_data = $this->platformPost($ext_api, $body_data);
        $user_list = json_decode($plat_data->content(), true);
        if(!isset($user_list['code']) || $user_list['code']!=200){
            return $plat_data;
        }

        //获取角色
        $ext_api = 'role/getUserRoles';
        $body_data = [
            'token' => $params['token'],
            'user_ids' => $params['id']
        ];
        $role_list = json_decode($this->ssoPost($ext_api, $body_data)->content(), true);
        if(isset($role_list['data']['list']['0']['roles']) ){
            $role_list = $role_list['data']['list']['0']['roles'];
        } else {
            $role_list = [];
        }

        $user_list['data']['roles'] = $role_list;

        return response()->json($user_list);

    }

}
