<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * user
 *
 * @author
 **/
class User_model extends CI_Model 
{
	private $table ="identity_user";
	function __construct()
	{
		parent::__construct();
		$this->rdb = getReadOnlyDB();
        $this->wdb = getWriteOnlyDB();
	}
	/**
	 * 写注册信息
	 * 
	 * @param array $user
	 */
	function register($user){
	    $u = array();
	    $u['nickname'] = $user['nickname'];
	    $u['email'] = $user['email'];
	    if(isset($user['tno'])){
	        $u['tno'] = $user['tno'];
	    }
        if(isset($user['auth'])){
        	$u['auth'] = $user['auth'];
        }
	    $this->wdb->trans_start();
	    //注册基本信息
	    $this->wdb->insert('identity_user',$u);
	    $uid = $this->wdb->insert_id();
	    //创建两个凭证
	    $credit = array();
	    if(!empty($user['email'])){
	       array_push($credit,array(
               'uid' => $uid,
               'type' => 2,
               'name' => $user['email']
           ) );
	    }
	    if(!empty($user['name'])){
           array_push($credit,array(
               'uid' => $uid,
               'type' => 1,
               'name' => $user['nickname']
           ) );
        }


        if(sizeof($credit)>0){
	       $this->wdb->insert_batch('identity_credential',$credit);
        }
	    //记录密码
	    $this->wdb->insert('identity_password',array('uid'=>$uid,'pwd'=>sha1($user['password'])));
	    $this->wdb->trans_complete();
	    if ($this->wdb->trans_status() === FALSE){
	        //注册失败
	        $uid = 0;
	    }
	    return $uid;
	}
	/**
     * 创建两个凭证
	 */
	public function createCredit($user){
	        //创建两个凭证
        $credit = array();
        if(!empty($user['email'])){
           array_push($credit,array(
               'uid' => $user['id'],
               'type' => 2,
               'name' => $user['email']
           ) );
        }
        if(!empty($user['nickname'])){
           array_push($credit,array(
               'uid' => $user['id'],
               'type' => 1,
               'name' => $user['nickname']
           ) );
        }
        $uid = 0;
        if(sizeof($credit)>0){
        	$this->wdb->trans_start();
            $this->wdb->insert_batch('identity_credential',$credit);
            //记录密码
            $this->wdb->insert('identity_password',array('uid'=>$user['id'],'pwd'=>sha1($user['password'])));
	        $this->wdb->trans_complete();
            if ($this->wdb->trans_status() === FALSE){
	            //注册失败
	            $uid = 0;
	        }else{
	        	$uid=$user['id'];
	        }
	        
        }
        return $uid;
	}
	/**
	 * 检查凭证是否已经存在
	 * 
	 * @param string $str
	 */
	public function isExist($str){
	    return true;
	}
	/**
	 * 查看用户是否合法
	 * 
	 * @param $name
	 * @param $pwd
	 */
	public function check($name,$pwd){
	    $sql = "SELECT * from identity_password AS ip 
                LEFT JOIN identity_credential AS ic ON ip.uid = ic.uid 
                WHERE ic.`name` = ? and ip.pwd = ? LIMIT 1";
	    $query = $this->rdb->query($sql, array($name,sha1($pwd)));
    	if ($query->num_rows() == 1){
           $row = $query->row_array(); 
           return $row['uid'];
        }else{
            return 0;
        }
	}
    /**
     * 查看用户是否合法,并返回用户信息
     * 
     * @param $name
     * @param $pwd
     */
    public function checkWithUser($name,$pwd){
        $sql = "SELECT * from identity_password AS ip 
                LEFT JOIN identity_credential AS ic ON ip.uid = ic.uid 
                WHERE ic.`name` = ? and ip.pwd = ? LIMIT 1";
        $user_credit = $this->rdb->query($sql, array($name,sha1($pwd)));
        if ($user_credit->num_rows() == 1){
           $row = $user_credit->row_array(); 
           $query = $this->rdb->get('identity_user',array('id'=>$row['uid']));
           $res = $query->result();
           if(sizeof($res) == 1)
           {
               return  $res[0];
           }else{
               return false;
           }
        }else{
            return false;
        }
    }

