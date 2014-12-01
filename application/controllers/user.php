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
		    
		    $res = set_cookie($token_name,$token,$exptime,$cookie_domain,$cookie_path,$cookie_prefix);
		    //记录登录日志
		    $this->config_model->insert_model("user_logs",array('uid'=>$uid,'ctime'=>mktime(),'ctype'=>1));
		}
		return $token;
	}

	public function ssologin(){
		/*
	    //记录是否有callback，有则写入cookie
	    $callback = $this->input->get_post('callback',true);
	    if($callback){
	        $domain = $this->config->item('cookie_domain');
	        set_cookie('lcback',base64_encode($callback),0,$domain);
	    }
        //跳转到登录页面 ，带上callback? 
        $ssologinUrl = $this->config->item('ssologin');
        redirect($ssologinUrl); 
        */
		
	    $base65_aes_str = $this->input->get('auth', true);
	    if(empty($base65_aes_str))
	    {
	        show_error('参数错误');
	    }
	    
	    //更具密文获取用户信息
	    $this->load->library('AES');
	    $aes_str = base64_decode($base65_aes_str);
	    //$aes_str = pack("H*", $aes_str);平台去除hex加密解密
	    $decode_aes_str = $this->aes->decrypt($aes_str);
	    $user_info = explode(',',rtrim($decode_aes_str,"\6"));
	    if(is_array($user_info))
	    {
	       $uname = isset($user_info[0]) ? $user_info[0] : '';
	       $umobile = isset($user_info[1]) ? $user_info[1] : '';
	       $uemail = isset($user_info[2]) ? $user_info[2] : '';
	       if(! $uname)
	       {
	           show_error('无法获取用户信息');
	       }
	       
	       //判断用户是否存在
	       $checkName = $this->user->checkUsername($uname);
	       if($checkName !== FALSE)
	       {
	           $user = array(
	               'uid' => $checkName->id,
	               'type' => 'sso',
	           );
	           $this->_loginDo($user, FALSE);
	           redirect();
	       }
	       else 
	       {
	       		//注册新用户
	       		$user = array(
	       			'username' => $uname,
	       			'nickname' => $uname,
	       			'realname' => $uname,
	       			'phone'=>$umobile,
	       			'email' => $uemail,
	       			'auth' => 1,
	       		);
	       		$uid = $this->user->sso_register($user);
	       		if($uid)
	       		{
	       			$user = array(
	       					'uid' => $uid,
	       					'type' => 'sso',
	       			);
	       			$this->_loginDo($user, FALSE);
	       			redirect();
	       		}
	       		else 
	       		{
	       			show_error('登录失败');
	       		}
	       }
	       
	    }
	    else
	    {
	        show_error('无法获取用户信息');
	    }
	    
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
	        $token_name = $this->config->item('user_login_cookie');
	        $token = get_cookie($token_name);
	        //$token = $this->session->userdata($this->config->item('sess_cookie_name'));
	        $ajax = $this->input->post('ajax',true);
            $this->user->delToken($token);
            $redis = getRedis(CACHE_SESSION);
            if($redis){
                $redis->delete($token);
            }
            //去除ssocookie
            /*
            $co = get_cookie('ObSSOCookie',true);
            if($co){
                set_cookie('ObSSOCookie','',-1);
            }
            */
            //$this->session->set_userdata('SUPER_ADMIN', '');
            /*
             * 去除长期登录cookie
            $cookie_name = $this->config->item('sess_cookie_name');
            $cookie_domain = $this->config->item('cookie_domain');
            $cookie_prefix = $this->config->item('cookie_prefix');          
            $cookie_path = $this->config->item('cookie_path');          
            set_cookie($this->config->item('sess_cookie_name'),$token,'-1',$cookie_domain,$cookie_path,$cookie_prefix);
	        
            $this->session->unset_userdata($token);
            */
            $cookie_prefix = $this->config->item('cookie_prefix');
            $cookie_domain = $this->config->item('cookie_domain');
            $cookie_path = $this->config->item('cookie_path');          
            $res = set_cookie($token_name,$token,-1,$cookie_domain,$cookie_path,$cookie_prefix);
             
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
	/**
	 * 头部获取用户登录情况
	 */
	public function information(){
		$user = checklogin();
		$res = array();
		if($user){
			$res['msg'] =  '<div id="navbar-signin" class="nav-link pull-left">
					<a href="/material/my_material">'.$user['nickname'].'</a>
				</div>
			    <div id="navbar-signin" class="nav-link pull-left">
					<a href="/material/material_add">上传素材</a>
				</div>
				<div id="navbar-name" class="nav-link pull-left">
					<a href="/user/logout">注销</a>
				</div>
				';
			$res['code'] = 1;
		}else{
			$res['msg'] =  '<div id="navbar-name" class="nav-link pull-left">
					<a href="/user/login">登录</a>
				</div>';
			$res['code'] = 1;
		}
		echo json_encode($res);
		exit(0);
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
