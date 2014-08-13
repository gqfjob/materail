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
		
		//登录用户信息
		$this->user_info = checklogin();
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
} 