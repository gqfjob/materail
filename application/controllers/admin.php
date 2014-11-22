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
     	
    	$data['bg_left'] = $this->load->module("common/bg_left",array(1),true);
    	$data['total_material'] = $total_material;
    	$data['total_user'] = $total_user;
    	$data['total_visit'] = $total_visit;
    	$data['total_download'] = $total_download;

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
     	$page = ($page <= 0) ? 1 : $page;
     	
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
     		show_error("查询出错了",500,"出错了");
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
			echo json_encode(array('status' => 0, 'msg' => '出错了'));
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
     public function mgVisitor($page = 1)
     {
     	$page = (int) $page;
     	$page = ($page <= 0) ? 1 : $page;
     	$start = $this->input->get('start', TRUE);
     	$end= $this->input->get('end', TRUE);
     	$where_time = array();
     	$start_time = $end_time = 0;
     	if($start_time !== FALSE)
     	{
     		$data['start'] = $where_time['start'] = trim($start);
     		$start_time = strtotime($where_time['start']);
     	}
     	if($end_time !== FALSE)
     	{
     		$data['end'] = $where_time['end'] = trim($end);
     		$end_time = strtotime($where_time['end']);
     	}
     	
     	$this->load->model('visit_log_model', 'visit_log');
     	//查询总数
     	$total = 0;
     	$total_query = $this->visit_log->getTotal($start_time, $end_time);
     	if($total_query['status'])
     	{
     		$total = (int) $total_query['total'];
     	}
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	if( ! empty($where_time))
     	{
     		$config['suffix'] = '?' . http_build_query($where_time);
     		$config['first_url'] = base_url('admin/mgVisitor/1?' . http_build_query($where_time));
     	}
     	$config['base_url'] = base_url('admin/mgVisitor');
		$config['total_rows'] = $total;
		$config['per_page'] = 20; 
     	$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$pages =  $this->pagination->create_links();
		
		//查询访问列表
		$lists = $uids = array();
		$lists_query = $this->visit_log->getAllList($page, $config['per_page'], $start_time, $end_time);
		if($lists_query['status'])
		{
			$lists = $lists_query['lists'];
			foreach($lists as $list)
			{
				$uids[] = $list['uid'];
			}
		}
		
     	//查询用户信息
     	$users = array();
     	if( ! empty($uids))
     	{
     		$users = $this->user->batchGetUser($uids);
     	}
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(3),true);
     	$data['lists'] = $lists;
     	$data['users'] = $users;
     	$data['pages'] = $pages;
     
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/visitor",$data);
     	$this->load->module("common/bg_footer");
     }
     /**
      * 分类管理
      */
     public function mgCategories()
     {
     	$this->lang->load('upload');
		$data['lang'] = $this->lang;
     	$this->load->model('material_model', 'material');
     	//素材分类
		$data['cates'] = array();
		$material_cate_query = $this->material->get_material_cate();
		if($material_cate_query['status'])
		{
			$data['cates'] = $material_cate_query['material_cate'];
		}
     	$data['bg_left'] = $this->load->module("common/bg_left",array(4),true);
     	 
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/category",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      * 创建分类
      */
     public function create_cate()
     {
     	$this->load->model('material_model', 'material');
     	
     	$cate_name = trim($this->input->post('cate_name', TRUE));
     	if(empty($cate_name))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '请输入分类名'));
     		exit;
     	}
     	$has_exists = $this->material->has_exists_cate($cate_name);
     	if($has_exists['status'])
     	{
     		if($has_exists['exists'])
     		{
     			echo json_encode(array('status' => 0, 'msg' => '分类已经存在'));
     			exit;
     		}
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '出错了'));
     		exit;
     	}
     	
     	$create_cate = $this->material->create_cate($cate_name);
     	if($create_cate['status'])
     	{
     		echo json_encode(array('status' => 1, 'cid' => $create_cate['cid']));
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '新增分类失败'));
     	}
     	exit;
     }
     
     /**
      * 检查分类
      */
     public function check_cate()
     {
     	$this->load->model('material_model', 'material');
     	
     	$cid = $this->input->post('cid', TRUE);
     	$cid = (int) $cid;
     	if(empty($cid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	$has_material = $this->material->has_material($cid);
		if($has_material['status'])
		{
			echo json_encode(array('status' => 1, 'has' => $has_material['has']));
		}  
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '出错了'));
		}   	
     }
     
     /**
      * 删除分类
      */
     public function delete_cate()
     {
     	$this->load->model('material_model', 'material');
     	
     	$cid = $this->input->post('cid', TRUE);
     	$cid = (int) $cid;
     	
     	if(empty($cid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	//查询默认分类
     	$default_id = 0;
     	$default_cate_query = $this->material->get_default_cate('其他');
     	if($default_cate_query['status'] && ! empty($default_cate_query['cate']))
     	{
     		$default_id = $default_cate_query['cate']['id'];
     	}
     	if($default_id == $cid)
     	{
     		echo json_encode(array('status' => 0, 'msg' => '默认分类不能删除'));
     		exit;
     	}
     	
     	$delete_cate = $this->material->delete_cate($cid, $default_id);
		if($delete_cate['status'])
		{
			echo json_encode(array('status' => 1));
		}  
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '出错了'));
		}   	
     }
     
     public function edit_cate()
     {
     	$this->load->model('material_model', 'material');
     	
     	$post = $this->input->post(NULL, TRUE);
     	$cid = (int) $post['cid'];
     	$cname = trim($post['cname']);
     	$clogo = trim($post['clogo']);
     	if(empty($cid) || empty($cname))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	$update_cate = $this->material->update_cate(array('id' => $cid, 'cname' => $cname, 'clogo' => $clogo));
     	if($update_cate['status'])
		{
			echo json_encode(array('status' => 1));
		}  
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '出错了'));
		}   	
     	
     }
     /**
      * 系统设置
      */
     public function mgSystem()
     {
     	//查询系统配置
     	$this->load->model('site_config_model', 'site_config');
     	$keys = array('SITE_TITLE', 'SITE_NOTICE', 'IS_NOTICE');
     	$site_config = array();
     	$site_config_query = $this->site_config->get_site_config($keys);
     	if($site_config_query['status'])
     	{
     		$site_config = $site_config_query['site_config'];
     	}
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(5),true);
     	$data['site_config'] = $site_config;
     	
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/system",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      * 设置系统配置
      */
     public function set_site_config()
     {
     	$this->load->model('site_config_model', 'site_config');
     	$title = $this->input->post('title', TRUE);
     	$notice = $this->input->post('notice', TRUE);
     	$is_notice = $this->input->post('is_notice', TRUE);
     	
     	$set_site_config = array(
     		array(
     			'skey' => 'SITE_TITLE',
     			'svalue' => ($title) ? trim($title) : ''
     		),
     		array(
     			'skey' => 'SITE_NOTICE',
     			'svalue' => ($notice) ? trim($notice) : ''
     		),
     		array(
     			'skey' => 'IS_NOTICE',
     			'svalue' => ($is_notice) ? 1 : 0
     		),
     	);
     	
     	$set_query = $this->site_config->set_site_config($set_site_config);
     	if($set_query['status'])
     	{
     		redirect('admin/mgSystem');
     	}
     	else
     	{
     		show_error('出错了');
     	}
     }
     
     /**
      * 用户管理
      */
     public function mgUser($page = 1)
     {
     	$page = (int) $page;
     	$page = ($page <= 0) ? 1 : $page;
     	
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
		$config['per_page'] = 10; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$pages =  $this->pagination->create_links();
     	
		//查询用户
     	$userlist_query = $this->user->getUserList($page, $config['per_page'], $search);
     	if( ! $userlist_query['status'])
     	{
     		show_error('出错了');
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
     	$data['search'] = $search;
     	 
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
     	$delete_query = $this->user->batchDeleteUser($uids);
     	
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
      * 设置用户状态
      */
     public function set_user_status()
     {
     	$this->load->model('user_model', 'user');
     	$post = $this->input->post(NULL, TRUE);
     	$post['uids'] = trim($post['uids']);
     	$post['status'] = (int) $post['status'];
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
     	$update_query = $this->user->batchSetUserStatus($uids, $post['status']);
     	
     	if($update_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     	}
     	else
     	{
     		echo json_encode(array('status' => 0));
     	}
     	
     }
     
     /**
      * 用户详细内容
      */
     public function userDetail($page = 1)
     {
     	$uid = (int) $this->input->get('uid', TRUE);
     	if(empty($uid))
     	{
     		show_error('参数错误');
     	}
     	$this->load->model('material_model', 'material');
     	//获取用户信息
     	$user = $this->user->getUserFull($uid);
     	if( ! $user)
     	{
     		show_error('用户不存在');
     	}
     	
     	$uids = $mids = $vids = array();
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	
     	//查询可访问素材总数
     	$view_total = 0;
     	$view_total_query = $this->material->count_view_material($uid);
     	if($view_total_query['status'])
     	{
     		$view_total = $view_total_query['total'];
     	}
     	
     	//可访问素材分页配置
     	$config['base_url'] = base_url('admin/get_view_material');
     	$config['suffix'] = '?uid=' . $uid;
     	$config['first_url'] = base_url('admin/get_view_material/1?uid=' . $uid);
		$config['total_rows'] = $view_total;
		$config['per_page'] = 10; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$view_pages =  $this->pagination->create_links();
		
     	//查询可访问素材列表
     	$view_materials = array();
     	$view_materials_query = $this->material->get_view_material($uid, 1, $config['per_page']);
     	if($view_materials_query['status'])
     	{
     		$view_materials = $view_materials_query['view_materials'];
     		foreach($view_materials as $view_material)
     		{
     			$uids[] = $view_material['uid'];
	     		$mids[] = $view_material['id'];
	     		$vids[] = $view_material['cversion'];
     		}
     	}
     	
     	//查询上传素材总数
     	$upload_total = 0;
     	$upload_total_query = $this->material->count_upload_material($uid);
     	if($upload_total_query['status'])
     	{
     		$upload_total = $upload_total_query['total'];
     	}
     	
     	//可上传素材分页配置
     	$config['base_url'] = base_url('admin/get_upload_material');
     	$config['suffix'] = '?uid=' . $uid;
     	$config['first_url'] = base_url('admin/get_upload_material/1?uid=' . $uid);
		$config['total_rows'] = $upload_total;
		$config['per_page'] = 10; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$upload_pages =  $this->pagination->create_links();
		
     	//查询上传素材
     	$upload_materials = array();
     	$upload_materials_query = $this->material->get_upload_material($uid, 1, $config['per_page']);
     	if($upload_materials_query['status'])
     	{
     		$upload_materials = $upload_materials_query['upload_materials'];
     		foreach($upload_materials as $upload_material)
     		{
     			$uids[] = $upload_material['uid'];
	     		$mids[] = $upload_material['id'];
	     		$vids[] = $upload_material['cversion'];
     		}
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
     	
     	$data['bg_left'] = $this->load->module("common/bg_left",array(6),true);
     	$data['user'] = (array)$user;
     	$data['view_materials'] = $view_materials;
     	$data['upload_materials'] = $upload_materials;
     	$data['view_pages'] = $view_pages;
     	$data['upload_pages'] = $upload_pages;
     	$data['users'] = $users;
     	$data['versions'] = $versions;
     	$data['attachment_num'] = $attachment_num;
     	 
     	$this->load->module("common/bg_header");
     	$this->load->view("admin/user_detail",$data);
     	$this->load->module("common/bg_footer");
     }
     
     /**
      * 获取可访问素材
      * 
      * @param int $uid
      * @param int $page
      */
     public function get_view_material($page = 1)
     {
     	$page = (int) $page;
     	$page = ($page <= 0) ? 1 : $page;
     	$uid = (int) $this->input->get('uid', TRUE);
     	if(empty($uid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	$this->load->model('material_model', 'material');
     	//获取用户信息
     	$user = $this->user->getUserFull($uid);
     	if( ! $user)
     	{
     		echo json_encode(array('status' => 0, 'msg' => '用户不存在'));
     		exit;
     	}
     	
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	
     	//查询可访问素材总数
     	$view_total = 0;
     	$view_total_query = $this->material->count_view_material($uid);
     	if($view_total_query['status'])
     	{
     		$view_total = $view_total_query['total'];
     	}
     	
     	//可访问素材分页配置
     	$config['base_url'] = base_url('admin/get_view_material');
     	$config['suffix'] = '?uid=' . $uid;
     	$config['first_url'] = base_url('admin/get_view_material/1?uid=' . $uid);
		$config['total_rows'] = $view_total;
		$config['per_page'] = 10; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$view_pages =  $this->pagination->create_links();
		
		$uids = $mids = $vids = array();
     	//查询可访问素材列表
     	$view_materials = array();
     	$view_materials_query = $this->material->get_view_material($uid, $page, $config['per_page']);
     	if($view_materials_query['status'])
     	{
     		$view_materials = $view_materials_query['view_materials'];
     		foreach($view_materials as $view_material)
     		{
     			$uids[] = $view_material['uid'];
	     		$mids[] = $view_material['id'];
	     		$vids[] = $view_material['cversion'];
     		}
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
     	
     	$html = '';
     	if( ! empty($view_materials))
     	{
     		foreach($view_materials as $material)
     		{
     			$html .= '<tr>';
                $html .= '<td><input autocomplete="off" type="checkbox" name="view-material" data-id="' .  $material['id'] . '" value="" /></td>';
                $html .= '<td><a href="' . base_url('admin/mgVersion/' . $material['id']) . '" target="_blank"  title="' . $material['mname'] . '" >' .$material['mname'] . '</a></td>';
                $html .= '<td><a href="' . base_url('admin/userDetail/' . $material['uid']) . '" target="_blank">' . (empty($users[$material['uid']]['realname']) ? '' : $users[$material['uid']]['realname']) . '</a></td>';
                $html .= '<td>' . (empty($material['cname']) ? '' : $material['cname']) . '</td>';
                $html .= '<td>' . (empty($attachment_num[$material['id']]['num']) ? 0 : $attachment_num[$material['id']]['num']) . '</td>';
                $html .= '<td>' . $material['vernum'] . '</td>';
                $html .= '<td>' . (empty($versions[$material['cversion']]['depict']) ? '' : $versions[$material['cversion']]['depict']) . '</td>';
                $html .= '<td>' . date('Y-m-d H:i:s', $material['create_at']) . '</td>';
                $html .= '</tr>';
     		}
     	}
     	else
     	{
     		$html = '<td colspan="8" class="text-center"><strong>暂无素材</strong></td>';
     	}
     	echo json_encode(array('status' => 1, 'html' => $html, 'pages' => $view_pages));
     	exit;
     }
     
     /**
      * 获取上传素材
      * @param int $uid
      * @param int $page
      */
 	 public function get_upload_material($page = 1)
     {
     	$page = (int) $page;
     	$page = ($page <= 0) ? 1 : $page;
     	$uid = (int) $this->input->get('uid', TRUE);
     	if(empty($uid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	$this->load->model('material_model', 'material');
     	//获取用户信息
     	$user = $this->user->getUserFull($uid);
     	if( ! $user)
     	{
     		echo json_encode(array('status' => 0, 'msg' => '用户不存在'));
     		exit;
     	}
     	
     	//分页配置
     	$config = $this->config->item('pagination_config');
     	//查询上传素材总数
     	$upload_total = 0;
     	$upload_total_query = $this->material->count_upload_material($uid);
     	if($upload_total_query['status'])
     	{
     		$upload_total = $upload_total_query['total'];
     	}
     	
     	//可访问素材分页配置
     	$config['base_url'] = base_url('admin/get_upload_material');
     	$config['suffix'] = '?uid=' . $uid;
     	$config['first_url'] = base_url('admin/get_upload_material/1?uid=' . $uid);
		$config['total_rows'] = $upload_total;
		$config['per_page'] = 10; 
		$this->load->library('pagination');
     	$this->pagination->initialize($config); 
		$upload_pages =  $this->pagination->create_links();
		
		$uids = $mids = $vids = array();
     	//查询可访问素材列表
     	$upload_materials = array();
     	$upload_materials_query = $this->material->get_upload_material($uid, $page, $config['per_page']);
     	if($upload_materials_query['status'])
     	{
     		$upload_materials = $upload_materials_query['upload_materials'];
     		foreach($upload_materials as $upload_material)
     		{
     			$uids[] = $upload_material['uid'];
	     		$mids[] = $upload_material['id'];
	     		$vids[] = $upload_material['cversion'];
     		}
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
     	
     	$html = '';
     	if( ! empty($upload_materials))
     	{
     		foreach($upload_materials as $material)
     		{
     			$html .= '<tr>';
                $html .= '<td><input autocomplete="off" type="checkbox" name="upload-material" data-id="' .  $material['id'] . '" value="" /></td>';
                $html .= '<td><a href="' . base_url('admin/mgVersion/' . $material['id']) . '" target="_blank"  title="' . $material['mname'] . '" >' .$material['mname'] . '</a></td>';
                $html .= '<td>' . (empty($material['cname']) ? '' : $material['cname']) . '</td>';
                $html .= '<td>' . (empty($attachment_num[$material['id']]['num']) ? 0 : $attachment_num[$material['id']]['num']) . '</td>';
                $html .= '<td>' . $material['vernum'] . '</td>';
                $html .= '<td>' . (empty($versions[$material['cversion']]['depict']) ? '' : $versions[$material['cversion']]['depict']) . '</td>';
                $html .= '<td>' . date('Y-m-d H:i:s', $material['create_at']) . '</td>';
                 $html .= '<td><a href="' . base_url('admin/userDetail/' . $material['uid']) . '" target="_blank">' . (empty($users[$material['uid']]['realname']) ? '' : $users[$material['uid']]['realname']) . '</a></td>';
                $html .= '</tr>';
     		}
     	}
     	else
     	{
     		$html = '<td colspan="8" class="text-center"><strong>暂无素材</strong></td>';
     	}
     	echo json_encode(array('status' => 1, 'html' => $html, 'pages' => $upload_pages));
     	exit;
     }
     
     /**
      * 搜索素材
      */
     public function search_material()
     {
     	$name = $this->input->post('name', TRUE);
     	$name = trim($name);
     	if(empty($name))
     	{
     		echo json_encode(array('status' => 0));
     		exit;
     	}
     	$this->load->model('material_model', 'material');
     	$material_query = $this->material->get_all_materials(1,10, $name);
     	if( ! $material_query['status'])
     	{
     		echo json_encode(array('status' => 0));
     		exit;
     	}
     	echo json_encode(array('status' => 1, 'materials' => $material_query['materials']));
     }
     
     /**
      * 新增访问素材
      */
     public function add_view_material()
     {
     	$post = $this->input->post(NULL, TRUE);
     	$mid = (int) $post['mid'];
     	$uid = (int) $post['uid'];
     	
     	if(empty($mid) || empty($uid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	$this->load->model('material_model', 'material');
     	$check_query = $this->material->check_view_material($mid, $uid);
     	if($check_query['status'])
     	{
     		if($check_query['check'])
     		{
     			echo json_encode(array('status' => 0, 'msg' => '此素材已经存在于访问列表中'));
     			exit;
     		}
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '出错了'));
     		exit;
     	}
     	
     	$add_view_query = $this->material->add_view_material($mid, $uid);
     	if($add_view_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     		exit;
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '新增失败'));
     		exit;
     	}
     	
     }
     
     /**
      * 删除可访问素材
      */
     public function remove_view_material()
     {
     	$post = $this->input->post(NULL, TRUE);
     	$uid = (int) $post['uid'];
     	$mids = trim($post['mids']);
     	if(empty($mids) || empty($uid))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	$this->load->model('material_model', 'material');
     	
     	$remove_view_query = $this->material->remove_view_material(explode(',', $mids), $uid);
     	if($remove_view_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     		exit;
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '删除失败'));
     		exit;
     	}
     	
     }
     
     /**
      * 设置用户权限
      */
     public function set_auth()
     {
     	$post = $this->input->post(NULL, TRUE);
     	$uid = (int) $post['uid'];
     	$auth = (int) $post['auth'];
     	if(empty($uid) || ! in_array($auth, array(1, 2)))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	//判断权限
     	$can_op_user = $this->_can_op_user(array($uid));
     	if( ! $can_op_user['check'])
     	{
     		echo json_encode(array('status' => 0, 'msg' => $can_op_user['msg']));
     		exit;
     	}
     	
     	$set_auth_query = $this->user->setAuth($auth,$uid);
        if($set_auth_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     		exit;
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '操作失败'));
     		exit;
     	}
     	
     }
     
	/**
      * 设置用户权限
      */
     public function set_upload_auth()
     {
     	$post = $this->input->post(NULL, TRUE);
     	$uid = (int) $post['uid'];
     	$upload_auth = (int) $post['upload_auth'];
     	if(empty($uid) || ! in_array($upload_auth, array(0, 1)))
     	{
     		echo json_encode(array('status' => 0, 'msg' => '参数错误'));
     		exit;
     	}
     	
     	//判断权限
     	$can_op_user = $this->_can_op_user(array($uid));
     	if( ! $can_op_user['check'])
     	{
     		echo json_encode(array('status' => 0, 'msg' => $can_op_user['msg']));
     		exit;
     	}
     	
     	$set_uploadauth_query = $this->user->setUploadAuth($upload_auth,$uid);
        if($set_uploadauth_query['status'])
     	{
     		echo json_encode(array('status' => 1));
     		exit;
     	}
     	else
     	{
     		echo json_encode(array('status' => 0, 'msg' => '操作失败'));
     		exit;
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
     				return array('check' => FALSE, 'msg' => '出错了');
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