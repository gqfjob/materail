<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli extends CI_Controller {

	public function index(){
		/*
		 if(!$this->input->is_cli_request()){
			show_404();
			}*/
		//$this->load->model("");
		echo "somthings";
		$param = array();
		register_shutdown_function(array($this, '_doFunc'), $param);
		if (function_exists("fastcgi_finish_request")) {
			
			fastcgi_finish_request();//立即输出 somthings 并 触发 shutdown 函数运行。
		}else{
			echo "anythings";
		}

	}
	public function _doFunc($param)//异步执行函数
	{
		//不可以使用header函数 因为header已经发送
		//保存session，转换上传的视频，处理统计，发送email等耗时操作
		sleep(50);
		debug_log("test");
	}

}