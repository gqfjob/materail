<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_Bg_Header_module extends CI_Module {

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

    function index($arr=array(),$metas=array())
    {
       
    	$data = array();
    	$data['admin_name'] = $this->session->userdata('SUPER_ADMIN');
    	//自定义标题，描述等
        if(is_string($arr)){
            $data['title'] = $arr;
        }else if(is_array($arr)){
            if(isset($arr['title']))
            {
               $data['title'] = $arr['title'];
            }else{
               $data['title'] = '';
            }
            if(isset($arr['description']))
            {
               $data['description'] = $arr['description'];
            }else{
               $arr['description'] = '';
            }
            if(isset($arr['keywords'])){
                 $data['keywords'] = $arr['keywords'];
            }else{
                $data['keywords'] = '';
            }
            if(isset($arr['cur'])){
                $data['cur'] = $arr['cur'];
            }
        }
        
        $this->load->view('bg_header',$data);
    }
}