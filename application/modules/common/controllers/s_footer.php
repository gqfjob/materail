<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_S_Footer_module extends CI_Module {

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

    function index()
    {
        $this->load->view('s_footer');
    }
}