    /**
     * 查看用户是否合法
     * 
     * @param $name
     */
    public function checkUsername($name){
        $sql = "SELECT * from identity_credential AS ic 
                WHERE ic.`name` = ? LIMIT 1";
        $user_credit = $this->rdb->query($sql, array($name));
        if ($user_credit->num_rows() == 1){
           $row = $user_credit->row_array(); 
           $query = $this->rdb->get('identity_user',array('id'=>$row['uid']));
           $res = $query->result();
           if(sizeof($res) == 1)
           {
               return  $res[0];
           }else{
               return false;
           }
        }else{
            return false;
        }
    }    
    
    /**
     * 为用户生成登录token
     */
    public function createToken($uid,$ip,$ttl=1800,$client=1,$tti=1800){
        //生成一个token,检查是否开启缓存，已开启，存放到缓存，未开启存放到数据库
        $token = sha1(getRandCode(rand(8,15)));//随机产生一个8-15位的随机码
        $now = time();
        $ttl_live = $now + $ttl*60;//token存活时间定义为分钟
        //先检查 uid 和 type 的token是否存在，存在则先删除 :踢出上一个用户登录，并记录踢出者情况  ,redis中怎么处理？？？
        $this->wdb->trans_start();
        $query = $this->wdb->get_where('identity_session',array('uid'=>$uid, 'client_type'=>$client),1);
        $result = $query->result_array();
        if(sizeof($result) > 0){
            foreach($result as $row){
                $this->wdb->delete('identity_session', array('id' => $row['id']));//删除上次登录记录
                //$this->wdb->delete('identity_session_kicked', array('owner_uid' => $row['uid']));//删除上次被踢记录
               // $this->wdb->insert('identity_session_kicked',array('token'=>$row['token'],'uid'=>$uid,'owner_uid'=>$row['uid'],'from_ip'=>$ip,'ctime'=>$now));
            }
        }
        $this->wdb->delete('identity_session', array('token' => $token));//删除可能存在的冲突，如果token存在，则删除
               
        $this->wdb->insert('identity_session',array('uid'=>$uid,'token'=>$token,'tti'=>$tti,'ttl'=>$ttl_live,'create_time'=>$now,'client_ip'=>$ip,'last_active_time'=>$now,'client_type'=>$client));
        //更新用户登录时间/IP
        $sql = "UPDATE `identity_user` SET `last_login_time` = ?, `last_login_ip` = ? WHERE `id`=?";
        $this->wdb->query($sql,array($now,$ip,$uid));
        $this->wdb->trans_complete();
        if($this->wdb->trans_status() === FALSE){
            return false;
        }else{
            return $token;
        }
    }
    /**
     * 验证用户token是否有效
     * 
     * @param string $token
     * @param boolean $user
     */
    public function valideToken($token,$user=false){
        $redis = getRedis(CACHE_SESSION);
        if($redis){
            $opts = $redis->getOption(Redis::OPT_PREFIX);
            $exist = $redis->exists($token);
            $userPacked = $redis->get($token);
            if($userPacked){
                if($user){
                    return  json_decode($userPacked,true);
                }else{
                    return 1;
                }
            }
        }else{
            $this->rdb->order_by("last_active_time","desc");
            $now = time();
            $query = $this->rdb->get_where('identity_session',array('token'=>$token,'ttl >'=>$now),1);
            $result = $query->result_array();
            if(sizeof($result) == 1){
                if($user){
                    //TODO:获取用户信息
                    $uq = $this->rdb->get_where('identity_user',array('id'=>$result[0]['uid']));
                    $r = $uq->result_array();
                    //获取用户相关凭证
                    $cq = $this->rdb->get_where('identity_credential',array('uid'=>$result[0]['uid']));
                    $c = $cq->result_array();
                    $r[0]['credit'] = $c;
                    return $r[0];
                }else{
                    return 1;//正常登陆
                }
            }
        }

        //验证是否是踢出的
       /* $sql = "SELECT `token` FROM `identity_session_kicked` where `token` = ?  and uid != owner_uid";
        $query = $this->wdb->query($sql, array($token));
        if($query->num_rows()>0){
            $this->wdb->delete('identity_session_kicked',array('token'=>$token));
            return -1;//用户被人踢出
        }*/
        return 0;//token失效
    }
    
