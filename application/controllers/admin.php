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
      * 管理素材版本
      */
     public function mgVersion($mid = 0)
     {
     	$this->load->model('material_model', 'material');
     	
     	$mid = (int)$mid;
		if( ! $mid)
		{
			show_error('参数错误');
		}
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			show_error('素材不存在');
		}
		$material = $material_query['material'];
		
        //查询素材版本信息
		$material_versions_query = $this->material->get_material_versions($mid);
		if( ! $material_versions_query['status'])
		{
			$material_versions = array();
		}
		else
		{
			$material_versions = $material_versions_query['material_versions'];
		}
		
     	$data['bg_left'] = $this->load->module("common/bg_left",array(2),true);
     	$data['material'] = $material;
     	$data['material_versions'] = $material_versions;
     	
     	$this->load->module("common/bg_header");
    	$this->load->view("admin/material_version",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      *  设置默认版本
      */
     public function set_default_version()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
		$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		if( ! $mid || ! $vid)
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
		//检查版本是否属于素材
		if( ! check_version_of_material($vid, $mid))
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
		$set_default_version = $this->material->set_default_version($vid, $mid);
		if($set_default_version['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode(array('status' => 0));
		}
     }
     
     /**
      * 删除版本
      */
     public function delete_version()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
		$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		if( ! $mid || ! $vid)
		{
			echo json_encode(array('status' => 0, 'msg' => '参数错误'));
			exit;
		}
		
     	//检查版本是否属于素材
		if( ! check_version_of_material($vid, $mid))
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			echo json_encode(array('status' => 0, 'msg' => '数据库错误'));
			exit;
		}
		$material = $material_query['material'];
		
		if($material['cversion'] == $vid)
		{
			echo json_encode(array('status' => 0, 'msg' => '默认版本无法删除'));
			exit;
		}
		
		if($material['vernum'] == 1)
		{
			echo json_encode(array('status' => 0,'msg' => '最后一个版本无法删除'));
			exit;
		}
		
		$delete_version = $this->material->delete_version($vid, $mid);
		if($delete_version['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '删除失败'));
		}
     }
     
     /**
      * 修改版本详细说明
      */
     public function edit_version_content()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
     	$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		$content = trim($post['content']);
		if( ! $mid || ! $vid)
		{
			echo json_encode(array('status' => 0, 'msg' => '参数错误'));
			exit;
		}
		
    	//检查版本是否属于素材
		if( ! check_version_of_material($vid, $mid))
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
     	$update_version = $this->material->update_version_content($content, $vid, $mid);
		if($update_version['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '删除失败'));
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
     
     /**
      * 用户管理
      */
     public function mgUser($page = 1)
     {
     	$page = (int) $page;
     	
     	$this->load->model('user_model', 'user');
     	$this->load->model('material_model', 'material');
     	
		$search = $this->input->get('search', TRUE);
     	$search = trim(urldecode($search));
     	
     	//查询总数
     	$total = 0;
     	$total_query = $this->user->getTotalUser($search);
     	if($total_query['status'])
     	{
     		$total = $total_query['total'];
     	}
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	if($search)
     	{
     		$config['suffix'] = '?search=' . urlencode($search);
     		$config['first_url'] = base_url('admin/mgUser/1?search=' . urlencode($search));
     	}
     	$config['base_url'] = base_url('admin/mgUser');
		$config['total_rows'] = $total;
		$config['per_page'] = 2; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$pages =  $this->pagination->create_links();
     	
		//查询用户
     	$userlist_query = $this->user->getUserList($page, $config['per_page'], $search);
     	if( ! $userlist_query['status'])
     	{
     		show_error('数据库出错');
     	}
     	$users = $userlist_query['users'];
     	
     	//查询用户素材数
     	$uids = $user_material =  array();
     	foreach($users as $user)
     	{
     		$uids[] = $user['id'];
     	}
     	if( ! empty($uids))
     	{
     		$user_material_query = $this->material->get_user_material($uids);
     		if($user_material_query['status'])
     		{
     			$user_material = $user_material_query['user_material'];
     		}
     	}
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(6),true);
     	$data['users'] = $users;
     	$data['user_material'] = $user_material;
     	$data['pages'] = $pages;
     	 
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/user",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      * 删除用户
      */
     public function delete_user()
     {
     	$this->load->model('user_model', 'user');
     	$post = $this->input->post(NULL, TRUE);
     	$post['uids'] = trim($post['uids']);
     	if(empty($post['uids']))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	$uids = explode(',', $post['uids']);
     	//判断权限
     	$can_op_user = $this->_can_op_user($uids);
     	if( ! $can_op_user['check'])
     	{
     		echo json_encode(array('status' => 0, 'msg' => $can_op_user['msg']));
     		exit;
     	}
     	$delete_query = $this->user->batchDeleteUser(explode(',',$uids));
     	
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
      * 判断是否有权限操作用户
      * 
      * @param array $uids
      */
     private function _can_op_user($uids)
     {
     	$this->load->model('user_model', 'user');
     	$login = checklogin();
     	if($login &&(is_array($login)))
     	{
     		if(in_array($login['auth'], array(2, 999)))
     		{
     			$auth = ($login['auth'] == 2) ? array(2, 999) : array(999);
     			$auth_user_query = $this->user->getUserByAuth($auth, $uids);
     			if($auth_user_query['status'])
     			{
     				if( ! empty($auth_user_query['user']))
     				{
     					return array('check' => FALSE, 'msg' => '无权操作用户  : ' . $auth_user_query['user']['realname']);
     				}
     			}
     			else
     			{
     				return array('check' => FALSE, 'msg' => '数据库出错了');
     			}
     		}
     		else 
     		{
     			return array('check' => FALSE, 'msg' => '无权限操作');
     		}
     	}
     	else
     	{
     		return array('check' => FALSE, 'msg' => '未登录');
     	}
     	
     	return array('check' => TRUE);
     }
}