<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 访问日志
 *
 * @author
 **/
class Visit_log_model extends CI_Model 
{
	private $table = 'visit_log';
	function __construct()
	{
		parent::__construct();
		$this->rdb = getReadOnlyDB();
        $this->wdb = getWriteOnlyDB();
	}
	/**
	 * 保存日志
	 * @param $log 日志数组
	 */
    function save($log){
        $this->wdb->insert($this->table, $log);
        return $this->wdb->insert_id();
    }

    /**
     * 统计一段时间内的PV/UV/IP
     * Enter description here ...
     * @param $start
     * @param $end
     */
    function tongji($start = 0,$end = 0){
    	$where = ' Where 1 ';
    	if($start > 0){
    		$where .= ' AND v.time >= '.$start.' ';
    	}
    	if($end > 0){
    		$where .= ' AND v.time <= '.$end.' ';
    	}
    	//独立访问数UV
    	$res_uv = $this->rdb->query("SELECT COUNT( DISTINCT v.usign) as num FROM visit_log as v $where");
    	if ($res_uv->num_rows() > 0){
    		$r['uv'] = $res_uv->row()->num;
    	}else{
    		$r['uv'] = 0;
    	}
    	//pv数
    	$res_pv = $this->rdb->query("SELECT COUNT(v.id) as num FROM visit_log as v $where");
        if ($res_pv->num_rows() > 0){
            $r['pv'] = $res_pv->row()->num;
        }else{
            $r['pv'] = 0;
        }
    	//IP
    	$res_ip = $this->rdb->query("SELECT COUNT(DISTINCT v.ip) as num FROM visit_log as v $where");
        if ($res_ip->num_rows() > 0){
            $r['ip'] = $res_ip->row()->num;
        }else{
            $r['ip'] = 0;
        }
        return $r;
    }

    /**
     * 统计访问的浏览器，平台 top n
     * @param $ziduan
     * @param $limit
     */
    function getVisitTop($ziduan, $limit){
    	$sql = "SELECT COUNT(*) as num,$ziduan FROM  $this->table WHERE 1=1 GROUP BY $ziduan ORDER BY num DESC LIMIT 0, $limit";
        $query = $this->rdb->query($sql);
        return $query->result_array();
    }
    /**
     * 根据某个字段查询
     * Enter description here ...
     * @param $ziduan
     * @param $order 
     * @param $limit
     */
    function getRecord($ziduan,$order = 'DESC',$limit = 5){
        $sql = "SELECT * FROM  $this->table WHERE 1=1 ORDER BY $ziduan $order LIMIT 0, $limit";
        $query = $this->rdb->query($sql);
        return $query->result_array();
    }
    

}