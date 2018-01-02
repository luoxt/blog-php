<?php
namespace App\Http\Controllers\Api\Front\Home;

use App\Http\Controllers\Api\Front\Controller;
use Illuminate\Http\Request;

/**
 * @biref 首页
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
     * @brief 首页
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //验证请求数据
        $params = $this->request->input();
        $params['user_id'] = $this->user_id;

        $rule = [
            'user_id' => 'required|integer', //	是	string	user_id
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }


        return reJson(false, 200, 'ok');

    }

}
