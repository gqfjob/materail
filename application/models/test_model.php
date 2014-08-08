<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * user
 *
 * @author
 **/
class Test_model extends CI_Model {
    private $table ="test_db";
    function __construct()
    {
        parent::__construct();
        $this->rdb = getReadOnlyDB();
        $this->wdb = getWriteOnlyDB();
    }
    function insert(){
    	return $this->wdb->insert($this->table,array('name'=>'gip'));
    }
}