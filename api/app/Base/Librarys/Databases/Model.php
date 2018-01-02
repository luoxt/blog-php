<?php

namespace App\Base\Librarys\Databases;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException as UniqueConstraintViolationException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException as NotNullConstraintViolationException;

/**
 * Class Model
 * @brief 调用Doctrine
 * @package App\Base\Librarys\Databases
 */
class Model
{
    public $dbschema = null;
    public $database = null;
    protected $defaultOrder='';

    public function __construct($table_name)
    {
        if($table_name){
            $this->table = $table_name;
        }
        if(!$this->database){
            $this->database = $this->database();
        }
    }

    /**
     * @brief 连接数据库
     * @return \Doctrine\DBAL\Connection
     */
    public function database()
    {
        $connectionParams = array(
            'dbname' => env('DB_DATABASE'),
            'user' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host' => env('DB_HOST'),
            'driver' => 'pdo_mysql',
        );
        $config = new Configuration();
        return DriverManager::getConnection($connectionParams, $config);
    }

    /**
     * @brief 获取字段
     * @return array
     */
    public function getColumns()
    {
        $sm = $this->database->getSchemaManager();
        $getColumns = $sm->listTableColumns($this->table);

        $column_array = [];
        foreach ($getColumns as $column) {
            $column_array[$column->getName()] = $column;
        }

        return $column_array;
    }

    /**
     * @brief 获取表名称
     * @param string $table_name
     * @return string
     */
    public function table_name($table_name='')
    {
        if($table_name){
            $this->table = $table_name;
        }

        return $this->table;
    }

    /**
     * @brief 统计条数
     * @param null $filter
     * @return bool|string
     */
    public function count($filter=null)
    {
        $total = $this->database()->createQueryBuilder()
            ->select('count(*) as _count')->from($this->table_name())->where($this->doFilter($filter))
            ->execute()->fetchColumn();

        return $total;
    }

    /**
     * @brief 获取列表
     * @param string $cols
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @param null $orderBy
     * @return array
     */
    public function getList($cols='*', $filter=array(), $offset=0, $limit=-1, $orderBy=null)
    {
        if ($filter == null) {
            $filter = array();
        }
        if (!is_array($filter)) {
            throw new \InvalidArgumentException('filter param not support not array');
        }

        $offset = (int)$offset<0 ? 0 : $offset;
        $limit = (int)$limit < 0 ? 100000 : $limit;
        $orderBy = $orderBy ? $orderBy : $this->defaultOrder;

        $qb = $this->database()->createQueryBuilder();
        $qb->select($cols)
            ->from($this->table_name())
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $qb->where($this->doFilter($filter));

        //统一orderby的写法. 目前同时支持array和string
        if ($orderBy) {
            $orderBy = is_array($orderBy) ? implode(' ', $orderBy) : $orderBy;
            array_map(function($o) use (&$qb){
                $permissionOrders = ['asc', 'desc', ''];
                @list($sort, $order) = explode(' ', trim($o));
                if (!in_array(strtolower($order), $permissionOrders)  ) {
                    throw new \InvalidArgumentException("getList order by do not support {$order} ");
                }
                $qb->addOrderBy($qb->getConnection()->quoteIdentifier($sort), $order);
            }, explode(',', $orderBy));
        }

        $stmt = $qb->execute();
        $data = $stmt->fetchAll();
        //$this->tidy_data($data, $cols);

        return $data;
    }

    /**
     * @brief 获取单条记录
     * @param string $cols
     * @param array $filter
     * @param null $orderType
     * @return array|mixed
     */
    function getRow($cols='*', $filter=array(), $orderType=null)
    {
        $data = $this->getList($cols, $filter, 0, 1, $orderType);
        if($data){
            return $data['0'];
        }else{
            return $data;
        }
    }

    /**
     * @brief 过滤
     * @param array $filter
     * @return mixed
     */
    public function doFilter($filter = array())
    {
        if ($filter == null) {
            $filter = array();
        }

        $filter_obj = new \App\Base\Librarys\Databases\Filter();
        return $filter_obj->filterParser($filter, $this);
    }

    /**
     * @brief 检测inser条数据, 是否有必填数据没有处理(调试中...)
     * @param $data
     */
    public function checkInsert($data)
    {
        foreach($this->getColumns() as $columnName => $columnDefine)
        {
            if(!isset($columnDefine['default']) && $columnDefine['required'] && $columnDefine['autoincrement']!=true)
            {
                // 如果当前没有值, 那么抛错
                if(!isset($data[$columnName]))
                {
                    throw new \InvalidArgumentException(($columnDefine['label']?$columnDefine['label']:$columnName).app::get('base')->_('不能为空！'));
                }
            }
        }
    }