    /**
     * 注销，清除token
     */
    public function delToken($token){
        $query = $this->wdb->get_where('identity_session',array('token'=>$token));
        $res = $query->result_array();
        if(sizeof($res) >0){
            $this->wdb->delete('identity_session',array('uid'=>$res[0]['uid']));
            $this->wdb->delete('identity_session_kicked',array('uid'=>$res[0]['uid'],'owner_uid'=>$res[0]['uid']));
        }
    }
    
    public function getUser($uid){
        $q = $this->rdb->get_where('identity_user',array('id'=>$uid));
        return $q->first_row();
    }
    public function batchGetUser($uids){
    	$this->rdb->where_in('id',$uids);
    	$query = $this->rdb->get('identity_user');
    	$result = $query->result_array();
    	$u = array();
    	if(is_array($result) && sizeof($result)>0){
    		foreach ($result as $r){
    			$u[$r['id']] = $r;
    		}
    	}
    	return $u;
    }
    
    function getUserFull($uid){
    	$sql = "SELECT
                u.*
                FROM identity_user AS u 
                WHERE u.id=?";
    	$q = $this->rdb->query($sql,array($uid));
    	return $q->first_row();
    }
    /**
     * 创建ssoUser，不存在就创建，存在则更新
     */
    public function createOrUpdateSSOUser($user){
        if(is_array($user)){
            //查询凭证是否存在
            //获取用户相关凭证
            $cq = $this->wdb->get_where('identity_credential',array('name'=>$user['id'],'type'=>4));
            $c = $cq->result_array();
            $len = sizeof($c);
            if($len == 1){
                //存在，更新对应的用户信息
                $uid = $c[0]['uid'];
                $this->wdb->update('identity_user',array('nickname'=>$user['name'],'tno'=>$user['id']),array('id'=>$uid));
            }else{
                //查是否注册过（用户名对的上），注册过则绑定这个OA账户
            	$oa = $this->wdb->get_where('identity_user',array('tno'=>$user['id']));
                $coa = $oa->result_array();
            	if(sizeof($coa) == 1){
	                $uid = $coa['id'];
	                
	                //创建关联凭证
	                $credit =array(
	                       'uid' => $uid,
	                       'type' => 4,
	                       'name' => $user['id']
	                        );
	                $this->wdb->insert('identity_credential',$credit);
	            }else{
	                //不存在，新建用户，插入凭证
	                $u = array();
	                $u['nickname'] = $user['name'];
	                $u['tno'] = $user['id'];
	                $u['email'] = '';
	                //查询oa表中的真实姓名。存在则插入
                    $oauser = $this->wdb->get_where('oa_user',array('tno'=>$u['tno']));
                    $coauser = $oauser->row_array();
                    if(sizeof($coauser) == 1){
                    	$u['realname'] = $coauser['name'];
                    }
	                $this->wdb->trans_start();
	                //注册基本信息
	                $this->wdb->insert('identity_user',$u);
	                $uid = $this->wdb->insert_id();
	                //创建凭证
	                $credit =array(
	                       'uid' => $uid,
	                       'type' => 4,
	                       'name' => $user['id']
	                        );
	                $this->wdb->insert('identity_credential',$credit);
	                $this->wdb->trans_complete();
	                if ($this->wdb->trans_status() === FALSE){
	                    //注册失败
	                    $uid = 0;
	                }
	            }
            }
            //返回用户ID
            return $uid;
        }
    }
    


