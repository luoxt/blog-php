<?php
namespace App\Http\Controllers\Back\Stock;

use App\Http\Controllers\Back\BackController;
use Illuminate\Http\Request;

/**
 * 股票管理
 * @package App\Http\Controllers\Api\Organize
 */
class IndexController extends BackController
{
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $app = app();
        $app['user_info'] = ['user_id' => '','org_id' => ''];
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

        $rule = [
            'stock_code' => 'required|alpha_num', //	是	string	user_id
        ];
        if ($this->validation($params, $rule) === false) {
            $error = $this->error();
            return reJson(false, $error['code'], $error['message']);
        }

        $stock_code = $params['stock_code'];
        $stock_head = substr($stock_code, 0, 1);
        $he = '';
        switch ($stock_head){
            case '6':
                $he = 'sh';
                break;
            case '3':
                $he = 'sz';
                break;
            case  '0':
                $he = 'sz';
                break;
            default;
        }
        $stock_new = $he.$stock_code;

        $ext_api = 'http://hq.sinajs.cn/rn=i56ex&list='.$stock_new;

        $api_data = iconv('GB2312', 'UTF-8', trim(file_get_contents($ext_api)));


        debug(explode(',', $api_data));

    }


}