    /**
     * @brief 插入记录（调试中....）
     * @param $data
     * @return bool|null|string
     */
    public function insert(&$data)
    {
        $this->checkInsert($data);
        $prepareUpdateData = $this->prepareInsertData($data);
        $qb = $this->database()->createQueryBuilder();

        $qb->insert($this->database()->quoteIdentifier($this->table_name()));

        array_walk($prepareUpdateData, function($value, $key) use (&$qb) {
            $qb->setValue($key, $qb->createPositionalParameter($value));
        });

        try {
            $stmt = $qb->execute();
        } catch (UniqueConstraintViolationException $e) {
            // 主键重
            return false;
        }

        $insertId = $this->lastInsertId($data);
        if ($this->idColumnAutoincrement)
        {
            $data[$this->idColumn] = $insertId;
        }

        return isset($insertId) ? $insertId : true;
    }

    /**
     * @brief 获取lastInsertId （调试中....）
     * @param null $data
     * @return null|string
     */
    public function lastInsertId($data = null)
    {
        if ($this->idColumnAutoincrement) {
            $insertId = $this->database()->lastInsertId();
        } else {
            if (!is_array($this->idColumn)) {
                $insertId = isset($data[$this->idColumn]) ? $data[$this->idColumn] : null;
            } else {
                $insertId = null;
            }
        }
        return $insertId;
    }

    /**
     * @brief 删除记录 （调试中....）
     * @param $filter
     * @return bool
     */
    public function delete($filter)
    {
        $qb = $this->database()->createQueryBuilder();
        $qb->delete($this->database()->quoteIdentifier($this->table_name()))
            ->where($this->doFilter($filter));

        return $qb->execute() ? true : false;
    }

    /**
     * @brief 更新记录 （调试中....）
     * @param $data
     * @param $filter
     * @param null $mustUpdate
     * @return bool|\Doctrine\DBAL\Driver\Statement|int
     */
    public function update($data, $filter, $mustUpdate=null)
    {
        if (count((array)$data)==0) {
            return true;
        }
        $prepareUpdateData = $this->prepareUpdateData($data);
        $qb = $this->database()->createQueryBuilder();
        $qb->update($this->database()->quoteIdentifier($this->table_name()))
            ->where($this->doFilter($filter));

        array_walk($prepareUpdateData, function($value, $key) use (&$qb) {
            $qb->set($key, $qb->createPositionalParameter($value));
        });
        $stmt = $qb->execute();

        return $stmt>0 ? $stmt : true;
    }

    /**
     * @brief 保存记录 （调试中....）
     * @param $dbData
     * @param null $mustUpdate
     * @param bool $mustInsert
     * @return bool|\Doctrine\DBAL\Driver\Statement|int|null|string
     */
    final public function save(&$dbData,$mustUpdate=null, $mustInsert=false)
    {
        // 默认方式为
        $doMethod = 'update';
        $filter = array();

        // 如果save数据中主键为空, 则改方式为insert
        // todo: 如果是多主键的时候会有bug
        foreach( (array)$this->idColumn as $idv ){
            if( !$dbData[$idv] ){
                $doMethod = 'insert';
                break;
            }
            // 组织filter
            // 将要保存数据中的主键对应值取出, 做为filter的一个条件
            $filter[$idv] = $dbData[$idv];
        }

        // 如果非强制insert 并且 save方式为update 并且 能找到相关记录, 那么进行update
        if(!$mustInsert && $doMethod == 'update' && $a = $this->getRow(implode(',',(array)$this->idColumn), $filter))
        {
            return $this->update($dbData,$filter,$mustUpdate);
        }

        // 否则insert数据
        return $this->insert($dbData);
    }

    public function searchOptions()
    {
        return [];
    }

    /**
     * @brief 处理数据 （调试中....）
     * @param $rows
     * @param string $cols
     * @return mixed
     */
    public function tidy_data(&$rows, $cols='*')
    {
        if($rows) {

            // 目前不支持 字段别名
            $useColumnKeys = array_keys($rows[0]);
            debug($useColumnKeys);
            $columnDefines = $this->getColumns();

            foreach($useColumnKeys as $columnKey)
            {
                $columnType = $columnDefines[$columnKey]['type'];

//                if ($func = kernel::single('base_db_datatype_manage')->getDefineFuncOutput($columnType))
//                {
//                    array_walk($rows, function(&$row, $func) use ($func, $columnKey){
//                        $row[$columnKey] = call_user_func($func, $row[$columnKey]);
//                    });
//                }
            }

            return $rows;
        }
    }
}
