<?php
namespace App\Http\Controllers\Back\Stock;

use App\Http\Controllers\Api\Controller;
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
     * @brief 查看用户
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

        //获取角色用户
        if(isset($params['role_id']) && $params['role_id']){
            $ext_api = 'role/getRoleUserList';
            $body_data = [
                'token' => $params['token'],
                'role_ids' => $params['role_id'],
            ];
            $role_users = $this->ssoPost($ext_api, $body_data);
            $user_list = json_decode($role_users->content(), true);
            if(!isset($user_list['data']['list']['0']['user_ids'])) {
                return reJson('true', '200', '操作成功', []);
            }
            $user_ids = $user_list['data']['list']['0']['user_ids'];
            if(empty($user_ids)){
                return reJson('true', '200', '操作成功', []);
            }
            unset($rule['role_id']);
            $rule['ids'] = implode(',', $user_ids);
            $params['ids'] = implode(',', $user_ids);
        }

        $ext_api = 'org/user/list';
        $body_data = $this->postData($params, $rule);

        $plat_data = $this->platformPost($ext_api, $body_data);
        $user_list = json_decode($plat_data->content(), true);
        if(!isset($user_list['data']['items']) && empty($user_list['data']['items'])) {
            return $this->platformPost($ext_api, $body_data);
        }

        $user_items = $user_list['data']['items'];
        foreach ($user_items as $user_key => $user_val) {
            $user_id = $user_val['user_id'];

            $ext_api = 'role/getUserRoles';
            $body_data = [
                'token' => $params['token'],
                'user_ids' => $user_id
            ];

            $role_list = json_decode($this->ssoPost($ext_api, $body_data)->content(), true);
            if(isset($role_list['data']['list']['0']['roles']) ){
                $role_list = $role_list['data']['list']['0']['roles'];
            } else {
                $role_list = [];
            }

            $user_list['data']['items'][$user_key]['roles'] = $role_list;
        }

        return response()->json($user_list);

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

    public function add()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID

            'org_id' => 'required|integer',//	是	string	组织id
            'login_account' => 'required|alpha_num',//	是	string	用户名
            'login_password' => 'required|alpha_num',//	是	string	密码
            'full_name' => 'required|alpha_num',//	是	string	姓名
            'emp_id' => 'required|alpha_num',//	是	string	职员id
            'role_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/',
            ],//	是	string	角色id
            'status' => 'required|in:0,1',//	是	string	状态 1启用 2禁用
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        //调用平台接口
        $ext_api = 'org/user/register';
        $body_data = $this->postData($params, $rule);

        $plat_user = $this->platformPost($ext_api, $body_data);
        $res_user = json_decode($plat_user->content(), true);

        //绑定角色
        if(!isset($res_user['data']['user_id']) || empty($res_user['data']['user_id'])) {
            return $plat_user;
        }

        $user_id = $res_user['data']['user_id'];
        $ext_api = 'role/allotUserRole';
        $body_data = [
            'token' => $params['token'],
            'user_ids' => $user_id,
            'role_ids' => $params['role_ids'],
        ];

        $bind_res = json_decode($this->ssoPost($ext_api, $body_data)->content(), true);

        if(!isset($bind_res['code']) || $bind_res['code']!='200'){
            reJson(false, '4000', '创建用户成功，绑定角色失败', ['user_id'=>$user_id]);
        } else {
            return $plat_user;
        }

    }

    public function update()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID

            'id' => 'required|alpha_num',//	是	string	被更新记录的user_id
            'emp_id' => 'required|alpha_num',//	是	string	职员id
            'org_id' => 'required|integer',//	是	string	组织id
            'full_name' => 'required|alpha_num',//	是	string	姓名
            'role_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/',
            ],//	是	string	角色id
            'status' => 'required|in:0,1',//	是	string	状态 1启用 2禁用
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/allotUserRole';
        $body_data = [
            'token' => $params['token'],
            'user_ids' => $params['id'],
            'role_ids' => $params['role_ids'],
        ];

        $bind_res = $this->ssoPost($ext_api, $body_data);
        $bind_role = json_decode($bind_res->content(), true);
        if(!isset($bind_role['code']) || $bind_role['code']!='200'){
            return $bind_res;
        }

        //调用平台接口
        $ext_api = 'org/user/edit';
        unset($rule['role_ids']);
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

    public function set_passwd()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID

            'id' => 'required|integer',//是	int 用户ID
            'login_password' => 'required|alpha_num',//是 string 密码
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        //调用平台接口
        $ext_api = 'org/user/reset/password';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }


    public function enable()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID
            'ids'=> [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ]//	是	string	职员id(多个用,号分隔)
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        //调用平台接口
        $ext_api = 'org/user/enable';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

    public function disable()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID
            'ids'=> [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ],//	是	string	用户(多个用,号分隔)
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        //调用平台接口
        $ext_api = 'org/user/disable';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

    public function delete()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer',//是	int	操作员ID
            'ids'=> [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ],//	是	string	用户id(多个用,号分隔)
        ];

        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        //调用平台接口
        $ext_api = 'org/user/del';
        $body_data = $this->postData($params, $rule);

        return $this->platformPost($ext_api, $body_data);
    }

    public function get_role()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'user_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ], //是	int	用户ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getUserRoles';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function bind_role()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ],//	是	int	角色ID
            'user_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/'
            ], //是	int	用户ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/allotUserRole';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_info()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_id' => 'required|integer', //角色ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleById';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_son()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_id' => 'required|integer', //角色ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleAndPer';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_list()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'org_id' => 'required|integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleByOrgId';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_page()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',

            'user_id' => 'integer',	//是	int	用户ID
            'currentPage' => 'integer',	//	否	int	当前页码 默认第1页
            'limit' => 'integer',	//	否	string	每页显示条数 默认第10条
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleByUserId';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_tree()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            //'org_id' => 'required|integer',
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleByOrgId';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }



    public function role_users()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/',
            ]// 是  |string | 角色ID 多个用逗号分割    |
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/getRoleUserList';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_save()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'name' => 'required|alpha_num', //是	string	角色用户名
            'remark' => 'required|alpha_num', //是	string	角色说明
            'role_id' => 'integer', //	否	int	角色ID,空则新增 非空则更新
            'parent_id' => 'integer', //	是	int	上级角色ID
            'per_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/',
            ], //	是	int	权限ID组,多个用逗号分割 示例 per_ids = 1,2,3
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/saveRole';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

    public function role_getpermission()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_id' => 'required|integer',//	是	int	角色ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'permission/getPermissionByRoleId';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }


    public function role_delete()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['token'] = $this->user_info['token'];

        $rule = [
            'token' => 'required|alpha_dash',
            'role_ids' => [
                'required',
                'regex:/^([1-9][0-9]*[,]?[0-9])+$/',
            ],//	是	int	角色ID
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $ext_api = 'role/delRole';
        $body_data = $this->postData($params, $rule);

        return $this->ssoPost($ext_api, $body_data);
    }

}
