<?php

namespace App\Http\Controllers\Api\Users;

use App\Base\Plugins\Guid\IdWork;
use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Models\Sysuser\User;

class LoginController extends Controller
{

    public $user_model = null;
    /**
     * Create a new controller instance.
     * IndexController constructor.
     * @param Request $request
     */
    public function __construct(Request $request, User $user)
    {
        parent::__construct($request);

        $this->user_model = $user;
    }

    public function index()
    {


        $IdWork = new IdWork();
        $id = $IdWork->nextId();

        echo $id;


//        $end = time();
//        echo $end.'-'.$start.'='.$end-$start; exit();


//        $params = [
//            'name' => '',
//            'email' => '6666'
//        ];
//        $rule = [
//            'name' => 'required',
//            'email' => 'required|email'
//        ];
//        if ($this->validation($params, $rule) === false) {
//            $error = $this->error();
//            return reJson(false, $error['code'], $error['message']);
//        }


//        Log::info('案例');
//        $results = DB::table('sysuser_user')->where('user_id', '=', 300070)->first();
//        debug($results);

        //$User = new Model('sysuser_user');
        $res = $this->user_model->getRow('*', ['user_id'=>'300070']);
        debug($res);
        return reJson('false', '403');
    }

    public function db()
    {

        $User = new \App\Base\Librarys\Databases\Model('sysuser_user');
        $res = $User->getRow('*', ['user_id'=>'300070']);

        $aa = $User->count(['name|has'=>'罗']);

        $user_data = app('user_data');
        debug($user_data);

        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'dbname' => env('DB_DATABASE'),
            'user' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host' => env('DB_HOST'),
            'driver' => env('DB_CONNECTION'),
        );
        $conn = DriverManager::getConnection($connectionParams, $config);

        $sql = "SELECT * FROM sysuser_user limit 2";
        $stmt = $conn->query($sql)->fetchAll();

        $queryBuilder = $conn->createQueryBuilder();
        $data = $queryBuilder
            ->select('user_id', 'user_name')
            ->from('sysuser_user')
            ->toArray();
        debug($data);


        $sm = $conn->getSchemaManager();
        $tables = $sm->listTables();
        foreach ($tables as $table) {
            echo "\n".$table->getName() . " columns:\n";
            foreach ($table->getColumns() as $column) {
                echo ' - ' . $column->getName() . "\n";
            }
        }

        //事务
//        $conn->beginTransaction();
//        try{
//            // do stuff
//            $conn->commit();
//        } catch (\Exception $e) {
//            $conn->rollBack();
//            throw $e;
//        }


    }

}
