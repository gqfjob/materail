<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_Header_module extends CI_Module {

    /**
     * 构造函数
     *
     * @return void
     * @author
     **/
    function __construct()
    {
        parent::__construct();
        
    }
	
	/**
	 * 首页
	 *
	 * @param $arr 标题，描述等基本属性        	
	 * @param $metas 扩展meta        	
	 */
	function index($title = "", $cur = 0, $description = "", $keywords = "") {
		$data ['curl'] = urlencode ( current_url () );
		
		// 获取当前tab
		$data ['cur'] = $cur;
		if($cur == 0){
			$data['tab'] = "all";
		}else{
			$data['tab'] = $cur;
		}
		$this->load->model ( "config_model" );
		//从数据库获取标题，通知配置
		$tmpTitle = $this->config_model->getModelsBykey("skey","SITE_TITLE","*","site_config");//自定义标题
		$data['is_notice'] = $this->config_model->getModelsBykey("skey","IS_NOTICE","*","site_config");//是否显示通知
		$data['notice'] = $this->config_model->getModelsBykey("skey","SITE_NOTICE","*","site_config");//网站通知
		// 自定义标题，描述等
		if(!empty($tmpTitle->svalue))$title = $tmpTitle->svalue;
		$data ['title'] = $title;
		$data ['description'] = $description;
		$data ['keywords'] = $keywords;

		// 获取素材分类
		$this->load->model ( "material_model" );
		$allCate = $this->material_model->get_material_cate ();
		$data ['cate'] = $allCate ['material_cate'];
		$this->load->view ( 'header', $data );
	}
    
    public function getLinks(){
    	$data = array();
    	$this->load->view('links',$data);
    }
    public function getUeditor(){
        $data = array();
        $this->load->view('ueditor',$data);
    }
}