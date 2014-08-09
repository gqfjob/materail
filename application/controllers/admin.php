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
    
}