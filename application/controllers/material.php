<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Material Controller Class
 * 
 * @author gxy
 *
 */
class Material extends CI_Controller {
	/**
	 * 登录用户信息
	 * @var array
	 * @access private
	 */
	private $user_info = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('material_model', 'material');
		
		$current_method = $this->router->fetch_method();
		
		//登录用户信息
		$this->user_info = checklogin();
		if( ! in_array($current_method, array('search', 'detail', 'list')))
		{
			if( ! $this->user_info)
			{
				if($this->input->is_ajax_request()){
	    			echo json_encode(array('status' => 0, 'msg' => '您未登录'));
	    			exit;
	    		}else{
	    			redirect('user/login');
	    		}
			}
			if($this->user_info['status'] == 0)
			{
				if($this->input->is_ajax_request()){
	    			echo json_encode(array('status' => 0, 'msg' => '此用户已被禁用'));
	    			exit;
	    		}else{
	    			show_error('此用户已被禁用');
	    		}
			}
			
			//用户素材操作权限
			if( ! check_permission($this->user_info))
			{
				if($this->input->is_ajax_request()){
	    			echo json_encode(array('status' => 0, 'msg' => '您没有权限'));
	    			exit;
	    		}else{
	    			show_error('您没有权限');
	    		}
			}
		}
	}
	
	/**
	 * 上传素材
	 */
	public function material_add()
	{
		$this->lang->load('upload');
		$data['lang'] = $this->lang;
		
		//素材分类
		$data['material_cate'] = array();
		$material_cate_query = $this->material->get_material_cate();
		if($material_cate_query['status'])
		{
			$data['material_cate'] = $material_cate_query['material_cate'];
		}
		
		$this->load->module("common/header",array('title'=>'上传素材', 'cur' => 999));
		$this->load->view('mate/material_add',$data);
		$this->load->module("common/footer");
	}
	
	/**
	 * 上传素材操作
	 */
	public function material_action_add()
	{
		$post = $this->input->post(NULL, TRUE);
		
		if( ! $post['material-cate'])
		{
			show_error('请选择素材类型');
		}
		
		if( ! $post['material-name'])
		{
			show_error('请填入素材名称');;
		}
		
		if( ! $post['material-name'])
		{
			show_error('请填入版本描述');
		}
		$post['thumb-type'] = (int)$post['thumb-type'];
		$post['attachment-ids'] = trim($post['attachment-ids']);
		if($post['thumb-type'])
		{
			if(empty($post['attachment-ids']))
			{
				$logo = 'assets/img/thumb-default.jpg';
			}
			else
			{
				//获取附件信息
				$attachment_query = $this->material->get_type_attachment("'jpg','gif','png'",$post['attachment-ids']);
				if(($attachment_query['status'] && empty($attachment_query['type_attachment'])) || ! $attachment_query['status'])
				{
					$logo = 'assets/img/thumb-default.jpg';
				}
				else
				{
					//生成缩略图
					$this->load->library('Zebra_Image');
					$source_path = $attachment_query['type_attachment']['rname'];
					$target_path = 'uploads/thumb/' . date('Ym') . '/' . end(explode('/', $attachment_query['type_attachment']['rname']));
					$this->zebra_image->source_path = $source_path;
					$this->zebra_image->target_path = $target_path;
					if( ! $this->zebra_image->resize(118, 118))
					{
						show_error('生成缩略图失败');
					}
					else
					{
						$logo = $target_path;
					}
				}
			}
		}
		else
		{
			$logo = $post['thumb-path'];
		}
		$material = array(
			'mname'  => $post['material-name'],
			'cid' => (int)$post['material-cate'],
			'current_time' => time(),
			'uid' => $this->user_info['id'],
			'state' => 1,
			'logo' => trim($logo),
			'vright' => trim($post['permission']),
			'vright_user' => trim($post['permission-user']),
			'version_depict' => trim($post['version-depict']),
			'attachment_ids' => $post['attachment-ids'],
			'version_content' => trim($post['version-content']),
			'version_zip' => ''
		);
		
		//附件生成zip压缩文件
		if( ! empty($material['attachment_ids']))
		{
			//查询附件信息
			$version_attachment_query = $this->material->batch_get_attachments(explode(',', $material['attachment_ids']));
			if( ! $version_attachment_query['status'])
			{
				$version_attachment = array();
			}
			else
			{
				$version_attachment = $version_attachment_query['attachments'];
			}
			$version_zip = create_zip($version_attachment);
			if($version_zip != '')
			{
				$material['version_zip'] = $version_zip;
			}
		}
		
		$material_insert = $this->material->insert_material($material);
		if($material_insert['status'])
		{
			redirect('material/manager/' . $material_insert['mid']);
		}
		else
		{
			if($version_zip && file_exists($version_zip))
			{
				unlink($version_zip);
			}
			show_error('上传素材失败');
		}
	}
	
	/**
	 * 素材管理
	 * @param int $mid
	 */
	public function manager($mid = 0)
	{
		$mid = (int)$mid;
		if( ! $mid)
		{
			show_error('参数错误');
		}
		
		//检查素材管理权限
		$manager_material =  check_manager_material($mid, $this->user_info['id']);
		
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
		
		$data = array(
			'material' => $material, 
			'material_versions' => $material_versions,
			'manager_material' => $manager_material,
			'user' => $this->user_info
		);
		
		$this->load->module("common/header",array('title'=>'管理素材', 'cur' => 999));
		$this->load->view('mate/material_manager',$data);
		$this->load->module("common/footer");
	}
	
	/**
	 * 设置素材状态
	 */
	public function set_material_status()
	{
		$post = $this->input->post(NULL, TRUE);
		$mid = isset($post['mid']) ? (int)$post['mid'] : 0;
		if( ! $mid)
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
		//检查素材管理权限
		if( ! check_manager_material($mid, $this->user_info['id']))
		{
			echo json_encode(array('status' => 0, 'msg' => '没有权限'));
			exit;
		}
		
		$update_material = $this->material->set_material_status($mid, (int) $post['status']);
		if($update_material['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode(array('status' => 0));
		}
	}
	
	/**
	 * 新增版本
	 */
	public function add_version($mid)
	{
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
		
		$data['material'] = $material;
		$data['action_url'] = 'material/version_action_add';
		$data['op_type'] = 'add';
		$this->load->module("common/header",array('title'=>'上传新版本', 'cur' => 999));
		$this->load->view('mate/material_version_op',$data);
		$this->load->module("common/footer");
	}
	
	/**
	 * 新增版本操作
	 */
	public function version_action_add()
	{
		$post = $this->input->post(NULL, TRUE);
		
		$post['version-depict'] = trim($post['version-depict']);
		$mid = (int) $post['mid'];
		
		if( ! $mid)
		{
			show_error('参数错误');
		}
		if( ! $post['version-depict'])
		{
			show_error('请填入版本描述');
		}
		
		
		//查询素材最大版本
		$max_version_query = $this->material->get_max_version($mid);
		if( ! $max_version_query['status'])
		{
			show_error('查询最大版本失败');
		}
		
		$max_version = $max_version_query['max_version'];
		$max_version++;
		
		$version = array(
			'mid' => $mid,
			'vnum' => $max_version,
			'uid' => $this->user_info['id'],
			'version_depict' => trim($post['version-depict']),
			'attachment_ids' => trim($post['attachment-ids']),
			'version_content' => trim($post['version-content']),
			'current_time' => time(),
			'version_zip' => ''
		);
		
		//附件生成zip压缩文件
		if( ! empty($version['attachment_ids']))
		{
			//查询附件信息
			$version_attachment_query = $this->material->batch_get_attachments(explode(',', $version['attachment_ids']));
			if( ! $version_attachment_query['status'])
			{
				$version_attachment = array();
			}
			else
			{
				$version_attachment = $version_attachment_query['attachments'];
			}
			$version_zip = create_zip($version_attachment);
			if($version_zip != '')
			{
				$version['version_zip'] = $version_zip;
			}
		}
		
		$insert_version = $this->material->insert_version($version);
		
		if($insert_version['status'])
		{
			redirect('material/manager/' . $mid);
			
		}
		else
		{
			if($version_zip && file_exists($version_zip))
			{
				unlink($version_zip);
			}
			show_error('上传新版本失败');
		}
	}
	
	/**
	 * 设置默认版本
	 */
	public function set_default_version()
	{
		$post = $this->input->post(NULL, TRUE);
		
		$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		if( ! $mid || ! $vid)
		{
			show_error('参数错误');
		}
		
		//检查素材管理权限
		if( ! check_manager_material($mid, $this->user_info['id']))
		{
			echo json_encode(array('status' => 0, 'msg' => '没有权限'));
			exit;
		}
		
		//检查版本所属
		if( ! check_version_of_material($vid, $mid))
		{
			echo json_encode(array('status' => 0, 'msg' => '版本不属于该素材'));
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
	 * 修改版本
	 */
	public function edit_version($mid = 0, $vid = 0)
	{
		$mid = (int)$mid;
		$vid = (int)$vid;
		
		if( ! $mid || ! $vid)
		{
			show_error('参数错误');
		}
		
		//检查版本管理权限
		if( ! check_manager_version($vid, $mid, $this->user_info['id']))
		{
			show_error('没有权限');
			
		}
		
		//检查版本所属
		if( ! check_version_of_material($vid, $mid))
		{
			show_error('版本不属于该素材');
		}
		
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			show_error('素材不存在');
		}
		$material = $material_query['material'];
		
		//查询版本信息
		$version_query = $this->material->get_version($vid);
		if( ! $version_query['status'] || empty($version_query['version']))
		{
			show_error('版本不存在');
		}
		$version = $version_query['version'];
		
		//查询版本附件
		$version_attachment_query = $this->material->get_version_attachment($vid);
		if( ! $version_attachment_query['status'])
		{
			$version_attachment = array();
		}
		else
		{
			$version_attachment = $version_attachment_query['version_attachment'];
		}
		
		$data['material'] = $material;
		$data['version'] = $version;
		$data['version_attachment'] = $version_attachment;
		$data['action_url'] = 'material/version_action_edit';
		$data['op_type'] = 'edit';
		
		$this->load->module("common/header",array('title'=>'修改版本', 'cur' => 999));
		$this->load->view('mate/material_version_op',$data);
		$this->load->module("common/footer");
	}
	
	/**
	 * 修改版本操作
	 */
	public function version_action_edit()
	{
		$post = $this->input->post(NULL, TRUE);
		
		$post['version-depict'] = trim($post['version-depict']);
		$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		
		if( ! $mid || ! $vid)
		{
			show_error('参数错误');
		}
		if( ! $post['version-depict'])
		{
			show_error('请填入版本描述');
		}
		
		//检查版本管理权限
		if( ! check_manager_version($vid, $mid, $this->user_info['id']))
		{
			show_error('没有权限');
			
		}
		
		//检查版本所属
		if( ! check_version_of_material($vid, $mid))
		{
			show_error('版本不属于该素材');
		}
		
		//查询版本信息
		$version_query = $this->material->get_version($vid);
		if( ! $version_query['status'] || empty($version_query['version']))
		{
			show_error('版本不存在');
		}
		$old_zip_path = $version_query['version']['zip_path'];
		
		$version = array(
			'mid' => $mid,
			'vid' => $vid,
			'version_depict' => trim($post['version-depict']),
			'attachment_ids' => trim($post['attachment-ids']),
			'version_content' => trim($post['version-content']),
			'current_time' => time(),
			'version_zip' => ''
		);
		
		//附件生成zip压缩文件
		$old_version_attachment = $new_version_attachment = array();
		$old_attachment_query = $this->material->get_version_attachment($vid);
		if($old_attachment_query['status'])
		{
			$old_version_attachment = $old_attachment_query['version_attachment'];
		}
		
		if( ! empty($version['attachment_ids']))
		{
			//查询附件信息
			$new_attachment_query = $this->material->batch_get_attachments(explode(',', $version['attachment_ids']));
			if($new_attachment_query['status'])
			{
				$new_version_attachment = $new_attachment_query['attachments'];
			}
		}
		$version_attachment = array_merge($old_version_attachment, $new_version_attachment);
		$version_zip = create_zip($version_attachment);
		if($version_zip != '')
		{
			$version['version_zip'] = $version_zip;
		}
		
		$update_version = $this->material->update_version($version);
		
		if($update_version['status'])
		{
			if($old_zip_path && file_exists($old_zip_path))
			{
				unlink($old_zip_path);
			}
			redirect('material/manager/' . $mid);
			
		}
		else
		{
			if($version_zip && file_exists($version_zip))
			{
				unlink($version_zip);
			}
			show_error('修改新版本失败');
		}
	}
	/**
	 * 删除素材版本
	 */
	public function delete_version()
	{
		$post = $this->input->post(NULL, TRUE);
		
		$mid = (int) $post['mid'];
		$vid = (int) $post['vid'];
		if( ! $mid || ! $vid)
		{
			echo json_encode(array('status' => 0, 'msg' => '参数错误'));
			exit;
		}
		
		//检查版本管理权限
		if( ! check_manager_version($vid, $mid, $this->user_info['id']))
		{
			echo json_encode(array('status' => 0, 'msg' => '没有权限'));
			exit;
			
		}
		
		//检查版本所属
		if( ! check_version_of_material($vid, $mid))
		{
			echo json_encode(array('status' => 0, 'msg' => '版本不属于该素材'));
			exit;
		}
		
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			echo json_encode(array('status' => 0, 'msg' => '素材不存在'));
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
	 * 搜索用户
	 */
	public function search_user()
	{
		$this->load->model('user_model','user');
		
		$name = trim($this->input->post('name', TRUE));
		if($name == '')
		{
			echo  json_encode(array('status' => 0, 'msg' => '请输入用户名称'));
			exit;
		}
		
		$users = $this->user->searchUserByField('realname', $name);
		echo json_encode(array('status' => 1, 'result' => $users));
	}

	/**
	 * 素材分类列表
	 */
	public function lists($cat = 0 )
	{
		//查询当前分类下的素材
		
		$this->load->module("common/header",array('title'=>'列表','cur'=> $cat));
		$this->load->view('mate/list');
		$this->load->module("common/footer");	
	}
	/**
	 * 素材详情
	 * @param unknown $id 素材Id
	 * @param unknown $ver 素材版本
	 */
	public function detail($id,$ver=1)
	{
		//查询素材信息
		$material_query = $this->material->get_material($id);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			show_error('素材不存在');
		}
		$material = $material_query['material'];
		if($material['vright'] == 2)
		{
			if(empty($this->user_info))
			{
				show_error('登录后才能查看');
			}
		}
		elseif($material['vright'] == 3)
		{
			//查询允许用户
			$allow_uids = array();
			$allow_users_query = $this->material->allow_users($id);
			if($allow_users_query['status'])
			{
				$allow_users = $allow_users_query['users'];
				foreach($allow_users as $allow_user)
				{
					$allow_uids[] = $allow_user['uid'];
				}
			}
			
			if(empty($this->user_info) || ! in_array($this->user_info['id'], $allow_uids))
			{
				show_error('您无权查看');
			}
		}
		
		$this->load->module("common/header",array('title'=>'素材详情','cur'=>999));
		$this->load->view('mate/detail');
		$this->load->module("common/footer");
	}
	/**
	 * 素材搜索
	 */
	public function search()
	{
		$key = $this->input->post('k',true);
		$this->load->module("common/header",array('title'=>'搜索结果'));
		$this->load->view('mate/searchRes');
		$this->load->module("common/footer");
	}
	
}

