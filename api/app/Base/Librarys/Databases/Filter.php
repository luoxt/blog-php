<?php

namespace App\Base\Librarys\Databases;

/**
 * Class Filter
 * @package App\Base\Librarys\Databases
 */
class Filter
{
    /**
     * @brief 过滤字段
     * @param array $filter
     * @param $object
     * @return array|mixed
     */
    function filterParser($filter=array(), &$object)
    {
        if (!is_array($filter)) {
            return $filter;
        }

        $tPre = ('`'.$object->table_name().'`').'.';

        $where = [1];
        $qb = $object->database()->createQueryBuilder();

        $cols = array_merge($object->searchOptions(), $object->getColumns());

        // 过滤无用的filter条件
        $filter = array_where($filter, function($filterValue, $filterKey) use ($cols) {
            return !is_null($filterValue) && (isset($cols[$filterKey]) || strpos($filterKey, '|'));
        });

        foreach($filter as $filterKey => $filterValue)
        {
            if (strpos($filterKey, '|')) {
                // dd($filter);
                list($columnName, $type) = explode('|', $filterKey);
                if($type == '') $type = 'nequal';
                $where[] = $this->processSql($tPre.$columnName, $type, $filterValue, $qb);
            } else {
                $columnName = $filterKey;
                if (is_array($filterValue)) {

                    $where[] = $this->processSql($tPre.$columnName, 'in', $filterValue, $qb);
                }  else {
                    $where[] = $this->processSql($tPre.$columnName, 'nequal', $filterValue, $qb);
                }
            }
        }

        return call_user_func_array(array($qb->expr(), 'andX'), $where);
    }

    /**
     * @brief 转换成sql语句
     * @param $columnName
     * @param $type
     * @param $filterValue
     * @param $qb
     * @return mixed
     * @throws \ErrorException
     */
    private function processSql($columnName, $type, $filterValue, &$qb)
    {
        $db = $qb->getConnection();
        
        switch ($type) {
            case 'than':
                $sql = $qb->expr()->gt($columnName, $db->quote($filterValue, \PDO::PARAM_INT));
                break;
            case 'lthan':
                $sql = $qb->expr()->lt($columnName, $db->quote($filterValue, \PDO::PARAM_INT));
                break;
            case 'nequal':
            case 'tequal':
                $sql = $qb->expr()->eq($columnName, $db->quote($filterValue));
                break;
            case 'noequal':
                $sql = $qb->expr()->neq($columnName, $db->quote($filterValue));
                break;

            case 'sthan':
                $sql = $qb->expr()->lte($columnName, $db->quote($filterValue, \PDO::PARAM_INT));
                break;
            case 'bthan':
                $sql = $qb->expr()->gte($columnName, $db->quote($filterValue, \PDO::PARAM_INT));
                break;
            case 'has':
                $sql = $qb->expr()->like($columnName, $db->quote('%'.$filterValue.'%', \PDO::PARAM_STR));
                break;
            case 'head':
                $sql = $qb->expr()->like($columnName, $db->quote($filterValue.'%', \PDO::PARAM_STR));
                break;
            case 'foot':
                $sql = $qb->expr()->like($columnName, $db->quote('%'.$filterValue, \PDO::PARAM_STR));
                break;
            case 'nohas':
                $sql = $qb->expr()->notlike($columnName, $db->quote('%'.$filterValue.'%', \PDO::PARAM_STR));
                break;
            case 'between':
                $sql = $qb->expr()->andX($qb->expr()->gte($columnName, $db->quote($filterValue[0], \PDO::PARAM_INT)),
                                         $qb->expr()->lt($columnName, $db->quote($filterValue[1], \PDO::PARAM_INT)));
                break;
            case 'in':
                $filterValue = (array)$filterValue;
                if (empty($filterValue)) throw new InvalidArgumentException("filter column:{$columnName} in type, cannot empty");
                array_walk($filterValue, function(&$value) use ($qb) {
                    $value = $qb->getConnection()->quote($value);
                });
                $sql = $qb->expr()->in($columnName, $filterValue);
                break;
            case 'notin':
                $filterValue = (array)$filterValue;
                array_walk($filterValue, function(&$value) use ($qb) {
                    $value = $qb->getConnection()->quote($value);
                });
                $sql = $qb->expr()->notin($columnName, $filterValue);
                break;
            default:
                throw new \ErrorException(sprintf('column : %s dbeav filter donnot support type:%s', $columnName, $type));
        }
        return $sql;
    }
}