    /**
     * 
     * 获取用户列表
     * @param $start 开始页数
     * @param $limit 每页
     * @param $order 排序标准
     * @param $seq 倒序/正序
     * @param $ky 搜索关键词
     * @param $type 筛选类型 all全部，ad管理员，pd评审专家，bind：禁止访问
     */
    function getAdminUserlists($start=0, $limit=30,$order="time",$seq="DESC",$kw='',$type='all'){
    	$sql = "SELECT
				u.nickname,
				u.realname,
				u.id,
				u.auth,
				u.ispro,
				u.status,
				oa.tno,
				sc.coin,
				sc.score,
				r.rights,
				r.static,
				oa.depart FROM identity_user AS u LEFT JOIN oa_user AS oa ON u.tno = oa.tno LEFT JOIN identity_user_score AS sc ON u.id = sc.uid LEFT JOIN identity_user_admin AS r ON u.id = r.uid
 WHERE 1=1 ";
    	if($kw !=''){
    		$sql .= " and u.nickname LIKE '%".$kw."%'  or u.realname LIKE '%".$kw."%' or u.tno LIKE '%".$kw."%' ";
    	}
    	if($type != 'all'){
    		switch ($type) {
    			case 'ad':
    			$sql .= " and `auth` in (2,999) ";
    			break;
                case 'pd':
                $sql .= " and `ispro`=1 ";
                break;
                case 'bind':
                $sql .= " and `status`=2 ";
                break;
    		}
    	}
        if('coin'==$order){
    	   $sql .=" ORDER BY sc.coin ".$seq." ";
        }else if('scroe' ==$order){
    	   $sql .=" ORDER BY sc.score ".$seq." ";
        }else{
    	   $sql .=" ORDER BY u.create_time ".$seq." ";
        }
        
        $sql .=" LIMIT $start, $limit ";
        $query = $this->rdb->query($sql);
        return $query->result_array();
    }
    /**
     * 
     * 查询uid对应用户的后台管理员权限
     * @param $uid
     */
    function getAdminRights($uid){
        $sql = "SELECT auth FROM $this->table WHERE id = ? "; 
        $query = $this->rdb->query($sql,array($uid));
        if($query->num_rows > 0){
           $result = $query->row_array();
           if($result['auth'] > 1)return $result['auth'];
        }
        return 1;
    }

    /**
     * 
     * 更新用户信息，必须有一个id
     * @param $arr
     */
    function updateInfo($arr){
    	if(isset($arr['id'])){
    		$uid = $arr['id'];
    		unset($arr['id']);
    		return $this->wdb->update($this->table, $arr, array('id' => $uid));
    	}
    	return false;
    }

    /**
     * 
     * 根据某个列的值查询用户数
     * @param $col
     * @param $val
     * @param $like 是否用like查询
     * @param $type 筛选类型 all全部，ad管理员，pd评审专家，bind：禁止访问
     */
    function getUserCounter($col='',$val='',$like=false,$type='all'){
        if($type != 'all'){
            switch ($type) {
                case 'ad':
                $this->rdb->where_in('auth', array(2,999));
                break;
                case 'pd':
                $this->rdb->where('ispro', 1);
                break;
                case 'bind':
                $this->rdb->where('status', 2);
                break;
            }
        }
    	if($like){
    		$this->rdb->like($col, $val);
    		$this->rdb->or_like("realname", $val);
    		$this->rdb->or_like("tno", $val);
    	}else{
    		if($col!=''){
    			$this->rdb->where($col, $val);
    		}
    	}
    	$this->rdb->from($this->table);
    	return $this->rdb->count_all_results();
    }
    
    
    /**
     * 批量插入用户
     */
    public function batchIn($names, $uids){
    	$credit  = array();
    	$passwords  = array();
    	$pw = "123456";
    	foreach ($uids as $k=>$uid){
           array_push($credit,array(
               'uid' => $uid,
               'type' => 1,
               'name' => $names[$k]
           )); 
           array_push($passwords,array(
               'uid' => $uid,
               'pwd' => sha1($pw)
           )); 
    	}
    	
    	$this->wdb->insert_batch('identity_credential',$credit);
    	$this->wdb->insert_batch('identity_password',$passwords);
    	return true;
    }
    /**
     * 
     * 批量插入用户到Identity
     * @param unknown_type $emails email
     * @param unknown_type $realnames 真实姓名
     * @param unknown_type $tnos 工号
     */
    public function batchId($emails, $realnames,$tnos){
    	$user  = array();
        foreach ($emails as $k=>$e){
           array_push($user,array(
               'nickname' => substr($e,0,strpos($e,'@')),
               'tno' => $tnos[$k],
               'realname' => $realnames[$k],
           	   'status' => 1,
           		'ispro' => 1,
           		'last_login_time'=>1400665539,
           		'last_login_ip'=>'127.0.0.1',
           		'auth'=>1
           )); 
 
    	}
    	$this->wdb->insert_batch('identity_user',$user);
    }
}
