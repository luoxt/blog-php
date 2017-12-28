<?php
namespace App\Http\Controllers\Api\Base;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

use App\Base\Plugins\Upload\Common;

/**
 * 上传接口
 * @package App\Http\Controllers\Api\Organize
 */
class UploadController extends Controller
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

    public function images()
    {
        $file = $this->request->file('tty_img');

        // 文件是否上传成功
        if ($file->isValid()) {

            // 获取文件相关信息
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $size = $file->getClientSize();   //大小
            $type = $file->getClientMimeType();     // image/jpeg
            if(!in_array($type, ['image/jpeg', 'image/png', 'image/gif'])){
                return reJson(false, 4000, '图片类型错误，只支持jpeg，png，gif ！');
            }

            if($size/1024>200){
                return reJson(false, 4000, '图片大小不能超过200k！');
            }

            //上传文件
            $filename = date('Ymd').'/'.uniqid().'.'.$ext;
            return $this->upimg($filename, $file);

        }else{
            return reJson(false, 4000, '上传图片无效');
        }
    }

    protected function upimg($object, $file)
    {
        $path = "images/";
        $object = $path . $object;

        $bucket = Common::getBucketName();
        $ossClient = Common::getOssClient();
        if (is_null($ossClient)) {
            return reJson(false, 4000, '上传图片无效');
        }

        $options = array();
        try {
            $ossClient->multiuploadFile($bucket, $object, $file, $options);
        } catch (OssException $e) {
            return reJson(false, 4000, $e->getMessage());
        }

        $data = [
            'show_url'=>env('IMG_SHOW_URL').$object,
            'save_url' =>'/'.$object,
        ];

        return reJson(true, 200, '上传图片成功', $data);

    }

}
