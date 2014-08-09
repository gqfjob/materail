<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('user_model','user');
        $this->load->helper('cookie');
        $this->load->model('config_model');
    }

	public function register(){
		$this->load->module("common/header");
		$this->load->view("user/register");
		$this->load->module("common/footer");
	}
	public function registerDo(){
        $user['nickname'] = trim($this->input->post('user_name', true));//昵称
        $user['email'] = trim($this->input->post('user_email', true));//email
        $user['password'] = trim($this->input->post('password', true));//email
		//对数据进行安全性验证
        //查该用户是否已经注册过(两个凭证:用户名,email)
        $ocredits = $this->config_model->getModelsBykeyarray(array('type'=>1,'name'=>$u['nickname']),'identity_credential');
        $emailcredits = $this->config_model->getModelsBykeyarray(array('type'=>2,'name'=>$user['email']),'identity_credential');
        
        if(sizeof($ocredits)>0){//昵称已经存在
        	echo RST('',100003,'注册失败，用户名已经存在，请更换昵称');
        	die(0);
        }
	    if(sizeof($emailcredits)>0){//email已经存在
            echo RST('',100003,'注册失败，email已经存在，是否已经注册过?');
            die(0);
        }
        if(1){

            $mailConf = $this->config->item('mail');
            $this->load->library('Smtp');

            $smtpusermail = "cxyfzx@js.chinamobile.com";//SMTP服务器的用户邮箱 
            $smtpemailto = $user['email'];//发送给谁 
            $mailsubject = "=?UTF-8?B?".base64_encode("创新梦工厂用户注册信息")."?=";//邮件主题 
			$mailbody = "";//发送激活邮件内容 
			$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件 
			
			$smtp = new Smtp($mailConf['smtp_host'],$mailConf['smtp_port'],true,$mailConf['smtp_user'],$mailConf['smtp_pass']);//这里面的一个true是表示使用身份验证,否则不使用身份验证. 
			//$send = $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
            $send = true;
			//发送密码

			$this->user->register($user);
			
			
			if($send){
		        echo RST('',0,'注册成功，请登录您的邮箱以激活用户');
			}else{
				echo RST('',0,"注册完成，但邮件发送失败了，请联系管理员查看原因");
			}
            
        }else{
        	echo RST('',100002,'注册失败，请确认您的工号和工号对应的邮箱是否正确输入！');
        }
        die(0);
	}
	public function login(){
	    $data['callback'] = $this->input->get_post('callback',true);
		$this->load->module("common/s_header");
		$this->load->view("user/index",$data);
		$this->load->module("common/s_footer");
		

	}

	public function loginDo(){
	    $loginName = trim($this->input->post('loginName', true));
        $loginPwd = trim($this->input->post('loginPwd', true));
        if(isset($_POST['callback']) || isset($_GET['callback'])){
            $callback = urldecode($this->input->get_post('callback',true));
        }else{
        	$callback = false;
        }
        $ajax = $this->input->post('ajax',true);
        //先查询用户状态
        $user = $this->user->checkWithUser($loginName, $loginPwd);
        $checkName = $this->user->checkUsername($loginName);
        if($user){
        	if($user->status ==2){//用户被禁止登录
        		if($ajax){
        			echo RST(urlencode(base_url()),100002,'登录失败，您已被禁止登录');
        			die(0);
        		}else{
        			show_error("登录失败，您已被禁止登录");
        		}
        	}else{
		        $token = $this->_loginDo($loginName, $loginPwd);
		        
		        if($token != ''){
		            //用户名验证成功
		            //获取回调信息
		            if(!$callback ){
		                $callback = base_url();
		            }
		            if($ajax){
		                echo RST(urlencode($callback),0,'登录成功');
		                exit(0);
		            }else{
		                redirect($callback);
		            }
		        }else{
		            if($ajax){
		                echo RST('用户名或者密码错误，登录失败',100001,'用户名或者密码错误，登录失败');//登录失败
		                exit(0);
		            }else{
		                show_error("登录失败");
		            }
		        }
        	}
        }else{
        	if($ajax){
        		if($checkName ){
        			echo RST('登录失败，密码错误',100003,'登录失败，密码错误');//登录失败
        		}else{
        			echo RST('登录失败，账户不存在',100003,'登录失败，账户不存在');//登录失败
        		}
        		exit(0);
        	}else{
        		if($checkName ){
        			show_error("登录失败,密码错误");
        		}else{
        			show_error("登录失败,账户不存在");
        		}
        	}
        }
        
        
	}
	
	private function _loginDo($loginName,$loginPwd){
	    $token = '';
	    if(($loginPwd == false) && is_array($loginName)){
	      //检查$loginName['type'] == sso,$loginName['uid']>0
	      if($loginName['type'] == 'sso') {
	          $uid = $loginName['uid'];
	      } 
	    }else{
		  $uid = $this->user->check($loginName,$loginPwd);
	    }
		if($uid){
            $ip = $this->input->ip_address();
            //用户信息保存时间
            $exptime = $this->config->item('cookie_expiration');
            $cookie_prefix = $this->config->item('cookie_prefix');
            $cookie_domain = $this->config->item('cookie_domain');
            $cookie_path = $this->config->item('cookie_path');
            $exptime = $this->config->item('cookie_expiration');
		    $token = $this->user->createToken($uid,$ip,$exptime);
            $user = $this->user->getUser($uid);
            //保存用户信息到redis
		    if($user){
		        $redis = getRedis(CACHE_SESSION);
		        if($redis){
		            $redis->setex($token,$exptime,json_encode($user));
		        }
		    }
		    //生成的token保存在cookie中
		    $token_name = $this->config->item('user_login_cookie');
		    //$res = $this->session->set_userdata(array($token_name=>$token));
		    
		    $res = set_cookie($token_name,$token,$exptime,$cookie_domain,$cookie_path,$cookie_prefix);
		    //登录奖励积分
		    //getScore($uid, "user-login");
		    //长期登录，保存一个加密的用户信息到cookie，下次先检查是否存在
		    //set_cookie($this->config->item('sess_cookie_name'),$token,$cookie_expiration,$cookie_domain,$cookie_path,$cookie_prefix);
		    //记录登录日志
		    $this->config_model->insert_model("user_logs",array('uid'=>$uid,'ctime'=>mktime(),'ctype'=>1));
		}
		return $token;
	}

	public function ssologin(){
	    //记录是否有callback，有则写入cookie
	    $callback = $this->input->get_post('callback',true);
	    if($callback){
	        $domain = $this->config->item('cookie_domain');
	        set_cookie('lcback',base64_encode($callback),0,$domain);
	    }
        //跳转到登录页面 ，带上callback? 
        $ssologinUrl = $this->config->item('ssologin');
        redirect($ssologinUrl);
	}
	
	public function ssologinCallback(){
	    $callback = $this->input->get_post('callback',true);
	    if($callback){
	        $callback = urldecode($callback);
	    }else{
	        $c = get_cookie('lcback',true);
	        if($c){
	            $callback = base64_decode($c);
	            $domain = $this->config->item('cookie_domain');
	            set_cookie('lcback',base64_encode($callback),-1,$domain);
	        }
	    }
	    //检查cookie值 ObSSOCookie
	    $co = get_cookie('ObSSOCookie',true);
	    $valid = valid_cookie($co);
	    if($co){
            //存在
            $url = $this->config->item('ssoserver').'sso/ssoservice/getUser';
            $result = post_data($url,$co);
            if($result && $result->status == 'ok'){
                //add or update 用户资料到本地   
	            $user['id'] = (string)$result->employeeNumber;
	            $user['name'] = (string)$result->uid;
	            $ouserID = $this->user->createOrUpdateSSOUser($user);
	            //处理本地登录
	            if($ouserID > 0){
	                $token = $this->_loginDo(array('type'=>'sso','uid'=>$ouserID),false);
	                if($callback){
	                    redirect($callback);
	                }else{
	                    redirect(base_url());
	                }
	            }else{
	                //创建用户失败，重新来过？
	                die('登录失败请联系管理员');
	            }
            }else{
                redirect(base_url());
            }

        //导向callback
	    }else{
	        //找不到cookie？
	        redirect(base_url());
	    }

	}
	
	public function logout(){
	        //$token = get_cookie($this->config->item('sess_cookie_name'));
	        $token = $this->session->userdata($this->config->item('sess_cookie_name'));
	        $ajax = $this->input->post('ajax',true);
            $this->user->delToken($token);
            $redis = getRedis(CACHE_SESSION);
            if($redis){
                $redis->delete($token);
            }
            //去除ssocookie
            $co = get_cookie('ObSSOCookie',true);
            if($co){
                set_cookie('ObSSOCookie','',-1);
            }
            $this->session->set_userdata('SUPER_ADMIN', '');
            /*
             * 去除长期登录cookie
            $cookie_name = $this->config->item('sess_cookie_name');
            $cookie_domain = $this->config->item('cookie_domain');
            $cookie_prefix = $this->config->item('cookie_prefix');          
            $cookie_path = $this->config->item('cookie_path');          
            set_cookie($this->config->item('sess_cookie_name'),$token,'-1',$cookie_domain,$cookie_path,$cookie_prefix);
	        */
            $this->session->unset_userdata($token);
	        $callback = $this->input->get_post('callback',true);
            if($callback){
	            $callback = urldecode($callback);
	        }else{
	            $callback = base_url();
	        }
	        if($ajax){
                echo RST(urlencode($callback),'success',0);
                die(0);
	        }else{
                redirect($callback);
	        }
	}
	
	public function  changpwd(){
        $data = array();
        $data['user'] = checklogin();
        if(!$data['user'])
        {
            redirect(base_url('user/login'));
            exit;
        }
        $uid = $data['user']['id'];
        $data['userheadinfo'] = $this->load->module("user/userinfo/userheadinfo",array($data['user']['id']),true);
                
        $data['cur'] = 'changpwd';
        $data['num'] = $this->_getAllNums($data['user']['id']);
        $data['left_nav'] = $this->load->module("user/left_nav",array($data),true);
        
        $this->load->module("common/header",array('title'=>'修改密码'));
        $this->load->view("user/changpwd",$data);
        $this->load->module("common/footer");
	}
	
    public function forget(){
        $data = array();
        //$this->load->module("common/header");
        $this->load->view("user/forget",$data);
        //$this->load->module("common/footer");
        

    }
    public function forgetDo(){
        $data = array();
        
        $name = $this->input->post('loginName',true);
        $email = $this->input->post('loginEmail',true);
        
        //$user['mail'] = trim($this->input->post('user_no', true));
        //$user['tno'] = trim($this->input->post('user_oano', true));
        
        $res = $this->config_model->getModelsBykey('name',$email,'*','identity_credential');
        //查找这个用户是否注册，是否存在oa的email
        $oa = $this->config_model->getModelsBykeyarray(array('tno'=>$name,'mail'=>$email),'oa_user');
        if(!isset($res->name)){
            echo RST('',400001,'系统不存在该用户，您是否为注册？请注册用户后登录使用');//用户凭证表不存在记录
            exit;
        }
        if(isset($oa->mail) && !empty($oa->mail)){
        	$uid = $res->uid;
        	
            $mailConf = $this->config->item('mail');
            $this->load->library('Smtp');
            $pwd = random_string('alnum',6);
            $smtpusermail = "cxyfzx@js.chinamobile.com";//SMTP服务器的用户邮箱 
            $smtpemailto = $oa->mail;//发送给谁 
            $mailsubject = "创新梦工厂用户注册信息";//邮件主题 
            $mailbody = "尊敬的用户".
                "<br/>欢迎您访问创新梦工厂用户，".
                "您刚刚进行了找回密码操作，最新登录密码是：<strong>".$pwd."</strong>,".
                "您可以从以下链接登录创新梦工厂,开始您的创新之旅:<br/>".base_url('user/login');//邮件内容 
            $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件 
            
            $smtp = new Smtp($mailConf['smtp_host'],$mailConf['smtp_port'],true,$mailConf['smtp_user'],$mailConf['smtp_pass']);//这里面的一个true是表示使用身份验证,否则不使用身份验证. 
            $send = $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
            //$send = true;
            if($send){
                $this->_changePwd($uid, $pwd);
                echo RST(urlencode(base_url()),0,'新密码已经发送，请到邮箱查收');
            }else{
            	echo RST(urlencode(base_url()),400004,'邮件发送失败，密码未修改，请联系管理处理');
            }
        }else{
            echo RST(urlencode(base_url()),400003,'系统不存在该用户，邮箱错误？请联系管理员处理');//没有oa用户记录或者邮箱设置不正确
        }
        exit;
        //$this->load->module("common/footer");
        

    }
    
    public function changpwdDo(){
    	$op = $this->input->post('old-pwd',true);
        $np = $this->input->post('new-pwd',true);
        $id = $this->input->post('pwd-id',true);
        $data['user'] = checklogin();
        if(!$data['user'])
        {
            echo RST('',200001,'登录超时，请重新登录后操作');
            exit;
        }
        $uid = $data['user']['id'];
        if($uid == $id){
        	//获取旧密码，比较是否相等
        	$res = $this->config_model->getModelsBykey('uid',$uid,'*','identity_password');
        	if(!isset($res->pwd)) {
        		echo RST('',200003,'未注册用户，无法修改密码');
        	}else{
	        	if($res->pwd != sha1($op)){
	        		echo RST('',200003,'旧密码错误，无法修改!');
	        	}else{
		        	$this->_changePwd($uid, $np);
		        	echo RST('',0,'密码修改成功!');
	        	}
        	}
        }else{
            echo RST('',200002,'无权限!');
        }
        exit;
    }
    /**
     * 
     * 修改密码
     * @param unknown_type $uid 用户Id
     * @param unknown_type $pass 用户新密码
     */
    private function _changePwd($uid,$pass){
    	  return $this->config_model->updateModelBykey('uid',$uid,'identity_password',array('pwd'=>sha1($pass)));
    }
    /**
     * 获取当前登录用户的可用积分，最多可兑换金币数，以及积分兑换标准
     */
    public function ajaxGetScore(){
        $user = checklogin();
        if(!$user)
        {
            echo RST('',200001,'登录超时，请重新登录后操作');
            exit;
        }
        $k = $this->config_model->getModelsBykey('key',"SCORE_TO_COIN",'*','site_config');
        $res['std'] = $k->value;
        $result = $this->config_model->getModelsBykey('uid',$user['id'],'*','identity_user_score');
        if($result){
	    	$res['score'] = $result->score;
	    	$res['maxCoin'] = floor($result->score / $res['std']);
        }else{
            $res['score'] = 0;
            $res['maxCoin'] = 0;
        }
    	
    	$res['html']="<div style=\"font-size:16px;line-height:35px;text-align:center\"><div style=\"font-size:18px;\">请在输入框输入将要兑换的金币数量</div>
    	<div>
	    	<span style=\"font-size:16px;\">目前可用积分</span>
	    	<span style=\"font-size:16px;font-weight:bold;\">{$res['score']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	    	<span style=\"font-size:16px;\">兑换金币</span>";
	    if($result->score >= $res['std']){
	        $res['html']="<div style=\"font-size:16px;line-height:35px;text-align:center\"><div style=\"font-size:18px;\">请在输入框输入将要兑换的金币数量</div>
	        <div>
	            <span style=\"font-size:16px;\">目前可用积分</span>
	            <span style=\"font-size:16px;font-weight:bold;\">{$res['score']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            <span style=\"font-size:16px;\">兑换金币</span>";
	    	$res['html'].= "<span style=\"font-size:16px;\"><input type\"text\" id=\"coin\" value=\"{$res['maxCoin']}\" style=\"width:80px\"/></span>";
    	    $res['sure'] = false;
	    }else{
	        $res['html']="<div style=\"font-size:16px;line-height:35px;text-align:center\"><div style=\"font-size:18px;color:red\">您的积分过少，无法兑换金币</div>
	        <div>
	            <span style=\"font-size:16px;\">目前可用积分</span>
	            <span style=\"font-size:16px;font-weight:bold;\">{$res['score']}</span>";
    	    $res['sure'] = true;
    	}
	    $res['html'].= "</div>
    	<div style=\"font-size:16px;\">提示:积分金币兑换比例为[1:{$res['std']}],金币可用于商城商品购买</div></div>";
    	
	    
    	echo RST($res,0,'success');
    	exit(0);
    }
    /**
     * 积分兑换
     */
    public function ajaxChargeVal(){
        $user = checklogin();
        if(!$user)
        {
            echo RST('',200001,'登录超时，请重新登录后操作');
            exit;
        }
        $s = intval($this->input->post('coin',true));
        if($s > 0){
	        $k = $this->config_model->getModelsBykey('key',"SCORE_TO_COIN",'*','site_config');
	        $res['std'] = $k->value;
	        $result = $this->config_model->getModelsBykey('uid',$user['id'],'*','identity_user_score');
	        $r = $result->score - ($s * $res['std']);
	        if($r < 0){
	            echo RST('',1,"您的积分不足({$result->score})，不足够兑换你输入金币数");
	        }else{
	        	$coin = $result->coin + $s;
	        	$this->config_model->updateModelBykey('uid',$user['id'],"identity_user_score",array('score'=>$r,'coin'=>$coin));
	        	$this->load->model('common_credit_rule_model', 'ccr');
	        	$this->load->model('common_credit_rule_log_model', 'ccrl');
	        	$rule = $this->ccr->getRule('charge-score');
                $actId = $rule['rid']; //规则Id
	        	$this->ccrl->setUserActLog($user['id'], $actId,0,'积分兑换，减少积分'.$s * $res['std'].'积分增加'.$s);
	            $to['coin'] = $coin;
	            $to['score'] = $r;
	        	echo RST($to,0,'积分兑换成功,你剩余积分'.$r);
	        }
        }else{
        	echo RST('',1,'参数不正确，请确认您输入了正确的金币数');
        }
        exit(0);
    }
    
    /**
     * 分页获取部门积分
     */
    public function getDepartScore(){
        //分页
        $per_page = 4;
        if(isset($_GET['p'])){
            $page = intval($this->input->get('p',true));
        }else{
            $page = 1;
        }
        /*
        //排序
        if(isset($_GET['o'])){
            $a['score'] = 'ASC';
            $data['order'] = '<a href="'.base_url('user/getDepartScore').'">积分↑</a>';
        }else{
            $a['score'] = 'DESC';
            $data['order'] = '<a href="'.base_url('user/getDepartScore/?o=1').'">积分↓</a>';
        }
        $offset = ($page - 1)*$per_page;    
        $data['res'] = $this->user->getLists('identity_group_score',array(), $a,$offset,$per_page,'more');
        //生成分页
        $data['curpage'] =  $page;
      
        //$num = $this->config_model->countAll('identity_group_score');
        $data['page'] = '';
        $data['totalpage'] = 1;
        
        if($num > $per_page){
            $data['totalpage'] = floor($num /$per_page);
            if(($num % $per_page) >0){
                $data['totalpage'] = $data['totalpage'] + 1;
            }
                    
            if($data['curpage'] > 1){
                $data['page'] .= '<a href="'.base_url('user/getDepartScore/?p=').($data['curpage']-1).(isset($_GET['o'])?'&o=1':'').'" class="pre"><前一页</a>';
            }
            for($i=1;$i<= $data['totalpage']; $i++){
                if($i !=$data['curpage']){
                    $data['page'] .= '<a href="'.base_url('user/getDepartScore/?p=').($i).(isset($_GET['o'])?'&o=1':'').'" >'.$i.'</a>';
                }else{
                    $data['page'] .= '<a href="javascript:void(0);" class="current" >'.$i.'</a>';
                }
            }
            if($data['curpage'] < $data['totalpage']){
                $data['page'] .= '<a href="'.base_url('user/getDepartScore/?p=').($data['curpage']+1).(isset($_GET['o'])?'&o=1':'').'" class="next">下一页></a>';
            }
        }else{
            $data['totalpage'] = 1;
        }
        */
        $data['page'] = '';
        if(isset($_GET['o'])){
            $a['score'] = 'ASC';
            $data['order'] = '<a href="'.base_url('user/getDepartScore').'">积分↑</a>';
            $flag = false;
        }else{
            $a['score'] = 'DESC';
            $data['order'] = '<a href="'.base_url('user/getDepartScore/?o=1').'">积分↓</a>';
            $flag = true;
        }
        $data['res'] = $this->user->getDepartScores($flag);
        //$this->load->module("common/bg_header",array('title'=>'江苏移动本部及分公司积分统计表'));
        $this->load->view("user/departscore",$data);
        $this->load->module("common/bg_footer");
    }
	/**
	 * sso测试函数
	 */
	public function set(){
	    $cookie_domain = $this->config->item('cookie_domain');
	    set_cookie('ObSSOCookie','nPD9FuxcuL16a9Q9LnU9IhxL/hQpvIUmOw/CTHbg9f6qVaos56OmCVSb8GejN06bcP02xnCxJLeee2kK/BCSQJ3HI2y4S/LWz5JV9kKgtbHJ/vrNcAbeBZGnGTODm9w2QlOsTKHeumj+HM6u99My9dqCcM3e/a8ISNHV/de+1YT5Tv4BvDNTn3k3yxTRYAE0NCMDi5qTIx24t39YoGZfAktbEF+sQ8zxnlfAigJKxAoUcrI70qGE9mHlddfthJH9aDCbsKnDQreszs+k3ydIzisyq8LJsIplNthJN9JNYC/2GvLlOpkgtlN3LxIZM1Eq+kA6Mq+HeslcAtummo4abcpGDzIS+WleotKBtJPDTMQ=',0,$cookie_domain);
	}
	public function clear(){
	    $cookie_domain = $this->config->item('cookie_domain');
	    set_cookie('ObSSOCookie','',-1, $cookie_domain);
	}
	
	public function get(){
	    $user = checklogin();
	    var_dump($user);
	}
	
	
	public function valid(){
        $co = get_cookie('ObSSOCookie',true);
        if(!$co){
        	echo 0;
        	exit();
        }
        $valid = valid_cookie($co);
        if($valid){
        	echo 1;
        }else{
        	echo 0;
        }
        exit();
	}
	
	public function infomation($id){
		$uid = intval($id);
		if($uid > 0){
			$data['user'] = $this->load->module("user/userinfo/userselfinfo",array($uid),true);
		}else{
			$data['user'] = false;
		}
		$this->load->module("common/header");
		$this->load->view("user/selfinfo",$data);
		$this->load->module("common/footer");
	}
	/**
	 * 连续在线奖励积分
	 */
	public function logward(){
	    $user = checklogin();
        if($user)
        {
            getScore($user['id'], "one-hour");
        }
        //获取当前登录者IP，记录到redis
        $ip = md5($this->input->ip_address());
        $time = time ();
        //获取redis记录
        $key = "online-user";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $serUser = $this->cache->redis->get($key);
        if(!$serUser){
        	$tempUser = array();
        }else{
        	$tempUser = unserialize($serUser);
        }
        	
        $tempUser[$ip]=$time;	
        foreach ($tempUser as $k=>$v){
        	// 如果三分钟后再未访问页面，刚视为过期
        	if (time() - $v > 180) {
        		unset($tempUser[$k]);
        	}
        }
        $this->cache->redis->save($key,serialize($tempUser));
        echo RST('',0,'success');
        exit;
	}
    /**
     * 首页领取金币
     */
	public function getMyCoin(){
		exit(0);
		$user = checklogin();
		$data['status'] = 0;
		if(empty($user)){
            $data['message'] = '亲，请先登录';
		}else{
		  //检查是否已经领取 user_getcoin_log
		    //首页领取金币数
            $coin = $this->config_model->getModelsBykey("key","USR_GETCOIN",'*',"site_config");
 
		    $this->load->model("user_getcoin_log_model","uglm");
            $result = $this->uglm->check($user['id']);
            if($result){
            	//随机获取一个项目
            	$project = $this->pbasicinf->getOneRand();
            	//分配金币
            	$this->uglm->addCoin($user['id']);
	            $data['status'] = 1;
	            
	            $data['message'] = '
	            <div style="margin-bottom:20px">
	            <div style="border-bottom:1px solid #ccc;padding-bottom:10px;">
	                <p style="font-weight:bold;font-size: 16px;">恭喜领到'.$coin->value.'个金币</p>
	                <p style="">您还可以通过积分兑换金币，想要获取更多积分和金币就加入到创新行列吧！</p>
	            </div>
	            <div style="float:left;text-align:left;margin-top:10px;">
	                <div style="float:left;width:240px;margin-right:10px;">
	                    <p style="font-size: 16px;">发起一个项目</p>
	                    <div class="vote_click" style="margin:10px 0px 10px 0px;padding-right:10px"><a href="'.base_url("project/create/1").'" style="font-size:16px;color:white;font-weight:bold;">发起项目</a></div>
	                    <div>不管你有什么样的创意和想法，都可以勇敢的发出来，我们一起完善，一起成长</div>
	                </div>
	                <div style="float:left;width:200px;border-left:1px solid #ccc;padding-left:10px">
	                   <p style="font-size: 16px;margin-bottom:10px">看别人的项目</p>
	                   <div>
	                   <a href="'.base_url("project/detail/".$project["pid"]).'">
	                   <img width="200" height="157" src="'.base_url("assets/upload/img/small/".$project["logo"]).'">
	                   </a>
	                   <p style="margin:5px 0px;width:200px;height:20px;overflow:hidden;">
	                   <a href="'.base_url("project/detail/".$project["pid"]).'" style="font-size: 16px;color:#000">'.$project['name'].'</a></p>
	                   <div style="color:#ccc;width:200px;height:40px;overflow:hidden;">'.$project['content'].'</div>
	                   </div>
	                </div>
	            </div>
	            </div>
	            ';
	            
            }else{
	           $data['message'] = "您今天已经领取过金币，明天请早";
            }
		}
        echo json_encode($data);
        exit;
	}
	//TODO首页统计改为ajax异步请求
	function getIndexAccount(){
		$result = array();
		//统计项目数
		$where = "`status` in (0,1,2)";
		$project = $this->config_model->countBykeyarray($where,"project_base_info");
		$result['project_num'] = intval($project->num);
		//统计总访问数
		$result['visit_num'] = intval($this->config_model->countAll("visit_log"));

		//统计在线数
		//读取redis记录
        $key = "online-user";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $serUser = $this->cache->redis->get($key);
        if(!$serUser){
            $result['online_num'] = 1;
        }else{
            $result['online_num'] = count(unserialize($serUser));
        }
        
	}
	
   /**
     * ajax判断是不是管理员,能不能后台登陆
     */
   public function ajax_CheckUserLogin()
   {

        $status=1;//没有用户名
        $msg = "请先输入正确用户名和密码";
        $name = trim($this->input->post('name',true));

        $password = trim($this->input->post('pass_word',true));
        if($name){
            $row = $this->config_model->getModelByIns(array('auth'=>'2,999'),array('nickname'=>$name),'identity_user');
            if($row){
                $uid = $this->user->check($name,$password);
                if($uid){
                 //写session
                    $this->session->set_userdata('SUPER_ADMIN', $name);
                    $status = 0;
                    $msg = "success";
                }else{
                    $status = 3;
                    $msg = "密码错误，请修改";
                }
            }else{
                $status = 2;//无权限
                $msg = "对不起，您无权登录后台";
            }
        }
        echo RST('',$status,$msg);
    }
    
    /**
     * 统计个人积分排名
     */
    private function userScoreList(){
    	$n = 14;
    	$res = $this->user->getUserScoreList($n);
    	$r = array();
    	foreach($res as $v){
    		/*
    		$len = mb_strlen($v['depart']);
    		if($len > 4){
    			$dep = '['.mb_substr($v['depart'],0,$len-2).']';
    		}else{
    			$dep = '['.$v['depart'].']';
    		}
    		if(empty($v['realname'])){
    			$r[$v['name'].$dep] = $v['score'];
    		}else{
    			$r[$v['realname'].$dep] = $v['score'];
    		}*/
    		$dep = '['.$v['depart'].']';
    	    if(empty($v['realname'])){
                $r[$v['name'].$dep] = $v['score'];
            }else{
                $r[$v['realname'].$dep] = $v['score'];
            }
    	}
    	return $r;
    }
    /**
     * 统计地市项目数排名
     */
    function areaProjectList(){
    	
        $area = array("省本部","无锡分公司","泰州分公司","徐州分公司","南通分公司","南京分公司","连云港分公司","淮安分公司","苏州分公司","扬州分公司","常州分公司","盐城分公司","宿迁分公司","镇江分公司");
        $r = array();
        foreach ($area as $v){
	        $r[$v] = $this->user->getAreaProjectNum($v);
        }
        arsort($r);
        return $r;
    }
    /**
     * 首页分公司排名及个人积分排名
     * Enter description here ...
     */
    public function lists(){
    	$key = "paiming-lists";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $serUser = $this->cache->redis->get($key);
        if(!$serUser){
            $r['a'] = $this->areaProjectList();
            $r['u'] = $this->userScoreList();
            $this->cache->redis->save($key,serialize($r),43200);//缓存12小时
        }else{
            $r= unserialize($serUser);
        }
        /*

    <ul class="box-content">
        <li class="top_1"><a href="http://creative.js.cmcc/project/detail/201" target="_blank" title="NFC—智能生活“刷出来”">NFC—智能生活...</a><span>185</span></li><li class="top_2"><a href="http://creative.js.cmcc/project/detail/198" target="_blank" title="话费余额宝(支付宝)">话费余额宝(支付...</a><span>158</span></li><li class="top_3"><a href="http://creative.js.cmcc/project/detail/311" target="_blank" title="随身导游">随身导游</a><span>147</span></li><li class="top_4"><a href="http://creative.js.cmcc/project/detail/197" target="_blank" title="一站式自助医疗服务(预约、缴费、报告）">一站式自助医疗服...</a><span>113</span></li><li class="top_5"><a href="http://creative.js.cmcc/project/detail/283" target="_blank" title="校信通手机APP应用">校信通手机APP...</a><span>97</span></li>    
    </ul>
</div>
         */
        //组合html
        $tmpA = '<div class="box" id="as-list"><ul class="box-content">';
        $tmpB = '<div class="box" id="us-list"><ul class="box-content">';
        $i = 0;
        $j = 0;
        foreach($r['a'] as $k=>$v){
        	$i++;
        	$tmpA .='<li class="top_'.$i.'"><a href="'.base_url("project/getAreaProject/?a=".str_replace("+","-", base64_encode($k))).'"  target="_blank" style="">'.$k.'</a> <i>'.$v.'</i></li>';
        }
        foreach($r['u'] as $k=>$v){
            $j++;
            $tmpB .='<li class="top_'.$j.'"><a href="###" style="cursor:default;">'.$k.'</a> <i>'.$v.'</i></li>';
        }
        $tmpA .= '</ul></div>';
        $tmpB .= '</ul></div>';
        $res['a'] =$tmpA;
        $res['u'] =$tmpB;
        echo json_encode($res);
        exit;
    }
    /**
     * 删除特定key的缓存
     * Enter description here ...
     * @param unknown_type $key
     */
    public function delc($key){
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $res = $this->cache->redis->delete($key);
        var_dump($res);
    }
    
    public function randx(){
    	$randval = rand(0,100); 
    	echo $randval;
    }
}
