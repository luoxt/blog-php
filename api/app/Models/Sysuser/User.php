<?php

namespace App\Models\Sysuser;

use App\Base\Librarys\Databases\Model;

/**
 * 用户表
 */
class User extends Model
{
    public $table = 'sysuser_user';

    public $primaryKey = 'user_id';//主键

    public $incrementing = false;//是否自增主键

    public $timestamps = false;//不自动维护created_at 和 updated_at

    public $guarded = ['']; //字段批量赋值黑名单 为空表示不限制

    public function __construct()
    {
        parent::__construct($this->table);
    }
}
