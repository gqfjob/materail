<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Material Model Class
 * 
 * @author gxy
 */
class Material_Model extends CI_Model
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->rdb = getReadOnlyDB();
        $this->wdb = getWriteOnlyDB();
	}
	
	/**
	 * 增加素材附件
	 * 
	 * @param array $attachment 附件信息
	 */
	public function insert_attachment($attachment)
	{
		if(empty($attachment))
		{
			return array('status' => 0, 'msg' => '');
		}
		
		$insert_str = $this->wdb->insert_string('material_attatch',$attachment);
		$query = $this->wdb->insert('material_attatch',$attachment);
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			return array('status' => 1, 'attachment_id' => $this->wdb->insert_id());
		}
	}
	
	/**
	 * 删除素材附件
	 * 
	 * @param int $attachment_id
	 * @param int $vid
	 */
	public function update_attachment($attachment_id, $vid)
	{
		if(empty($attachment_id))
		{
			return array('status' => 0);
		}
		$this->wdb->trans_start();
		$query = $this->wdb->query('UPDATE material_attatch SET stat=0 WHERE id=?', array($attachment_id));
		$query = $this->wdb->query('UPDATE material_version SET anum=anum-1 WHERE id=?', array($vid));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
		
	}
	
	/**
	* 获取附件信息
	*
	* @param int $attachment_id
	*/
	public function get_attachment($attachment_id)
	{
		if(empty($attachment_id))
		{
			return array('status' => 0);
		}
		
		$query = $this->wdb->query('SELECT * FROM material_attatch WHERE id=?', array($attachment_id));
		
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$attachment = array();
			if ($query->num_rows() > 0)
			{
				$attachment = $query->row_array();
			} 
			return array('status' => 1,'attachment' => $attachment);
		}
	}
	
	/**
	 * 查询素材分类
	 * 
	 * @param array $attachment 附件信息
	 */
	public function get_material_cate()
	{
		$sql = "SELECT * FROM material_cate";
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$material_cate = array();
			if ($query->num_rows() > 0)
			{
				$material_cate = $query->result_array();
			} 
			return array('status' => 1, 'material_cate' => $material_cate);
		}
	}
	
	/**
	 * 插入素材
	 * 
	 * @param array $material
	 */
	public function insert_material($material)
	{
		if(empty($material))
		{
			return array('status' => 0);
		}
		$this->wdb->trans_start();
		$insert_material_info = array(
			'mname'  => $material['mname'],
			'vernum' => $material['vernum'],
			'cid' => $material['cid'],
			'create_at' => $material['current_time'],
			'update_at' => $material['current_time'],
			'uid' => $material['uid'],
			'state' => $material['state'],
			'logo' => $material['logo'],
			'vright' => $material['vright']
		);
		$this->wdb->insert('material_info', $insert_material_info);
		$mid = $this->wdb->insert_id();
		
		if($material['vright'] == 3)
		{
			$insert_visit_vright = array();
			$vright_users = explode(',', $material['vright_user']);
			foreach($vright_users as $value)
			{
				$uid = (int) $value;
				if($uid)
				{
					$insert_visit_vright[] = array(
						'mid' => $mid,
						'uid' => $value,
						'vr' => 2
					);
				}
			}
			if( ! empty($insert_visit_vright))
			{
				$this->wdb->insert_batch('material_visit_right', $insert_visit_vright);
			}
		}
		
		$attachments = ($material['attachment_ids']) ? explode(',', $material['attachment_ids']) : array();
		$insert_material_version = array(
			'mid' => $mid,
			'content' => $material['version_content'],
			'nohtml' => strip_tags($material['version_content']),
			'depict' => $material['version_depict'],
			'vnum' => 1,
			'anum' => count($attachments),
			'uid' => $material['uid'],
			'cat' => $material['current_time'],
			'upat' => $material['current_time'],
			'zip_path' => $material['version_zip']
		);
		$this->wdb->insert('material_version', $insert_material_version);
		$vid = $this->wdb->insert_id();
		
		if( ! empty($material['attachment_ids']))
		{
			$update_sql = "UPDATE material_attatch SET mid = {$mid} , mvid = {$vid} WHERE id IN ({$material['attachment_ids']})";
			$this->wdb->query($update_sql);
		}
		
		$this->wdb->query("UPDATE material_info SET cversion=?,vernum=1 WHERE id=?", array($vid, $mid));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1, 'mid' => $mid, 'vid' => $vid);
	}
	
	/**
	 * 获取素材信息
	 * 
	 * @param int $mid 素材ID
	 */
	public function get_material($mid)
	{
		if(empty($mid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT mi.*, mc.cname, mc.clogo FROM material_info mi LEFT JOIN  material_cate mc ON mi.cid=mc.id WHERE mi.id = ? ";
		$query = $this->rdb->query($sql, array($mid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$material = array();
			if ($query->num_rows() > 0)
			{
				$material = $query->row_array();
			} 
			return array('status' => 1, 'material' => $material);
		}
	}
	
	/**
	 * 获取素材版本信息
	 * 
	 * @param int $mid 素材ID
	 */
	public function get_material_versions($mid, $order = 'DESC')
	{
		if(empty($mid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT * FROM material_version WHERE mid = ? ORDER BY id {$order}";
		$query = $this->rdb->query($sql, array($mid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$material_versions = $material_versions_map = array();
			if ($query->num_rows() > 0)
			{
				$material_versions = $query->result_array();
				foreach($material_versions as $value)
				{
					$material_versions_map[$value['id']] = $value;
				}
			} 
			return array('status' => 1, 'material_versions' => $material_versions_map);
		}
	}
	
	/**
	 * 设置素材状态
	 * 
	 * @param int $mid 素材ID
	 * @param int $status 素材状态
	 */
	public function set_material_status($mid, $status)
	{
		$allow_status = array(0, 1);
		if(empty($mid))
		{
			return array('status' => 0);
		}
		
		if( ! in_array($status, $allow_status))
		{
			return array('status' => 0);
		}
		$update_sql = "UPDATE material_info SET state=?,update_at=? WHERE id=?";
		$query = $this->wdb->query($update_sql, array($status, time(), $mid));
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
		
	}
	
	/**
	 * 获取素材的最大版本
	 * 
	 * @param int $mid 素材ID
	 */
	public function get_max_version($mid)
	{
		if(empty($mid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT MAX(vnum) as max_version FROM material_version WHERE mid = ? ";
		$query = $this->rdb->query($sql, array($mid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$max_version = 0;
			if ($query->num_rows() > 0)
			{
				$res = $query->row_array();
			} 
			return array('status' => 1, 'max_version' => $res['max_version']);
		}
	}
	
	/**
	 * 插入新版本
	 * 
	 * @param array $version 版本信息
	 */
	public function insert_version($version)
	{
		if(empty($version))
		{
			return array('status' => 0);
		}
		$this->wdb->trans_start();
		$attachments = ($version['attachment_ids']) ? explode(',', $version['attachment_ids']) : array();
		$insert_material_version = array(
			'mid' => $version['mid'],
			'content' => $version['version_content'],
			'nohtml' => strip_tags($version['version_content']),
			'depict' => $version['version_depict'],
			'vnum' => $version['vnum'],
			'anum' => count($attachments),
			'uid' => $version['uid'],
			'cat' => $version['current_time'],
			'upat' => $version['current_time'],
			'zip_path' => $version['version_zip']
		);
		$this->wdb->insert('material_version', $insert_material_version);
		$mvid = $this->wdb->insert_id();
		
		if( ! empty($version['attachment_ids']))
		{
			$update_sql = "UPDATE material_attatch SET mid = {$version['mid']} , mvid = {$mvid} WHERE id IN ({$version['attachment_ids']})";
			$update_sql = $this->rdb->escape_str($update_sql);
			$this->wdb->query($update_sql);
		}
		
		$this->wdb->query("UPDATE material_info SET vernum=vernum+1,update_at={$version['current_time']} WHERE id=?", array($version['mid']));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 修改版本
	 * 
	 * @param array $version 版本信息
	 */
	public function update_version($version)
	{
		if(empty($version))
		{
			return array('status' => 0);
		}
		$this->wdb->trans_start();
		$attachments = ($version['attachment_ids']) ? explode(',', $version['attachment_ids']) : array();
		$update_material_version = array(
			$version['version_content'],
			strip_tags($version['version_content']),
			$version['version_depict'],
			count($attachments),
			$version['current_time'],
			$version['version_zip'],
			$version['vid']
		);
		$this->wdb->query('UPDATE material_version SET content=?, nohtml=?, depict=?, anum=anum+?,upat=?,zip_path=? WHERE id=?', $update_material_version);
		if( ! empty($version['attachment_ids']))
		{
			$update_sql = "UPDATE material_attatch SET mid={$version['mid']} , mvid={$version['vid']} WHERE id IN ({$version['attachment_ids']})";
			$update_sql = $this->rdb->escape_str($update_sql);
			$this->wdb->query($update_sql);
		}
		
		$this->wdb->query("UPDATE material_info SET update_at={$version['current_time']} WHERE id=?", array($version['mid']));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 检查素材所属者
	 * 
	 * @param int $mid 素材ID
	 * @param int $uid 用户ID
	 */
	public function check_material_of_user($mid, $uid)
	{
		if(empty($mid) || empty($uid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT id FROM material_info WHERE id=? AND uid=?";
		$query = $this->rdb->query($sql, array($mid, $uid));
		if($query === FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			if ($query->num_rows() > 0)
			{
				return array('status' => 1, 'check' => TRUE);
			}
			else
			{
				return array('status' => 1, 'check' => FALSE);
			}
		}
	}
	
	/**
	 * 检查版本所属用户
	 * 
	 * @param int $vid 版本ID
	 * @param int $mid 素材ID
	 * @param int $uid 用户ID
	 */
	public function check_version_of_user($vid, $mid, $uid)
	{
		if(empty($vid) || empty($mid) || empty($uid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT id FROM material_version WHERE id=? AND mid=? AND uid=?";
		$query = $this->rdb->query($sql, array($vid, $mid, $uid));
		if($query === FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			if ($query->num_rows() > 0)
			{
				return array('status' => 1, 'check' => TRUE);
			}
			else
			{
				return array('status' => 1, 'check' => FALSE);
			}
		}
	}
	
	/**
	 * 检查版本所属素材
	 * 
	 * @param int $vid 版本ID
	 * @param int $mid 素材ID
	 * @param int $uid 用户ID
	 */
	public function check_version_of_material($vid, $mid)
	{
		if(empty($vid) || empty($mid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT id FROM material_version WHERE id=? AND mid=?";
		$query = $this->rdb->query($sql, array($vid, $mid));
		if($query === FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			if ($query->num_rows() > 0)
			{
				return array('status' => 1, 'check' => TRUE);
			}
			else
			{
				return array('status' => 1, 'check' => FALSE);
			}
		}
	}
	
	/**
	 * 设置默认版本
	 * 
	 * @param int $vid 版本ID
	 * @param int $mid 素材ID
	 */
	public function set_default_version($vid, $mid)
	{
		if(empty($vid) || empty($mid))
		{
			return array('status' => 0);
		}
		
		$update_sql = "UPDATE material_info SET cversion=?  WHERE id=?";
		$query = $this->wdb->query($update_sql, array($vid, $mid));
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 删除版本
	 * 
	 * @param int $vid 版本ID
	 * @param int $mid 素材ID
	 */
	public function delete_version($vid, $mid)
	{
		if(empty($vid) || empty($mid))
		{
			return array('status' => 0);
		}
		
		$this->wdb->trans_start();
		$this->wdb->delete('material_version', array('id' => $vid, 'mid' => $mid));
		$this->wdb->query("UPDATE material_info SET vernum=vernum-1 WHERE id=?", array($mid));
		$this->wdb->query("UPDATE material_attatch SET stat=0 WHERE mid=? AND mvid=?", array($mid, $vid));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 获取版本信息
	 * 
	 * @param unknown_type $vid
	 */
	public function get_version($vid)
	{
		if(empty($vid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT * FROM material_version WHERE id = ? ";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql, array($vid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$version = array();
			if ($query->num_rows() > 0)
			{
				$version = $query->row_array();
			} 
			return array('status' => 1, 'version' => $version);
		}
	}
	
	/**
	 * 获取版本附件
	 * 
	 * @param unknown_type $vid
	 */
	public function get_version_attachment($vid)
	{
		if(empty($vid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT * FROM material_attatch WHERE mvid = ? AND stat=1 ORDER BY id DESC";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql, array($vid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$version_attachment = array();
			if ($query->num_rows() > 0)
			{
				$version_attachment = $query->result_array();
			} 
			return array('status' => 1, 'version_attachment' => $version_attachment);
		}
	}
	
	/**
	 * 获取特定类型附件
	 * 
	 * @param $type 类型
	 * @param $ids ID
	 */
	public function get_type_attachment($type, $ids)
	{
		if( empty($type) || empty($ids))
		{
			return array('status' => 0);
		}
		$sql = "SELECT * FROM material_attatch WHERE id IN ({$ids}) AND pfix IN ({$type}) ORDER BY id DESC LIMIT 1";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$type_attachment = array();
			if ($query->num_rows() > 0)
			{
				$type_attachment = $query->row_array();
			} 
			return array('status' => 1, 'type_attachment' => $type_attachment);
		}
	}
	
	public function getCateMaterial($cid){
		if(is_numeric($cid)){
			$this->rdb->get_where();
		}else{
			return false;
		}
	}
	
	/**
	 * 查询素材总数
	 * 
	 * @param string $search
	 */
	public function get_total_material($search = '')
	{
		if(empty($search))
		{
			$sql = "SELECT COUNT(*) as total FROM material_info";
			$query = $this->rdb->query($sql);
		}
		else
		{
			$sql = "SELECT COUNT(*) as total FROM material_info WHERE mname LIKE ?";
			$sql = $this->rdb->escape_str($sql);
			$query = $this->rdb->query($sql, array('%' . $search . '%'));
		}
		
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$total = 0;
			if ($query->num_rows() > 0)
			{
				$result = $query->row_array();
			} 
			return array('status' => 1, 'total' => $result['total']);
		}
	}
	
	/**
	 * 获取所有素材
	 * @param int $page
	 * @param int $pre_page
	 * @param string $search
	 */
	public function get_all_materials($page = 1, $pre_page = 10, $search = '' )
	{
		$record = ($page - 1) * $pre_page;
		if(empty($search))
		{
			$sql = "SELECT mi.*, mc.cname, mc.clogo FROM material_info mi LEFT JOIN  material_cate mc ON mi.cid=mc.id ORDER BY id DESC LIMIT {$record},{$pre_page}";
			$sql = $this->rdb->escape_str($sql);
			$query = $this->rdb->query($sql);
		}
		else
		{
			$sql = "SELECT mi.*, mc.cname, mc.clogo FROM material_info mi LEFT JOIN  material_cate mc ON mi.cid=mc.id WHERE mi.mname LIKE ? ORDER BY id DESC LIMIT {$record},{$pre_page}";
			$sql = $this->rdb->escape_str($sql);
			$query = $this->rdb->query($sql, array('%' . $search . '%'));
		}
		
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$materials = array();
			if ($query->num_rows() > 0)
			{
				$materials = $query->result_array();
			} 
			return array('status' => 1, 'materials' => $materials);
		}
	}
	
	/**
	 * 批量获取版本信息
	 * 
	 * @param $vids array
	 */
	public function get_batch_verisions($vids)
	{
		if(empty($vids))
		{
			return array('status' => 0);
		}
		
		$this->rdb->where_in('id', $vids);
    	$query = $this->rdb->get('material_version');
    	
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$versions = array();
			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() as $version)
				{
					$versions[$version['id']] = $version;
				}
			} 
			return array('status' => 1, 'versions' => $versions);
		}
	}
	
	/**
	 * 查询素材附件数
	 * 
	 * @param array $mids
	 */
	public function get_material_attachments($mids)
	{
		if(empty($mids))
		{
			return array('status' => 0);
		}
		
		$this->rdb->select('mid, count(id) as num');
		$this->rdb->where_in('mid', $mids);
		$this->rdb->group_by('mid');
    	$query = $this->rdb->get('material_attatch');
    	
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$attachment_num = array();
			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() as $value)
				{
					$attachment_num[$value['mid']] = $value['num'];
				}
			} 
			return array('status' => 1, 'attachment_num' => $attachment_num);
		}
	}
	
	/**
	 * 批量设置素材状态
	 * 
	 * @param array $mids
	 * @param int $status
	 */
	public function batch_set_status($mids, $status)
	{
		if(empty($mids))
		{
			return array('status' => 0);
		}
		$this->wdb->where_in('id', $mids);
		$query = $this->wdb->update('material_info',array('state' => $status));
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			return array('status' => 1);
		}
	}
	
	/**
	 * 批量删除素材
	 * 
	 * @param array $mids
	 */
	public function batch_delete($mids)
	{
		if(empty($mids))
		{
			return array('status' => 0);
		}
		$this->wdb->trans_start();
		$this->wdb->where_in('id', $mids);
		$this->wdb->delete('material_info');
		$this->wdb->where_in('mid', $mids);
		$this->wdb->delete('material_version');
		$this->wdb->where_in('mid', $mids);
		$this->wdb->update('material_attatch', array('stat' => 0));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 修改版本详细说明
	 * @param string $content
	 * @param int $vid
	 * @param int $mid
	 */
	public function update_version_content($content, $vid, $mid)
	{
		if(empty($vid) || empty($mid))
		{
			return array('status' => 0);
		}
		
		$this->wdb->trans_start();
		$this->wdb->where_in('id', $vid);
		$this->wdb->update('material_version', array('content' => $content, 'nohtml' => strip_tags($content)));
		$this->wdb->where_in('id', $mid);
		$this->wdb->update('material_info', array('update_at' => time()));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 批量获取附件
	 * @param array $aids
	 */
	public function batch_get_attachments($aids)
	{
		if(empty($aids))
		{
			return array('status' => 0);
		}
		
		$this->wdb->where_in('id', $aids);
		$query = $this->wdb->get('material_attatch');
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$attachments= array();
			if ($query->num_rows() > 0)
			{
				$attachments = $query->result_array();
			} 
			return array('status' => 1, 'attachments' => $attachments);
		}
	}
	
	/**
	 * 查询允许用户
	 * @param unknown_type $mid
	 */
	public function allow_users($mid)
	{
		if(empty($mid))
		{
			return array('status' => 0);
		}
		
		$this->wdb->where(array('mid' => $mid, 'vr' => 2));
		$query = $this->wdb->get('material_visit_right');
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$users= array();
			if ($query->num_rows() > 0)
			{
				$users = $query->result_array();
			} 
			return array('status' => 1, 'users' => $users);
		}
	}
	
	/**
	 * 获取其他版本
	 * @param int $vid
	 * @param int $mid
	 * @param int $page
	 * @param int $per_page
	 */
	public function get_other_versions($vid, $mid)
	{
		if(empty($mid) || empty($vid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT * FROM material_version WHERE mid = ? AND id != ? ORDER BY cat DESC";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql, array($mid, $vid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$other_versions = array();
			if ($query->num_rows() > 0)
			{
				$other_versions = $query->result_array();
			} 
			return array('status' => 1, 'other_versions' => $other_versions);
		}
	}
	
	/**
	 * 查询同类型素材
	 * @param int $cid
	 * @param int $mid
	 * @param int $limit
	 */
	public function get_same_materials($cid, $mid, $limit = 5)
	{
		if(empty($cid) || empty($mid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT m.*, sum(mv.downnum) as downnum FROM material_info m LEFT JOIN material_version mv ON m.id=mv.mid WHERE m.cid = ? AND m.id != ? GROUP BY m.id ORDER BY downnum DESC,create_at DESC LIMIT {$limit}";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql, array($cid, $mid));
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$same_materials = array();
			if ($query->num_rows() > 0)
			{
				$same_materials = $query->result_array();
			} 
			return array('status' => 1, 'same_materials' => $same_materials);
		}
	}
	
	/**
	 * 统计用户素材数
	 * 
	 * @param array $uids
	 */
	public function get_user_material($uids)
	{
		if(empty($uids))
		{
			return array('status' => 0);
		}
		
		$this->rdb->select('uid,COUNT(id) AS num');
		$this->rdb->where_in($uids);
		$this->rdb->group_by('uid');
		$query = $this->rdb->get('material_info');
		if($query == FALSE)
		{
			return array('status' => 0, 'msg' => '');
		}
		else
		{
			$user_material = array();
			if ($query->num_rows() > 0)
			{
				 foreach($query->result_array() as $value)
				 {
				 	$user_material[$value['uid']] = $value;
				 }
			} 
			return array('status' => 1, 'user_material' => $user_material);
		}
	}
	
	/**
	 * 统计用户可查看素材列表
	 * 
	 * @param $uid
	 */
	public function count_view_material($uid)
	{
		if(empty($uid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT COUNT(*) as total FROM material_visit_right WHERE uid={$uid}";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$total = 0;
			if ($query->num_rows() > 0)
			{
				$result = $query->row_array();
			} 
			return array('status' => 1, 'total' => $result['total']);
		}
	}
	
	/**
	 * 查询用户可查看素材列表
	 * 
	 * @param int $uid
	 * @param int $page
	 * @param int $per_page
	 */
	public function get_view_material($uid, $page, $per_page)
	{
		if(empty($uid))
		{
			return array('status' => 0);
		}
		$offset = ($page - 1) * $per_page;
		
		$sql = "SELECT DISTINCT m.*,mc.cname FROM material_visit_right mv, material_info m LEFT JOIN material_cate mc ON m.cid=mc.id WHERE mv.mid=m.id AND mv.uid={$uid} LIMIT {$offset},{$per_page}";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$view_materials = array();
			if ($query->num_rows() > 0)
			{
				$view_materials = $query->result_array();
			} 
			return array('status' => 1, 'view_materials' => $view_materials);
		}
	}
	
	/**
	 * 统计用户上传素材列表
	 * 
	 * @param $uid
	 */
	public function count_upload_material($uid)
	{
		if(empty($uid))
		{
			return array('status' => 0);
		}
		
		$sql = "SELECT COUNT(DISTINCT m.id) AS total FROM  material_info m,material_version mv WHERE m.id=mv.mid AND (m.uid={$uid} OR mv.uid={$uid})";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$total = 0;
			if ($query->num_rows() > 0)
			{
				$result = $query->row_array();
			} 
			return array('status' => 1, 'total' => $result['total']);
		}
	}
	
	/**
	 * 查询用户上传素材列表
	 * 
	 * @param int $uid
	 * @param int $page
	 * @param int $per_page
	 */
	public function get_upload_material($uid, $page, $per_page)
	{
		if(empty($uid))
		{
			return array('status' => 0);
		}
		$offset = ($page - 1) * $per_page;
		
		$sql = "SELECT DISTINCT m.*,mc.cname FROM  material_version mv,material_info m LEFT JOIN material_cate mc ON m.cid=mc.id WHERE m.id=mv.mid  AND (m.uid={$uid} OR mv.uid={$uid}) ORDER BY m.id DESC LIMIT {$offset},{$per_page}";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query == FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			$upload_materials = array();
			if ($query->num_rows() > 0)
			{
				$upload_materials = $query->result_array();
			} 
			return array('status' => 1, 'upload_materials' => $upload_materials);
		}
	}
	
	/**
	 * 新增用户访问素材
	 * @param int $mid
	 * @param int $uid
	 */
	public function add_view_material($mid, $uid)
	{
		if( empty($mid) || empty($uid) )
		{
			return array('status' => 0);
		}
		
		$query = $this->wdb->insert('material_visit_right', array('mid' => $mid, 'uid' => $uid, 'vr' => 2));
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 删除用户访问素材
	 * @param array $mids
	 * @param int $uid
	 */
	public function remove_view_material($mids, $uid)
	{
		if( empty($mids) || empty($uid) )
		{
			return array('status' => 0);
		}
		
		$this->wdb->where('uid', $uid);
		$this->wdb->where_in('mid', $mids);
		$query = $this->wdb->delete('material_visit_right');
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	
	/**
	 * 检查用户访问素材
	 * @param int $mid
	 * @param int $uid
	 */
	public function check_view_material($mid, $uid)
	{
		if( empty($mid) || empty($uid) )
		{
			return array('status' => 0);
		}
		$this->rdb->where(array('mid' => $mid, 'uid' => $uid));
		$query = $this->rdb->get('material_visit_right');
		if($query)
		{
			if($query->num_rows() > 0)
			{
				return array('status' => 1, 'check' => TRUE);
			}
			else
			{
				return array('status' => 1, 'check' => FALSE);
			}
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 检查分类是否存在
	 * @param string $cate_name
	 */
	public function has_exists_cate($cate_name)
	{
		$this->rdb->where('cname', $cate_name);
		$total = $this->rdb->count_all_results('material_cate');
		return array('status' => 1, 'exists' => ($total > 0) ? TRUE : FALSE);
	}
	
	/**
	 * 新增分类
	 * @param string $cate_name
	 */
	public function create_cate($cate_name)
	{
		$query = $this->wdb->insert('material_cate', array('cname' => $cate_name));
		if($query)
		{
			return array('status' => 1, 'cid' => $this->wdb->insert_id());
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 检查分类下是否有素材
	 * @param int $cid
	 */
	public function has_material($cid)
	{
		$this->rdb->where('cid', $cid);
		$total = $this->rdb->count_all_results('material_info');
		return array('status' => 1, 'has' => ($total > 0) ? TRUE : FALSE);
	}
	
	/**
	 * 删除分类
	 * @param int $cid
	 * @param int $default_id
	 */
	public function delete_cate($cid, $default_id)
	{
		$this->wdb->trans_start();
		$this->wdb->where('id', $cid);
		$this->wdb->delete('material_cate');
		$this->wdb->where('cid', $cid);
		$this->wdb->update('material_info', array('cid' => $default_id));
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
		    return array('status' => 0);
		}
		
		return array('status' => 1);
	}
	
	/**
	 * 查询默认分类
	 * @param string $cate_name
	 */
	public function get_default_cate($cate_name)
	{
		$this->rdb->where('cname', $cate_name);
		$query = $this->rdb->get('material_cate');
		if($query)
		{
			$cate = array();
			if($query->num_rows() > 0)
			{
				$cate = $query->row_array();
			}
			return array('status' => 1, 'cate' => $cate);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 修改分类
	 * @param $cate
	 */
	public function update_cate($cate)
	{
		if(empty($cate))
		{
			return array('status' => 0);
		}
		$this->wdb->where('id', $cate['id']);
		unset($cate['id']);
		$query = $this->wdb->update('material_cate',$cate);
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 自定义修改数据
	 * @param int $vid
	 * @param array $update
	 */
	public function custom_update_version($vid, $update)
	{
		if( empty($update))
		{
			return array('status' => 0);
		}
		$this->wdb->where('id', $vid);
		foreach($update as $key => $value)
		{
			$this->wdb->set($key, $value, FALSE);
		}
		$query = $this->wdb->update('material_version');
		if($query)
		{
			return array('status' => 1);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 获取分类最热素材
	 * @param int $cid
	 */
	public function get_cate_hot_material($cid, $limit = 8)
	{
		if(empty($cid))
		{
			return array('status' => 0);
		}
		$sql = "SELECT m.id,m.mname,logo,SUM(mv.downnum) AS num FROM material_info m, material_version mv WHERE m.id=mv.mid AND m.state=1 AND m.cid={$cid} GROUP BY mv.mid ORDER BY num DESC, mv.id DESC LIMIT {$limit}";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		if($query)
		{
			$cate_hot_material = array();
			if($query->num_rows())
			{
				$cate_hot_material = $query->result_array();
			}
			return array('status' => 1, 'cate_hot_material' => $cate_hot_material);
		}
		else 
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 获取素材的一个附件
	 * @param int $mid
	 * @param string $type
	 */
	public function get_mate_one_attachemt($mid, $type = array())
	{
		$this->rdb->where('mid', $mid);
		if( ! empty($type))
		{
			$this->rdb->where_in('pfix', $type);
		}
		$this->rdb->limit(1);
		$query = $this->rdb->get('material_attatch');
		if($query)
		{
			$attachment = array();
			if($query->num_rows())
			{
				$attachment = $query->row_array();
			}
			return array('status' => 1, 'attachment' => $attachment);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 修改素材
	 * @param array $material
	 */
	public function edit_material($material)
	{
		if(empty($material))
		{
			return array('status' => 0);
		}
		 
		$this->wdb->trans_start();
		$update_material_info = array(
				'mname'  => $material['mname'],
				'cid' => $material['cid'],
				'update_at' => $material['update_time'],
				'logo' => $material['logo'],
				'vright' => $material['vright']
		);
		$this->wdb->where('id', $material['id']);
		$this->wdb->update('material_info', $update_material_info);
		 
		$this->wdb->where('mid', $material['id']);
		$this->wdb->delete('material_visit_right');
		if($material['vright'] == 3)
		{
			$insert_visit_vright = array();
			$vright_users = explode(',', $material['vright_user']);
			foreach($vright_users as $value)
			{
				$uid = (int) $value;
				if($uid > 0)
				{
					$insert_visit_vright[] = array(
							'mid' => $material['id'],
							'uid' => $uid,
							'vr' => 2
					);
				}
			}
			if( ! empty($insert_visit_vright))
			{
				$this->wdb->insert_batch('material_visit_right', $insert_visit_vright);
			}
		}
		 
		$this->wdb->trans_complete();
		$this->wdb->trans_off();
		if ($this->wdb->trans_status() === FALSE)
		{
			return array('status' => 0);
		}
		else
		{
			return array('status' => 1);
		}
	}
	
	/**
	 * 分页查询分类素材列表
	 * @param unknown $cat 分类ID
	 * @param unknown $page 第几页
	 * @param unknown $perpage  每页数量
	 */
	public function getCateList($cat, $page, $perpage)
	{	
		if($page <=0){
			$page = 1;
		}
		$start = ($page-1)*$perpage;
		$end = $perpage;
		if($cat == 0)
		{
			$sql = "select m.*,i.depict,i.cat,i.upat,i.nohtml from material_info as m left join material_version as i on m.cversion = i.id where m.state=1 order by i.upat DESC limit {$start},{$end}";
		}else
		{
			$sql = "select m.*,i.depict,i.cat,i.upat,i.nohtml from material_info as m left join material_version as i on m.cversion = i.id where m.state=1 and m.cid = {$cat} order by i.upat DESC limit {$start},{$end}";
		}
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		return $query->result_array();
	}
	/**
	 * 查询分类下的素材数以及分类名称
	 * @param unknown $cat 分类ID
	 */
	public function countCateList($cat)
	{
		if($cat == 0)
		{
			$sql ="select count(*) as num,c.cname,c.clogo,c.id from material_info as m left join material_cate as c on m.cid = c.id where m.state=1";
		}
		else
		{
			$sql ="select count(*) as num,c.cname,c.clogo,c.id from material_info as m left join material_cate as c on m.cid = c.id where m.state=1 and m.cid = {$cat}";
		}
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		$res = $query->row();
		return $res;
	}
	/**
	 * 模糊查询素材
	 * @param unknown $key 关键词
	 * @param unknown $page 第几页
	 * @param unknown $perpage  每页数量
	 * @param unknown $cur 分类 all表示全部
	 */
	public function get_materials_like($key, $page, $perpage, $cur)
	{
		$page = intval($page);
		$perpage = intval($perpage);
		if($page <=0){
			$page = 1;
		}
		$start = ($page-1)*$perpage;
		$end = $perpage;
		$key = $this->rdb->escape_str($key);
		$cur = $this->rdb->escape_str($cur)
		;
		$sql = "select distinct v.id as vid, m.*,v.*,c.cname,c.clogo  from material_info as m right join material_version as v  on m.id = v.mid ";
		$sql .= " left join material_cate as c on m.cid = c.id ";
		$sql .= " LEFT JOIN material_attatch AS a ON m.id = a.mid ";
		$sql .= " where (m.mname like '%".$key."%' or v.nohtml like '%".$key."%'  or a.sname like '%".$key."%') and m.state=1 ";
		if(($cur != 'all') && is_numeric($cur)){
			if($cur > 0){
				$sql .= " and m.cid = ".$cur;
			}
		}
		$sql .= " order by m.update_at DESC limit {$start},{$end}";
		$query = $this->rdb->query($sql);
		$res = $query->result_array();
		return $res;
	}
	/**
	 * 计算模糊查询的结果
	 * @param unknown $key
	 * @param unknown $cur
	 */
	public function count_materials_like($key, $cur)
	{
		
		$key = $this->rdb->escape_str($key);
		$cur = $this->rdb->escape_str($cur);
		$sql = "select distinct v.id as vid, m.*,v.*,c.cname,c.clogo  from material_info as m right join material_version as v  on m.id = v.mid ";
		$sql .= " left join material_cate as c on m.cid = c.id ";
		$sql .= " LEFT JOIN material_attatch AS a ON m.id = a.mid ";
		$sql .= " where (m.mname like '%".$key."%' or v.nohtml like '%".$key."%'  or a.sname like '%".$key."%') and m.state=1 ";
		if(($cur != 'all') && is_numeric($cur)){
			if($cur > 0){
				$sql .= " and m.cid = ".$cur;
			}
		}
		
		$query = $this->rdb->query($sql);
		$res = $query->row();
		return $res->num;
	}
	/**
	 * 获取任意版本的附件信息
	 * @param unknown $mid
	 * @param unknown $vid
	 * @return unknown
	 */
	public function getSnapshot($mid, $vid){
		$sql = "select * from material_attatch where mid = $mid and mvid = $vid";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		$res = $query->result_array();
		return $res;
		
	}
	/**
	 * 获取素材的默认版本号
	 * @param unknown $mid
	 */
	public function getDefaultVersion($mid){
		
		$sql = "select * from material_info where id = $mid";
		$sql = $this->rdb->escape_str($sql);
		$query = $this->rdb->query($sql);
		$res = $query->result_array();
		if(sizeof($res)>0){
			return $res[0]['cversion'];
		}
		return 0;	
	}
}