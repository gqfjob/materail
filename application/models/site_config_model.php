<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Site_config Model Class
 * 
 * @author gxy
 */
class Site_Config_Model extends CI_Model
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
	 * 获取系统配置
	 * @param array $keys 配置key
	 */
	public function get_site_config($keys = array())
	{
		if( ! empty($keys))
		{
			$this->rdb->where_in('skey', $keys);
		}
		$query = $this->rdb->get('site_config');
		if($query)
		{
			$site_config = array();
			if($query->num_rows() > 0)
			{
				foreach($query->result_array() as $value)
				{
					$site_config[$value['skey']] = $value;
				}
			}
			return array('status' => 1, 'site_config' => $site_config);
		}
		else
		{
			return array('status' => 0);
		}
	}
	
	/**
	 * 获取系统配置
	 * @param array $config 配置选项
	 */
	public function set_site_config($config)
	{
		
		if( empty($config))
		{
			return array('status' => 0);
		}
		$insert = '';
		foreach($config as $value)
		{
			$insert .= ",('{$value['skey']}','{$value['svalue']}')";
		}
		$sql = "REPLACE INTO site_config (skey,svalue) VALUES " . substr($insert,1);
		$query = $this->wdb->query($sql);
		
		if( ! $query)
		{
		    return array('status' => 0);
		}
		return array('status' => 1);
	}
}
