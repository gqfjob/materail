<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Controller{
	/**
	 * 上传文件信息
	 * @var array
	 */
	private $file_info = array();
	
	/**
	 * 登录用户信息
	 * @var array
	 * @access private
	 */
	private $user_info;
	
	public function __construct()
	{
		parent::__construct();
		
		$current_method = $this->router->fetch_method();
		
		//登录用户信息
		$this->user_info = checklogin();
		if( ! in_array($current_method, array('download', 'upload_clogo')))
		{
			if( ! $this->user_info)
			{
				if($this->input->is_ajax_request()){
	    			echo json_encode(array('status' => 0, 'msg' => '您未登录'));
	    			exit;
	    		}else{
	    			redirect('user/login?callback=' . urlencode(base_url('material/' . $current_method)));
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
	 * 上传缩略图
	 * 
	 * @return json 
	 */
	public function upload_thumb()
	{
		$config['upload_path'] = 'uploads/thumb/' . date('Ym') . '/';
	    $config['allowed_types'] = 'gif|jpg|png';
	    $config['max_size'] = '2048';
	  	$config['overwrite'] = FALSE;
	  	$config['encrypt_name'] = TRUE;
		$this->_upload($config);

		if(empty($this->file_info))
		{
			echo json_encode(array('status' => 0, 'msg' => '上传文件失败'));
			exit;
		}
		
		//生成缩略图
		$this->load->library('Zebra_Image');
		$source_path = $target_path = $config['upload_path'] . $this->file_info['file_name'];
		$this->zebra_image->source_path = $source_path;
		$this->zebra_image->target_path = $target_path;
		if( ! $this->zebra_image->resize(118, 118))
		{
			echo json_encode(array('status' => 0, 'msg' => '生成缩略图失败'));
			exit;
		}
		
		echo json_encode(array('status' => 1, 'result' => array('file_path' => $source_path),'msg' => '操作成功'));
	}
	
	/**
	 * 上传素材附件
	 * 
	 * @return json
	 */
	public function upload_attachment()
	{
		$config['upload_path'] = 'uploads/attachment/' . date('Ym') . '/';
	    $config['allowed_types'] = 'doc|txt|ppt|zip|jpg|gif|png|ico';
	    $config['max_size'] = '204800';
	  	$config['overwrite'] = FALSE;
	  	$config['encrypt_name'] = TRUE;
		$this->_upload($config);
		
		if(empty($this->file_info))
		{
			echo json_encode(array('status' => 0, 'msg' => '上传文件失败'));
			exit;
		}
		
		$attachment = array(
			'sname' => $this->file_info['orig_name'],
			'rname' => $config['upload_path'] . $this->file_info['file_name'],
			'mid'   => 0,
			'mvid'  => 0,
			'pfix'  => ltrim($this->file_info['file_ext'], '.'),
			'uptime' => time(),
			'upuser' => $this->user_info['id'],
			'stat' => 1
		);
		$this->load->model('material_model', 'material');
		$res = $this->material->insert_attachment($attachment);
		if($res['status'])
		{
			echo json_encode(array('status' => 1, 'result' => array('attachment_id' => $res['attachment_id'], 'attachment_name' => $attachment['sname'])));
		}
		else
		{
			echo json_encode($res);
		}
		
	}
	
	/**
	 * 删除素材附件
	 * 
	 * @return json
	 */
	public function delete_attachment()
	{
		$attachment_id = $this->input->post('attachment_id', TRUE);
		if( ! $attachment_id)
		{
			echo json_encode(array('status' => 0));
			exit;
		}
		
		$this->load->model('material_model', 'material');
		$attachment_query = $this->material->get_attachment($attachment_id);
		if( ! $attachment_query['status'] || empty($attachment_query['attachment']))
		{
			echo json_encode(array('status' => 0, 'msg' => '文件不存在'));
			exit;
		}
		$attachment= $attachment_query['attachment'];
		if($attachment['mid'] != 0 && $attachment['mvid'] != 0)
		{
			//检查版本管理权限
			if( ! check_manager_version($attachment['mvid'], $attachment['mid'], $this->user_info['id']))
			{
				echo json_encode(array('status' => 0, 'msg' => '没有权限'));
				exit;
			}
		}
		else
		{
			if($attachment['upuser'] != $this->user_info['id'])
			{
				echo json_encode(array('status' => 0, 'msg' => '没有权限'));
				exit;
			}
		}
		$res = $this->material->update_attachment($attachment_id, $attachment['mvid']);
		if($res['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode(array('status' => 0, 'msg' => '删除失败'));
		}
	}
	
	/**
	 * 上传文件处理
	 * 
	 * @param array $config 上传文件配置
	 */
	private function _upload($config)
	{
		if( ! is_dir($config['upload_path']) && ! mkdir($config['upload_path']))
		{
			echo json_encode(array('status' => 0, 'msg' => '创建目录失败'));
			exit;
		}
		
	 	$this->load->library('upload', $config);
		 
	    if ( ! $this->upload->do_upload('upload_file'))
	    {
	   		echo json_encode(array('status' => 0, 'msg' => $this->upload->display_errors()));
	   		exit;
	  	} 
		
	  	$this->file_info = $this->upload->data();
	}
	
	/**
	 * 下载文件
	 * 
	 * @param string $type
	 * @param int $id
	 */
	public function download($type = '', $id = 0)
	{
		$this->load->model('material_model', 'material');
		
		$type = trim($type);
		$id = (int) $id;
		
		$types = array('version', 'attachment');
		
		if( ! in_array($type, $types) || empty($id))
		{
			show_error('无法访问');
		}
		
		if($type == 'version')
		{
			//查询版本信息
			$version_query = $this->material->get_version($id);
			if( ! $version_query['status'] || empty($version_query['version']))
			{
				show_error('版本不存在');
			}
			$version = $version_query['version'];
			$mid = $version['mid'];
			$vid = $version['id'];
			$down_path = $version['zip_path'];
			$filename = $version['depict'] . '.zip';
		}
		else
		{
			//查询附件信息
			$attachment_query = $this->material->get_attachment($id);
			if( ! $attachment_query['status'] || empty($attachment_query['attachment']))
			{
				show_error('附件不存在');
			}
			$attachment= $attachment_query['attachment'];
			$mid = $attachment['mid'];
			$vid = $attachment['mvid'];
			$down_path = $attachment['rname'];
			$filename = $attachment['sname'];
		}
		
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			show_error('素材不存在');
		}
		$material = $material_query['material'];
		
		//判断权限
		check_view_down_material($material, $this->user_info);
		
		if( ! file_exists($down_path))
		{
			show_error('下载文件不存在');
		}
		create_visit(3);
		$this->material->custom_update_version($vid, array('downnum' => 'downnum+1'));
		$mime = 'application/octet-stream';
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.rawurlencode($filename).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".filesize($down_path));
		}
		else
		{
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".filesize($down_path));
		}
		readfile($down_path);
	}
	
	/**
	 * 查看附件
	 * 
	 * @param int $id
	 */
	public function view($id)
	{
		$this->load->model('material_model', 'material');
		
		$id = (int) $id;
		
		$types = array('jpg', 'gif', 'png', 'txt');
		
		if(empty($id))
		{
			show_error('无法访问');
		}
		
		//查询附件信息
		$attachment_query = $this->material->get_attachment($id);
		if( ! $attachment_query['status'] || empty($attachment_query['attachment']))
		{
			show_error('附件不存在');
		}
		$attachment= $attachment_query['attachment'];
		if( ! in_array($attachment['pfix'], $types))
		{
			show_error('无法查看此类型文件,请下载后查看');
		}
		$mid = $attachment['mid'];
		$vid = $attachment['mvid'];
		$view_path = $attachment['rname'];
		$filename = $attachment['sname'];
		
		//查询素材信息
		$material_query = $this->material->get_material($mid);
		if( ! $material_query['status'] || empty($material_query['material']))
		{
			show_error('素材不存在');
		}
		$material = $material_query['material'];
		
		//判断权限
		check_view_down_material($material, $this->user_info);
		
		if( ! file_exists($view_path))
		{
			show_error('查看文件不存在');
		}
		$this->material->custom_update_version($vid, array('shownum' => 'shownum+1'));
		redirect($view_path);
	}
	
	/**
	 * 上传分类图片
	 */
	public function upload_clogo()
	{
		$config['upload_path'] = 'uploads/clogo/';
	    $config['allowed_types'] = 'gif|jpg|png';
	    $config['max_size'] = '2048';
	  	$config['overwrite'] = FALSE;
	  	$config['encrypt_name'] = TRUE;
		$this->_upload($config);

		if(empty($this->file_info))
		{
			echo json_encode(array('status' => 0, 'msg' => '上传文件失败'));
			exit;
		}
		$this->load->library('Zebra_Image');
		$source_path = $target_path = $config['upload_path'] . $this->file_info['file_name'];
		$this->zebra_image->source_path = $source_path;
		$this->zebra_image->target_path = $target_path;
		if( ! $this->zebra_image->resize(128, 128))
		{
			echo json_encode(array('status' => 0, 'msg' => '生成缩略图失败'));
			exit;
		}
		
		echo json_encode(array('status' => 1, 'result' => array('file_path' => $source_path),'msg' => '操作成功'));
	}
} 