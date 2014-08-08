<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		
		$this->load->module("common/header",array('title'=>'首页'));
		$this->load->view('page/index');
		$this->load->module("common/footer");	
	}
	/**
	 * 缓存读写测试
	 * Enter description here ...
	 */
	public function redisTest(){
		//$var = getCache('sessions:ede97052b7b6fa8aa12621bf58c2cccc');//sessions域另外一种表达
		//$var = getCache('ede97052b7b6fa8aa12621bf58c2cccc','sessions');//sessions域
		setCache("guoqiang", "cache test","code");
		$var = getCache("guoqiang",'code');
		var_dump($var);
	}
	/**
	 * modules测试
	 */
    public function moduleTest(){
        
    }

    /**
     * debug测试
     */
    public function debugTest(){
        debug_log("test");
    }
    /**
     * 读写分离测试
     */
    public function readWriteTest(){
        
    }
    /**
     * solr读写测试
     */
    public function solrTest(){
        return 1;
    }
    /**
     * redis队列测试
     * Enter description here ...
     */
    public function queueTest(){
        $this->load->helper('redis_loader');
        $this->load->library('Queue');
        $redis = getRedis();
        $queue = new Queue();
        $queue->init($redis);
        $name = "Redis";//队列名称在redis里面显示为前缀
        $queue->put($name, "Redis消息队列测试－－".rand(0,100));
        $queue->put($name, "Redis消息队列测试－－".rand(0,100));
        $queue->put($name, "Redis消息队列测试－－".rand(0,100));
        //$temp = $queue->get($name);
        //var_dump($temp);
        $tempB = $queue->status($name);
        var_dump($tempB);
        //$queue->reset($name);//清除队列
        
    }
    

    /**
     * 
     * 
     */
    public function temp(){
    	$this->load->view('test/templates');
    }
    
    /**
     * 测试ueditor
     */
    public function ue(){
    	$this->load->view('test/ueditor');
    }
    /**
     * 测试modal
     */
    public function md(){
        $this->load->view('test/modal');
    }
    
    /**
     * 访问日志记录
     * Enter description here ...
     */
    public function tongji(){
    	$this->load->library('user_agent');
    	$r['curl'] = $_SERVER['HTTP_REFERER'];//当前请求地址
    	$r['browser'] = $this->agent->browser();//浏览器
    	$r['browserVer'] = $this->agent->version();//浏览器版本
    	$r['browserAll'] = $this->agent->browser().' '.$this->agent->version();
    	$r['agent'] = $this->agent->agent_string();
    	$r['ip'] = $this->input->ip_address();
    	$r['reference'] = urldecode($this->input->get('ur'));
    	$r['isrobot'] = ($this->agent->is_robot())?1:0;
    	$r['robot'] = $this->agent->robot();
    	$r['platform'] = $this->agent->platform();
    	$r['time'] = mktime();
    	$r['ctitle'] = urldecode($this->input->get('t'));
    	$user = checklogin();
    	if(!$user){
    		$r['uid'] = 0;//匿名
    	}else{
    		$r['uid'] = $user['id'];
    	}
    	$r['usign'] = get_cookie('sign',true);
    
    	$this->load->model('visit_log_model');
    	$this->visit_log_model->save($r);
    	//debug_log($r['ctitle'],'log');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */