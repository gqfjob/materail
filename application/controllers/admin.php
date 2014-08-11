<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model','user');
        $this->load->helper('cookie');
        $this->load->model('config_model');
        $this->check_admin();
    }
    /*
     * 检查是否为管理员
    */
    public function check_admin()
    {
    	$uripath = uri_string();
    	if($uripath == 'admin')$uripath = 'admin/index';
    	$NOT_ACTION = 'admin/login';
    	if(strpos($NOT_ACTION,$uripath)=== false){
    		$user = checklogin();
    		if($user &&(is_array($user))){
	    		$right = checkAdminRight($user);
	    		if(!$right){
	    			if($this->input->is_ajax_request()){
	    				//当前请求是ajax
	    				echo RST('',100002,'您无操作权限，请用管理员帐号登录');
	    			}else{
	    				show_error("您无操作权限，请用管理员帐号登录",500,"出错了");
	    			}
	    			exit;
	    		}
    		}else{
    			redirect(base_url('user/login/?callback='.urlencode(base_url($uripath))));
    			exit(0);
    		}
    	}
    		
    }
    /**
     * 后台首页
     */
    public function index()
    {
    	/*
    	$user = checklogin();
    	if($user){
    		if(checkAdminRight($user)){//正常登录
    			
    		}else{
    			show_error("您无后台管理权限",500,"出错啦");
    		}
    	}else{
    		//未登录
    		redirect(base_url('user/login/?callback='.urlencode(base_url('admin/index'))));
    		exit(0);
    	}*/
    	$data['bg_left'] = $this->load->module("common/bg_left",array(1),true);

    	$this->load->module("common/bg_header");
    	$this->load->view("admin/index",$data);
    	$this->load->module("common/bg_footer");
    }

    /**
    * 素材管理
     */
     public function mgMaterial($page = 1)
     {
     	$page = (int) $page;
     	
     	$this->load->model('material_model', 'material');
     	$this->load->model('user_model', 'user');
     	
		$search = $this->input->get('search', TRUE);
     	$search = trim(urldecode($search));
     	
     	//查询总数
     	$total = 0;
     	$total_query = $this->material->get_total_material($search);
     	if($total_query['status'])
     	{
     		$total = $total_query['total'];
     	}
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	if($search)
     	{
     		$config['suffix'] = '?search=' . urlencode($search);
     		$config['first_url'] = base_url('admin/mgMaterial/1?search=' . urlencode($search));
     	}
     	$config['base_url'] = base_url('admin/mgMaterial');
		$config['total_rows'] = $total;
		$config['per_page'] = 20; 
		
     	//查询素材
     	$material_query = $this->material->get_all_materials($page, $config['per_page'], $search);
     	if( ! $material_query['status'])
     	{
     		show_error("数据库查询出错了",500,"出错了");
     	}
     	
     	$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$pages =  $this->pagination->create_links();
		
     	$uids = $mids = $vids = array();
     	foreach($material_query['materials'] as $material)
     	{
     		$uids[] = $material['uid'];
     		$mids[] = $material['id'];
     		$vids[] = $material['cversion'];
     	}
     	
     	//查询用户信息
     	$users = array();
     	if( ! empty($uids))
     	{
     		$users = $this->user->batchGetUser($uids);
     	}
     	
     	//查询素材当前版本信息
     	$versions = array();
     	if( ! empty($vids))
     	{
     		$versions_query = $this->material->get_batch_verisions($vids);
     		if($versions_query['status'])
     		{
     			$versions = $versions_query['versions'];
     		}
     	}
     	
     	//查询附件数
     	$attachment_num = array();
     	if( ! empty($mids))
     	{
	     	$attachment_num_query = $this->material->get_material_attachments($mids);
     		if($attachment_num_query['status'])
     		{
     			$attachment_num = $attachment_num_query['attachment_num'];
     		}
     	}
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(2),true);
     	
     	$data['materials'] = $material_query['materials'];
     	$data['pages'] = $pages;
     	$data['users'] = $users;
     	$data['versions'] = $versions;
     	$data['attachment_num'] = $attachment_num;
     	if(isset($_GET['search']))
     	{
     		$data['search'] = $search;
     	}
     	
    
     	$this->load->module("common/bg_header");
    	$this->load->view("admin/material",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      * 批量设置素材状态
      */
     public function set_material_status()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
     	$post['mids'] = trim($post['mids']);
     	$post['status'] = (int) $post['status'];
     	if(empty($post['mids']))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	$set_query = $this->material->batch_set_status(explode(',',$post['mids']), $post['status']);
     	
     	if($set_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     	}
     	else
     	{
     		echo json_encode(array('status' => 0));
     	}
     }
     
     /**
      * 批量删除素材
      */
     public function delete_material()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
     	$post['mids'] = trim($post['mids']);
     	if(empty($post['mids']))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	$delete_query = $this->material->batch_delete(explode(',',$post['mids']));
     	
     	if($delete_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     	}
     	else
     	{
     		echo json_encode(array('status' => 0));
     	}
     }
     
     /**
      * 访问日志管理
      */
     public function mgVisitor()
     {
     	$data['bg_left'] = $this->load->module("common/bg_left",array(3),true);
     
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/visitor",$data);
     	$this->load->module("common/bg_footer");
     }
     /**
      * 分类管理
      */
     public function mgCategories()
     {
     	$data['bg_left'] = $this->load->module("common/bg_left",array(4),true);
     	 
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/category",$data);
     	$this->load->module("common/bg_footer");
     }
     /**
      * 系统设置
      */
     public function mgSystem()
     {
     	$data['bg_left'] = $this->load->module("common/bg_left",array(5),true);
     	 
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/system",$data);
     	$this->load->module("common/bg_footer");
     }
}