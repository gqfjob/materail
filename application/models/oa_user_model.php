<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * oa用户表
 *
 * @author
 **/
class Oa_user_model extends CI_Model 
{
    private $table = 'oa_user';
    function __construct()
    {
        parent::__construct();
        $this->rdb = getReadOnlyDB();
        $this->wdb = getWriteOnlyDB();
    }
    //模糊查询用户
    function getUsersLike($name){
        $this->rdb->like('oanick', $name);
        $this->rdb->or_like('name', $name);
        $this->rdb->or_like('tno', $name);
        $this->rdb->limit(10);
        $query = $this->rdb->get($this->table);
        return $query->result_array();
    }
    /*
     * $ids 逗号分隔的id
     */
    function getUsersIds($ids){
        $nids = explode(",", $ids);
        $this->rdb->where_in('tno', $nids);
        $query = $this->rdb->get($this->table);
        return $query->result_array();
    }
} 
