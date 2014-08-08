<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Controller{
	/**
	 * 上传文件信息
	 * @var array
	 */
	private $file_info = array();
	
	public function __construct()
	{
		parent::__construct();
		//@todo 登录用户信息
		$this->user = array('uid' => 1);
		//@todo 用户素材操作权限(后台设置）
		if( ! check_permission())
		{
			exit('no premission');
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
			'upuser' => 0,
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
			echo json_encode(array('status' => 0));
			exit;
		}
		$attachment= $attachment_query['attachment'];
		$res = $this->material->update_attachment($attachment_id, $attachment['mvid']);
		if($res['status'])
		{
			echo json_encode(array('status' => 1));
		}
		else
		{
			echo json_encode($res);
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