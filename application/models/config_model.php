<?php

/*
* @File:        Config_model.php
* @Description: 动态表相关数据处理
* @copyright:   Copyright (c) 2013, zhangyang All Rights Reserved.
* @author       zhangyang (zhangyang1122@126.com)
* @date         2013-9-24
*/
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    
class Config_model extends CI_Model
{
    
    function __construct()
    {
        parent :: __construct();
        $this->load->database();
    }
   /*
	* @Description: 写入数据库表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-9-24
	* @param  : $model_name:表名
	* @param  : $data：字段对应的各值  数组格式
	* @param  : $status:为空不返回true,为true返回id;
	*/
    public function insert_model($model_name,$data,$status=''){

        $this->db->insert($model_name, $data);
        if($status){
          return $this->db->insert_id();
        }else{
        	return true;
        }
       // $sql = "INSERT INTO identity_user (username,nick_name)VALUES (".$this->db->escape($title).", ".$this->db->escape($nick_name).")";
       // Produces: INSERT INTO mytable (title, name, date) VALUES ('{$title}', '{$name}', '{$date}')
    }
   /*
	* @Description: 根据关键字段删除表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-12-28
	* @param  : $key_values:格式,array(id=>'1',name=>'you')
	* @param  : $model：表名
	* @param  : $action: where只要包含这个字段就可以删除
	*/
    public function del_model($model_name,$where=array(),$action='pid'){
        $wh = '';
        if(sizeof($where) > 0 && is_array($where)){
            foreach($where as $key3=>$val3){
                if($wh == ''){
                    $wh = ' where '.$key3.'='.$val3;
                }else{
                    $wh = $wh.' and '.$key3.'='.$val3;
                }
            }
        }
        if($wh == ''){
            return false;
        }
        if(strpos($wh,$action) === false){
            return false;
        }
        return $this->db->query("delete from $model_name $wh");
    
    }
   /*
	* @Description: 根据关键字段查找表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-1-20
	* @param  : $key:字段名
	* @param  : $value：对应值
	* @param  : $select：为*查询所有，反之则对应字段
	* @param  : $model：表名
	* @param  : $status:为more返回多条数据；为空则返回一条数据;
	*/
    public function getModelsBykey($key,$value,$select='*',$model='',$status='',$orderBy=FALSE, $seq="ASC")
    {
    	//$query = $this->db->query('SELECT * FROM identity_user where '.$key.'='.$value);
    	if((isset($key)&& $key !== '') && (isset($value) && $value !=='')){
    		$sql = "SELECT $select FROM $model where `$key` = '$value'";
    	}else{
    		$sql = "SELECT $select FROM $model ";
    	}
    	if($orderBy){
    		$sql = $sql.' '. " order by $orderBy $seq";
    	}
    	$query = $this->db->query($sql);
    	if($status == 'more'){
    	    return $query->result_array();
    	}else{
           return $query->row();
    	}
    }
    /*
	* @Description: 根据多关键字段查找表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-9-24
	* @param  : $key_values:格式,array(id=>'1',name=>'you')
	* @param  : $model_name：表名
	* @param  : $status:为more返回多条数据；为空则返回一条数据;
	*/
    public function getModelsBykeyarray($key_values,$model_name,$status='')
    {
    	$this->db->select('*');
		$this->db->from($model_name);
		$this->db->where($key_values);
		$query = $this->db->get();
        if($status == 'more'){
    	    return $query->result_array();
    	}else{
           return $query->row();
    	}
		//return $query->row_array();
    }
   /*
	* @Description: 根据关键字段修改表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-1-30
	* @param  : $key:字段名
	* @param  : $value：对应值
	* @param  : $model_name:表名
	* @param  : $data：字段对应的各值  数组格式
	*/
    public function updateModelBykey($key,$value,$model_name,$data)
    {
    	$this->db->where( $key , $value );
        return $this->db->update($model_name, $data);
    }
   /*
	* @Description: 根据多关键字段修改表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-1-30
	* @param  : $key_values:格式,array(id=>'1',name=>'you')
	* @param  : $model_name:表名
	* @param  : $data：字段对应的各值  数组格式
	*/
    public function updateModelBykeyarray($key_values,$model_name,$data)
    {
    	$this->db->where($key_values);
        return $this->db->update($model_name, $data);
    }
   /*
	* @Description: 根据多关键字删除
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-1-30
	* @param  : $key_values:格式,array(id=>'1',name=>'you')
	* @param  : $model_name:表名
	* @param  : $data：字段对应的各值  数组格式
	*/
    public function deleteModelBykeyarray($key_values,$model_name)
    {
    	$this->db->where($key_values);
        return $this->db->delete($model_name);
    }
   /*
	* @Description:根据多关键字查找表个数
	* @date     	2013-10-16
	* @param  : $key_values:格式,array(id=>'1',name=>'you')
	* @param  : $model_name:表名
	*/
    public function countBykeyarray($key_values,$model_name)
    {
    	$this->db->select('count(*) AS num');
		$this->db->from($model_name);
		$this->db->where($key_values);
		$query = $this->db->get();
		return $query->row();
    }
    /*
	* @Description:修改个数
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-10-15
	* @param  : $status：1:+;0:-;
	*/
    function updateCount($status,$where=array(),$nums=array(),$model)
    {
        $wh = '';
        if(sizeof($where) > 0 && is_array($where)){
            foreach($where as $key3=>$val3){
                if($wh == ''){
                    $wh = ' where '.$key3.'='.$val3;
                }else{
                    $wh = $wh.' and '.$key3.'='.$val3;
                }
            }
        }
        $set = '';
        if(sizeof($nums) > 0 && is_array($nums)){
        	if($status == 1){
	            foreach($nums as $key=>$val){
	                if($set == ''){
	                    $set = $key.'='.$key.'+'.$val;
	                }else{
	                   $set = ','.$key.'='.$key.'+'.$val;
	                }
	            }
        	}else{
        	    foreach($nums as $key=>$val){
	                if($set == ''){
	                    $set = $key.'='.$key.'-'.$val;
	                }else{
	                   $set = ','.$key.'='.$key.'-'.$val;
	                }
	            }
        	}
        }
      
       $sql = "update $model set $set $wh";
       return $this->db->query($sql);
    }
    /*
	* @Description: 根据in和多关键字段查找表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-11-26
	* @param  : $ins:格式,array(status=>'1,2',id=>'1,2')
	* @param  : $where:格式,array(id=>'1',name=>'you')
	* @param  : $model_name：表名
	* @param  : $more:为more返回多条数据；为空则返回一条数据;
	*/
    function getModelByIns($ins=array('status'=>'1,2'),$where=array(),$model='',$more='')
    {
        
        $in = '';
        if(sizeof($ins) > 0 && is_array($ins)){
            foreach($ins as $key=>$val){
                if($in == ''){
                    $in = $key.' in('.$val.')';
                }else{
                    $in = ' and '.$key.' in('.$val.')';
                }
            }
        }
        $wh = '';
        if(sizeof($where) > 0 && is_array($where)){
            foreach($where as $key3=>$val3){
                if($wh == ''){
                	if($in){
                      $wh = ' and '.$key3.'="'.$val3.'"';
                	}else{
                		$wh = ' '.$key3.'="'.$val3.'"';
                	}
                }else{
                    $wh = $wh.' and '.$key3.'="'.$val3.'"';
                }
            }
        }
        $query = $this->db->query("SELECT * FROM $model WHERE $in $wh");
        if($more == 'more'){
          return $query->result_array();
        }else{
        	return $query->row();
        }
    }
   /*
	* @Description: 根据in和多关键字段查找表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-11-26
	* @param  : $ins:格式,array(status=>'1,2',id=>'1,2')
	* @param  : $where:格式,array(id=>'1',name=>'you')
	* @param  : $model_name：表名
	* @param  : $more:为more返回多条数据；为空则返回一条数据;
	*/
    function getModelByNotIns($notins=array('status'=>'1,2'),$where=array(),$model='',$more='',$sorts=array(),$offset=0,$per_page=10,$ins=array())
    {
        
        $in = '';
        if(sizeof($ins) > 0 && is_array($ins)){
            foreach($ins as $key=>$val){
                if($in == ''){
                    $in = $key.' in('.$val.')';
                }else{
                    $in = ' and '.$key.' in('.$val.')';
                }
            }
        }
        $notin = '';
        if(sizeof($notins) > 0 && is_array($notins)){
            foreach($notins as $key=>$val){
                if($notin == ''){
                	if($in){
                       $notin = ' and '.$key.' not in('.$val.')';
                	}else{
                	   $notin = $key.' not in('.$val.')';
                	}
                }else{
                    $notin = ' and '.$key.' not in('.$val.')';
                }
            }
        }
        $wh = '';
        if(sizeof($where) > 0 && is_array($where)){
            foreach($where as $key3=>$val3){
                if($wh == ''){
                	if($notin || $in){
                      $wh = ' and '.$key3.'="'.$val3.'"';
                	}else{
                		$wh = ' '.$key3.'="'.$val3.'"';
                	}
                }else{
                    $wh = $wh.' and '.$key3.'="'.$val3.'"';
                }
            }
        }
        $order = '';
        if(sizeof($sorts) > 0 && is_array($sorts)){
            foreach($sorts as $key2=>$val2){
                if($order == ''){
                    $order = $key2.' '.$val2;
                }else{
                    $order = $order.','.$key2.' '.$val2;
                }
            }
        }
        if($order){
            $order = ' ORDER BY '.$order;
        }
        $query = $this->db->query("SELECT * FROM $model WHERE $in $notin $wh $order LIMIT $offset,$per_page");
        if($more == 'more'){
          return $query->result_array();
        }else{
        	return $query->row();
        }
    }
    /**
     * 
     * 返回model的全部记录
     * @param $model
     * @param $order 排序标准
     * @param $seq 倒序还是正序
     * @param $re
     */
    function getAll($model,$order, $seq='ASC', $res='array'){
    	$query = $this->db->query("SELECT * FROM $model ORDER BY $order $seq");
    	if($res == 'array'){
    		return $query->result_array();
    	}else{
    		return $query->result();
    	}
    }
   /*
	* @Description: 根据关键字段查找列表
	* @author   	ZhangYang (542736039@qq.com)
	* @date     	2013-01-10
	* @param  : $wheres数组：查询条件 key为字段valeu为值tg $wheres['status']=0;$wheres['type']=1;
	* @param  : $sorts数组：排序 tg $sorts['create_at']='DESC';$sorts['zhici_num']='DESC';create_at为字段名,'DESC'为降序
	* @param  : $offset:从第几条开始  tg:$offset=1
	* @param  : $per_page:到第几条开始  tg:$per_page=10
	* @param  : $$where_more:额外的sql条件语句
	*/
    function getLists($model='',$where=array(),$sorts=array(),$offset=0,$per_page=1,$more='',$where_more='')
    {   
    	$order = '';
        if(sizeof($sorts) > 0 && is_array($sorts)){
            foreach($sorts as $key2=>$val2){
                if($order == ''){
                    $order = $key2.' '.$val2;
                }else{
                    $order = $order.','.$key2.' '.$val2;
                }
            }
        }
        if($order){
            $order = 'ORDER BY '.$order;
        }
        $wh = '';
        if(sizeof($where) > 0 && is_array($where)){
            foreach($where as $key3=>$val3){
                if($wh == ''){
                    $wh = ' where '.$key3.'='.$val3;
                }else{
                    $wh = $wh.' and '.$key3.'='.$val3;
                }
            }
        }
        if(strlen($where_more)>0)
        {
        	$wh = $wh.' and '.$where_more.' ';
        }
        $query = $this->db->query("SELECT * FROM $model bp $wh $order LIMIT $offset,$per_page");
        if($more == 'more'){
          return $query->result_array();
        }else{
        	return $query->row();
        }
    }
    /**
     * 统计model记录数
     * 
     * @param $model
     */
    function countAll($model){
        $this->db->select('count(*) AS num');
        $this->db->from($model);
        $query = $this->db->get();
        $res = $query->row();
        return $res->num;
    }
    /**
     * 批量添加记录
     * 
     */
    function insert_batch($model, $data){
        return $this->db->insert_batch($model, $data); 
    }
}