<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_Bg_Left_module extends CI_Module {

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

    function index($currentID = 1)
    {
       /*if($this->session->userdata('SUPER_ADMIN') != 'admins'){
			
		    redirect(base_url('admin/login'));
		    exit;
		}*/
    	$data = array();
    	$uripath = uri_string();
    	for($i=1;$i<=9;$i++){
    		$data['current'.$i] = ''; 
    	}
    	$data['current'.$currentID] = 'current';
        $this->load->view('bg_left',$data);
    }
}