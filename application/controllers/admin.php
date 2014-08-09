<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model','user');
        $this->load->helper('cookie');
        $this->load->model('config_model');
    }
    /**
     * 后台首页
     */
    public function index()
    {
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
    	}
    }
    
}