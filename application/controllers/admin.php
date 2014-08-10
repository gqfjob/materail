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
     public function mgMaterial($search = '', $page = 1)
     {
     	$this->load->model('material_model', 'material');
     	$this->load->model('user_model', 'user');
     	
     	//分页配置
     	$config['base_url'] = base_url('admin/mgMaterial');
		$config['total_rows'] = 200;
		$config['per_page'] = 20; 
		
     	$search = trim($search);
     	$material_query = $this->material->get_all_materials($page - 1, $config['per_page']);
     	if( ! $material_query['status'])
     	{
     		show_error("数据库查询出错了",500,"出错了");
     	}
     	
     	$uids = array();
     	foreach($material_query['materials'] as $material)
     	{
     		$uids[] = $material['uid'];
     	}
     	
     	$users = array();
     	if( ! empty($uids))
     	{
     		$users = $this->user->batchGetUser(implode(',', $uids));
     	}
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(2),true);
     	
     	$data['materials'] = $material_query['materials'];
     	$data['users'] = $users;
     	
    
     	$this->load->module("common/bg_header");
    	$this->load->view("admin/material",$data);
     	$this->load->module("common/bg_footer");
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