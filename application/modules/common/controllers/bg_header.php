<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_Bg_Header_module extends CI_Module {

    /**
     * 构造函数
     *
     * @return void
     * @author
     **/
    function __construct()
    {
        parent::__construct();
    }

    function index($arr=array(),$metas=array())
    {
       
    	$this->load->model('material_model','material');
    	$this->load->model('user_model','user');
    	$this->load->model('visit_log_model','visit_log');
    	 
    	$total_material = $total_user = $total_visit = $total_download = 0;
    	//查询素材总数
    	$total_material_query = $this->material->get_total_material();
    	if($total_material_query['status'])
    	{
    		$total_material = $total_material_query['total'];
    	}
    	
    	//查询用户总数
    	$total_user_query = $this->user->getTotalUser();
    	if($total_user_query['status'])
    	{
    		$total_user = $total_user_query['total'];
    	}
    	 
    	//查询访问总数
    	$total_visit_query = $this->visit_log->getTotal();
    	if($total_visit_query['status'])
    	{
    		$total_visit = $total_visit_query['total'];
    	}
    	
    	//查询下载总数
    	$total_download_query = $this->visit_log->getTotal(0,0,3);
    	if($total_download_query['status'])
    	{
    		$total_download = $total_download_query['total'];
    	}
    	$data = array();
    	$data['admin_name'] = '管理员';
    	//自定义标题，描述等
        if(is_string($arr)){
            $data['title'] = $arr;
        }else if(is_array($arr)){
            if(isset($arr['title']))
            {
               $data['title'] = $arr['title'];
            }else{
               $data['title'] = '';
            }
            if(isset($arr['description']))
            {
               $data['description'] = $arr['description'];
            }else{
               $arr['description'] = '';
            }
            if(isset($arr['keywords'])){
                 $data['keywords'] = $arr['keywords'];
            }else{
                $data['keywords'] = '';
            }
            if(isset($arr['cur'])){
                $data['cur'] = $arr['cur'];
            }
        }
        $data['total_material'] = $total_material;
        $data['total_user'] = $total_user;
        $data['total_visit'] = $total_visit;
        $data['total_download'] = $total_download;
        
        $this->load->view('bg_header',$data);
    }
}