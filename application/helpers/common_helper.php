<?php
//通用方法
if(!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 封装ajax请求结果
 * @param $data string or array 需要返回给客户端的数据
 * @param $msg string 返回的消息
 * @param $errno int 错误号，成功返回0
 *
 * @return $tmp array
 */
function RST($data,$errno=0,$msg=''){
	$CI = & get_instance();
	$tmp = array();
	$tmp['data'] = $data;
	if($msg !=''){
		$tmp['msg'] = $msg;
	}else{
		$err = $CI->config->item('errno');
		$tmp['msg'] = $err[$errno];
	}
	$tmp['errno'] = $errno;
	return json_encode($tmp);
}
/*
 * 生成随机字符串
 *
 * @param $length int 生成的位数
 * @param $mode int 生成模式，1表示全数字2全小写字母3全大写字母4大小写混合
 */
function getRandCode($length = 10, $mode = 0)
{
	switch ($mode) {
		case '1':
			$str = '1234567890';
			break;
		case '2':
			$str = 'abcdefghijklmnopqrstuvwxyz';
			break;
		case '3':
			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case '4':
			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			break;
		case '5':
			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
			break;
		case '6':
			$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
			break;
		default:
			$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
			break;
	}

	$result = '';
	$max = strlen($str) - 1;
	//mt_srand((double)microtime() * 1000000);//php已经自动完成，不需要使用mt_srand
	for($i = 0;$i < $length;$i ++){
		$result .= $str[rand(0, $max)];
	}
	return $result;
}
/**
 * 格式化数据输出
 * 
 */
function fnum($num){
    if($num > 999) $num = number_format($num, 0);
    if($num > 9999) $num = '9,999+';
    return $num;
}

/*
 *功能：对字符串进行加密处理
 *参数一：需要加密的内容
 *参数二：密钥
 */
function passport_encrypt($str,$key=1)
{
	srand((double)microtime() * 1000000);
	$encrypt_key=md5(rand(0, 32000));
	$ctr=0;
	$tmp='';
	for($i=0;$i<strlen($str);$i++)
	{
		$ctr=$ctr==strlen($encrypt_key)?0:$ctr;
		$tmp.=$encrypt_key[$ctr].($str[$i] ^ $encrypt_key[$ctr++]);
	}
	return base64_encode(passport_key($tmp,$key));
}



/*
 *功能：对字符串进行解密处理
 *参数一：需要解密的密文
 *参数二：密钥
 */

function passport_decrypt($str,$key=1)
{
	$str=passport_key(base64_decode($str),$key);
	$tmp='';
	for($i=0;$i<strlen($str);$i++)
	{
		$md5=$str[$i];
		$tmp.=$str[++$i] ^ $md5;
	}
	return $tmp;
}

/*
 *辅助函数
 */
function passport_key($str,$encrypt_key)
{
	$encrypt_key=md5($encrypt_key);
	$ctr=0;
	$tmp='';
	for($i=0;$i<strlen($str);$i++)
	{
		$ctr=$ctr==strlen($encrypt_key)?0:$ctr;
		$tmp.=$str[$i] ^ $encrypt_key[$ctr++];
	}
	return $tmp;
}

function strlen_utf8($str) {
	$i = 0;
	$count = 0;
	$len = strlen ($str);
	while ($i < $len) {
		$chr = ord ($str[$i]);
		$count++;
		$i++;
		if($i >= $len) break;
		if($chr & 0x80) {
			$chr <<= 1;
			while ($chr & 0x80) {
				$i++;
				$chr <<= 1;
			}
		}
	}
	return $count;
}

//判断用户是否登录(登录情况下，返回用户对象，反之false)
function checklogin()
{
	$CI = & get_instance();
	$CI->load->model('user_model');
	$token_name = $CI->config->item('user_login_cookie');
    //$token = $CI->session->userdata($CI->config->item('user_login_cookie'));
    $token = get_cookie($CI->config->item('user_login_cookie'));
    $res = $CI->user_model->valideToken($token,true);
    //从session中获取当前登录用户信息
    if($res != 0){
    	$user = $res;
    }else{
    	$user = false;
    }
    return $user;
}


//加过滤的判断用户是否登录
function checkfilterlogin()
{
	$checlogin = checklogin();
	if($checlogin)
	{
		return $checlogin;
	}
	else
	{
		return checkActionNeedLogin();
	}
}

//判断操作是否不需要登录状态
function checkActionNeedLogin(){
	$CI = & get_instance();
	$ignorearr = $CI->config->item('ignore_arr');
	if(ignoreAction($CI->router->fetch_class(),$CI->router->fetch_method(),$ignorearr))
	{
		return true;//忽略登录
	}else{
		return false;//不忽略
	}
}
//
//检查当前action是否在排除过滤数组$ignorearr中
//$ignorearr=array('className/actionName','*/actionName','className/*',...)
//
function ignoreAction($curCls, $curAction,$ignorearr){
	if(0 == count($ignorearr))return true;
	foreach($ignorearr as $val){
		$exp = explode('/', $val);
		$strA = (('*' == $exp[0]) && ($curAction == $exp[1]));
		$strB = (($curCls == $exp[0]) && ('*' == $exp[1]));
		$strC = (($curCls == $exp[0]) && ($curAction == $exp[1]));
		if($strA || $strB || $strC)return true;
	}
	return false;
}

/*
 * 生成分页函数
 */
/**
 * 生成分页函数
 * @param $total : 总个数
 * @param $limit : 显示个数
 * @param $page :  第几页
 * @param $url :  地址
 */
function  _create_pages($total = 0,$limit = 0,$page=0,$url='')
{
	
	if(!$page){$page = 1;}
	$total_pages = ceil($total/$limit);
	//$start = '<div class="promanage_pages backfffff">';
	//$end = '</div>';
    if(!$total){//没有评论
       //return $start.'<div class="zanwupinglun">暂无评论</div>'.$end;
       return -1;
    }else if($total_pages < 2){//不足分页
    	//return '<div style="border:0px;border-top: 1px solid #E0E0E0;height: 0px;"></div>';
    	return 1;
    }
         
    
    $url1 = '</a>';  
    if($page == 1){
    	if($url){
           $up = '<a href="'.$url.'?page='.$page.'">';
    	}else{
    	    $up = '<a href="javascript:;" data-page="'.$page.'">';
    	}
    }else{
    	if($url){
           $up = '<a href="'.$url.'?page='.($page -1).'">';
    	}else{
    	   $up = '<a href="javascript:;" data-page="'.($page -1).'">';
    	}
    }
    if($page == $total_pages){
    	if($url){
    	   $down = '<a href="'.$url.'?page='.$page.'">';
    	}else{
    		$down = '<a href="javascript:;" data-page="'.$page.'">';
    	}
    }else{
    	if($url){
            $down = '<a href="'.$url.'?page='.($page + 1).'">';
    	}else{
    	    $down = '<a href="javascript:;" data-page="'.($page + 1).'">';
    	}
    }
    $str = $up.'<span class="page mr4"><img src="/assets/img/new/page_left.png" class="mr2"></img>前页</img></span>'.$url1;
	for($i=1;$i<=$total_pages;$i++)
	{
		$pu = '?page='.$i;
		if($url){
		   $url0 = '<a href="'.$url.$pu.'">';
		}else{
		   $url0 = '<a href="javascript:;" data-page="'.$i.'">';
		}
		if($page == $i){
			$str .= $url0.'<span class="page_check">'.$i.'</span>'.$url1;
		}else{
		  $str .= $url0.'<span>'.$i.'</span>'.$url1;
		}
	}
	$str .=$down.'<span class="page">后页<img src="/assets/img/new/page_right.png"></span>'.$url1;
	//return $start.$str.$end;
	return $str;
}
/*function  _create_pages($total_rows = 0,$per_page = 0,$url){
	$CI = & get_instance();
	$get = ($CI->input->get() === FALSE) ? array() : $CI->input->get();
	// 当前页开始条数
	$limit = (isset($get['per_page'])) ? (intval($get['per_page'])+1) : 1;
	// 当前页码
	$cur_page_num = ceil($limit/$per_page);
	// 总页数
	$total_pages = ceil($total_rows/$per_page);
	// 尾页显示的开始条数
	$end_pages = ($total_pages-1) * $per_page;
	unset($get['per_page']);
	$max_numlinks = 2;
	$config['base_url'] = base_url($url);
	$config['total_rows'] = $total_rows;
	$config['page_query_string'] = TRUE;
	$config['per_page'] = $per_page;
	$config['num_links'] = $max_numlinks;

	$config['full_tag_open'] = '<div class="pagination"><ul>';
	$config['full_tag_close'] = '</ul></div>';
	// 判断当前页为首页,上一页不可点
	if ($cur_page_num==1)  {
		$config['cur_tag_open'] = '<li class="item">◀</li><li class="item current">';
		$config['cur_tag_close'] = '</li>';
		// 判断当前页为尾页,下一页不可点
	} elseif ($cur_page_num==$total_pages) {
		$config['cur_tag_open'] = '<li class="item current">';
		$config['cur_tag_close'] = '</li><li class="item">▶</li>';
	} else {
		$config['cur_tag_open'] = '<li class="item current">';
		$config['cur_tag_close'] = '</li>';
	}

	$config['num_tag_open'] = '<li class="item">';
	$config['num_tag_close'] = '</li>';
	// 上一页
	$config['prev_link'] = '◀';
	$config['prev_tag_open'] = '<li class="item pre">';
	$config['prev_tag_close'] = '</li>';
	// 首页
	$config['first_link'] = 1;
	$config['first_tag_open'] = '<li class="item">';
	$config['first_tag_close'] = '</li>';
	// 下一页
	$config['last_link'] = $total_pages;
	$config['last_tag_open'] = '<li class="item">';
	$config['last_tag_close'] = '</li>';
	// 尾页
	$config['next_link'] = '▶';
	$config['next_tag_open'] = '<li class="item next">';
	$config['next_tag_close'] = '</li>';
	// 偏移首页数量大于3的，首页+ …
	if (($cur_page_num - 1) > 3) {
		$config['first_link'] = 1;
		$config['first_tag_open'] = '<li class="item">';
		$config['first_tag_close'] = '</li><li class="item">...</li>';
	}
	// 偏移首页数量大于2的，首页的链接写死
	if (($cur_page_num - 1) > 2) {
		$config['prev_link'] = '◀';
		$config['prev_tag_open'] = '<li class="item pre">';
		$config['prev_tag_close'] = '</li><li class="item"><a href="'.$config['base_url'].'&per_page=">'.$config['first_link'].'</a></li><li class="item">...</li>';
		$config['first_link'] = '';
		$config['first_tag_open'] = '';
		$config['first_tag_close'] = '';
	}
	// 偏移尾页数量大于3的， …+尾页
	if (($total_pages - $cur_page_num) > 3) {
		$config['last_link'] = $total_pages;
		$config['last_tag_open'] = '<li class="item">...</li><li class="item">';
		$config['last_tag_close'] = '</li>';
	}
	// 偏移尾页数量大于2的，尾页的链接写死
	if (($total_pages - $cur_page_num) > 2) {
		$config['next_link'] = '▶';
		$config['next_tag_open'] = '<li class="item">...</li><li class="item"><a href='.$config['base_url'].'&per_page='.$end_pages.'>'.$config['last_link'].'</a></li><li class="item next">';
		$config['next_tag_close'] = '</li>';
		$config['last_link'] = '';
		$config['last_tag_open'] = '';
		$config['last_tag_close'] = '';
	}
	$CI->load->library('pagination');
	$CI->pagination->initialize($config);
	return $CI->pagination->create_links();
}*/


/*
 *  中文截取，支持gb2312,gbk,utf-8,big5
 *
 * @param string $str 要截取的字串
 * @param int $start 截取起始位置
 * @param int $length 截取长度
 * @param string $charset utf-8|gb2312|gbk|big5 编码
 * @param $suffix 是否加尾缀
 */
function csubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	if(function_exists("mb_substr"))
	{
		$a = mb_substr($str, $start, $length, $charset);
		if($suffix && strlen($str)>$length)
		{
			return $a."…";
		}
	}
	$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	if($suffix && strlen($str)>$length) return $slice."…";
	return $slice;
}

function mysubstr($str, $start=0, $length, $charset="utf-8")
{
	$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all($re[$charset], $str, $match);
	$slice = join("",array_slice($match[0], $start, $length));
	return $slice;
}

/*
 * 字符串截取，英文算半个字符
 *
 */
function cut_str($sourcestr,$cutlength,$flag=true)
{
	$returnstr='';
	$i=0;
	$n=0;
	$str_length=strlen($sourcestr);//字符串的字节数
	while (($n<$cutlength) and ($i<=$str_length))
	{
		$temp_str=substr($sourcestr,$i,1);
		$ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
		if ($ascnum>=224)    //如果ASCII位高与224，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
			$i=$i+3;            //实际Byte计为3
			$n++;            //字串长度计1
		}
		elseif ($ascnum>=192) //如果ASCII位高与192，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
			$i=$i+2;            //实际Byte计为2
			$n++;            //字串长度计1
		}
		elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1;            //实际的Byte数仍计1个
			$n++;            //但考虑整体美观，大写字母计成一个高位字符
		}
		else                //其他情况下，包括小写字母和半角标点符号，
		{
			$returnstr=$returnstr.substr($sourcestr,$i,1);
			$i=$i+1;            //实际的Byte数计1个
			$n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
		}
	}
	if (($str_length>$i) && true == $flag){
		$returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
	}
	return $returnstr;
}



/**
 * 缓存服务
 */
function cache_service_get($key,$region = 'RYM'){
	if(!$region || !$key){
		return false;
	}
	$CI = & get_instance();
	$CI->load->model('Cache_model');
	$exists = $CI->Cache_model->exists($region,$key);
	if($exists->code == 0 || !$exists->result){
		return false;
	}else{
		$cache = $CI->Cache_model->get($region,$key);
		if($cache->code == 0){
			return false;
		}else{
			return my_unserialize($cache->result);
		}
	}
}
function cache_service_put($key,$value,$ttl = 3600,$tti = -1,$region = 'RYM'){
	if(!$region || !$value || !$key){
		return false;
	}
	$CI = & get_instance();
	$CI->load->model('Cache_model');
	$cache = $CI->Cache_model->putEx($region,$key,my_serialize($value),$ttl,$tti);
	if($cache->code == 0){
		return false;
	}else{
		return true;
	}
}

/**
 * 临时cache：5分钟有效
 * @param unknown $key
 * @param unknown $value
 */
function transient_cache_put($key, $value){

	cache_service_put($key, $value, 300);

}



function transient_cache_get($key){
	//return null;

	return cache_service_get($key);

}

function my_serialize($obj){
	return serialize($obj);
}
function my_unserialize($txt){
	return unserialize($txt);
}


/**
 * 获取用户掩码
 * 凡是涉及到用户账号显示的地方：
 * 手机号码：5-8位用*代替；如1381****903
 * 邮件：
 * @前面的字符中间的最中间的四个字符，用****代替。
 * 字符长度等于6：后面三个用***代替
 * 字符长度等于5：后面三个用***代替
 * 字符长度等于4：后面两个用**代替
 * 字符长度等于3：后面两个用**
 * 字符长度等于2：后面一个用*代替
 * 字符长度等于1：直接用*代替
 * 如：b**@any123.com；a*@163.com;hui****en@any123.com
 */
function get_user_mask($str, $login_type = false)
{
	if(strlen($str) == 0)
	return '';
	// 1 + 10位数字
	if(preg_match("/1[0-9]{10}/", $str))
	{
		//手机
		return substr_replace($str, '****', 4, 4);
	}
	else if(strpos($str,'@') !== false && $login_type === false)
	{
		//邮件
		$tmp = explode('@',$str);
		$str_h = $tmp[0];
		$str_f = '@'.$tmp[1];
		return get_mask($str_h).$str_f;
	}
	else if($login_type)
	{
		//到这里，应该是auth登录的
		$login_type = str_replace($login_type,'@');
		return 	$login_type.':'.get_mask($str);
	}
}

function get_mask($str_h)
{
	switch(mb_strlen($str_h,"UTF-8"))
	{
		case 1:
			$tmp = '*';
			break;
		case 2:
			$tmp = mb_substr_replace($str_h, '*', 1, 1);
			break;
		case 3:
			$tmp = mb_substr_replace($str_h, '**', 1, 2);
			break;
		case 4:
			$tmp = mb_substr_replace($str_h, '**', 2, 2);
			break;
		case 5:
			$tmp = mb_substr_replace($str_h, '***', 2, 3);
			break;
		case 6:
			$tmp = mb_substr_replace($str_h, '***', 3, 3);
			break;
		default:
			$tmp = mb_substr_replace($str_h, '****', 3, 4);
	}
	return $tmp;
}


function mb_substr_replace($str,$replacement,$start,$limit,$encoding="UTF-8"){
	$len = mb_strlen($str,$encoding);
	$start2 = $start+$limit;
	$prefix = mb_substr($str,0,$start,$encoding);
	$suffix = mb_substr($str,$start2,$len-$start2,$encoding);
	return $prefix.$replacement.$suffix;
}





/*
 * 检测文件类型
 */
function check_file_type($file)
{
	if(function_exists('finfo_open')){
		$file_type_list = array(
			'image/x-ms-bmp'     => array('bmp'),
			'image/jpeg'         => array('jpg', 'jpeg'),
			'image/png'          => array('png'),
			'image/gif'          => array('gif'),
			'application/pdf'    => array('pdf'),
			'application/msword' => array('doc'),
			'text/plain'         => array('txt'),
			'application/zip'    => array('docx')
		);
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($finfo,$file['tmp_name']);//取得上传文件类型
		finfo_close($finfo);
		if(isset($file_type_list[$file_type]) && in_array(strtolower(array_pop(explode('.',$file['name']))),$file_type_list[$file_type])){
			if(array_pop(explode('.', $file['name'])) == 'docx'){
				if(function_exists('zip_open')){
					$zip = @zip_open($file['tmp_name']);
					$file_names = array();
					while($zip_entry = @zip_read($zip)){
						$file_names[] = zip_entry_name($zip_entry);
					}
					if(in_array('word/settings.xml', $file_names)){
						return TRUE;
					}else{
						return FALSE;
					}
				}else{
					return TRUE;
				}
			}else{
				return TRUE;
			}
		}else{
			if(array_pop(explode('.', $file['name'])) == 'docx' && in_array('doc',$file_type_list[$file_type])){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}else{
		return TRUE;
	}
}


/**
 * 设置页面缓存
 * @param int $seconds 缓存有效时间（秒）,默认为300秒
 */
function active_proxy_cache($seconds = 300){
	//$seconds = 0;
	header("Expires: " . gmdate('D, d M Y H:i:s', time() + $seconds ). ' GMT');
}

/**
 * 随机获取只读数据库连接
 * 在只配置一个数据的情况下，返回默认的活动连接
 */
function getReadOnlyDB($DBName = '') {
	if (! defined('ENVIRONMENT') or ! file_exists( $file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php' )) {
		if(!file_exists( $file_path = APPPATH . 'config/database.php' )){
			show_error('The configuration file database.php does not exist.');
		}
	}
	include($file_path);

	if(!is_array($db) || count($db) == 0){
		show_error('The configuration file database.php has error!');
		return;
	}

	$readDBName = 'read_1'; // 默认值是第一个只读数据库
	if($DBName != '')   // 如果指定了数据库的名称，直接返回对应的数据库连接
	{
		$readDBName = $DBName;
	}elseif(count($db) == 1)    // 只有一个数据库配置，返回设置的活动连接
	{
		$readDBName = $active_group;
	}elseif(count($db) >= 3)    // 多余3个配置，认为是只读数据库有至少两个，需要随机选择一下
	{
		$selectNum = time() % (count($db)-1) + 1; // 编号从1开始
		$readDBName = 'read_' . $selectNum;
	}

	$CI = &get_instance();
	return $CI->load->database($readDBName, TRUE);
}
function getWriteOnlyDB($DBName = '') {
	if (! defined( 'ENVIRONMENT' ) or ! file_exists ( $file_path = APPPATH . 'config/' . ENVIRONMENT . '/database.php' )) {
		if (!file_exists( $file_path = APPPATH . 'config/database.php'))
		{
			show_error( 'The configuration file database.php does not exist.' );
		}
	}
	include($file_path);

	if (!is_array($db) || count($db) == 0) {
		show_error( 'The configuration file database.php has error!' );
		return;
	}

	$readDBName = 'default'; // 默认值是第一个只读数据库
	if ($DBName != '')  // 如果指定了数据库的名称，直接返回对应的数据库连接
	{
		$readDBName = $DBName;
	}elseif(count($db) == 1)    // 只有一个数据库配置，返回设置的活动连接
	{
		$readDBName = $active_group;
	}
	$CI = &get_instance ();
	return $CI->load->database($readDBName, TRUE);
}


/**
 * Redirect to another page
 *
 * This function implements either server-side (php) or client side (javascript) redirection
 * <br/>Example:
 * <code>
 * </code>
 *
 * @param string $url The url to redirect to. If 'self' is used, it is equivalent to a reload (only it isn't)
 * @param boolean $js Whether to use js-based redirection
 * @param string $target which frame to reload (only applicable when $js is true). Can be 'top', 'window' or any frame name
 * @param boolean $retainUrl Whether to retain the url as it is
 */
function ex_redirect($url, $js = false, $target = 'top') {
	if ($js) {
		echo "<script language='JavaScript'>$target.location='$url'</script>";
	} else {
		header("location:$url");
	}
	exit;
}
/**
 * 通过token，取oa用户资料
 * 线上
 * @param unknown_type $url 
 * @param unknown_type $data cookie中获取的token
 */
function post_data2($url,$data){
	//封装xml,调取用户资料
	$xml_data = '<?xml version="1.0" encoding="UTF-8"?><request><token>'.$data.'</token><employeeNumber/></request>';
	//post
	//获取用户ID，用名称？
	
	$header[] = "Content-type: text/xml";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
	 
	//处理post超时的处理
    curl_setopt($ch, CURLOPT_NOSIGNAL,1);    //注意，毫秒超时一定要设置这个  
    curl_setopt($ch, CURLOPT_TIMEOUT_MS,3000);  //超时3秒后结束请求
    
	$responce = curl_exec($ch);

	//post返回失败信息
	if(curl_errno($ch))
	{
		//print curl_error($ch);//打印错误信息
		curl_close($ch);
		//die();
		return false;
		
	}else{
		curl_close($ch);
	//{
		//$responce = '<response><status>ok</status><uid>testoa1</uid><employeeNumber>23999999</employeeNumber></response>';
		if($responce){
			$responce = '<?xml version="1.0" encoding="UTF-8"?>'.$responce;
			$res = simplexml_load_string($responce);
		}else{
			//TODO:请求oa认证接口失败，记录日志
			return false;
			//die("请求oa认证接口失败");
		}
		return $res;
	}
}
/*
 * 本地
 * 如果你想切换本地，那么你将如下post_data2函数改成post_data；上面的post_data函数改成post_data2；
 * 提交svn时请将之还原；
 */
function post_data($url,$data){
	    //封装xml,调取用户资料
	    $xml_data = '<?xml version="1.0" encoding="UTF-8"?><request><token>'.$data.'</token><employeeNumber/></request>';
		$responce = '<response><status>ok</status><uid>testoa1</uid><employeeNumber>23999999</employeeNumber></response>';
		if($responce){
			$responce = '<?xml version="1.0" encoding="UTF-8"?>'.$responce;
			$res = simplexml_load_string($responce);
		}else{
			//TODO:请求oa认证接口失败，记录日志
			return false;
			//die("请求oa认证接口失败");
		}
		return $res;
}
// XML转换成数组
function simplexml_obj2array($obj)
{
	if( count($obj) >= 1 )
	{
		$result = $keys = array();

		foreach( $obj as $key=>$value)
		{
			isset($keys[$key]) ? ($keys[$key] += 1) : ($keys[$key] = 1);

			if( $keys[$key] == 1 )
			{
				$result[$key] = simplexml_obj2array($value);
			}
			elseif( $keys[$key] == 2 )
			{
				$result[$key] = array($result[$key], $simplexml_obj2array($value));
			}
			else if( $keys[$key] > 2 )
			{
				$result[$key][] = simplexml_obj2array($value);
			}
		}
		return $result;
	}
	else if( count($obj) == 0 )
	{
		return (string)$obj;
	}
}
/**
 * 转换字符串到时间戳 
 * 
 * @param unknown_type $timestr 2001-02-23、2001.02.23、2001/02/23
 */
function tomk($timestr){
    $year=((int)substr($timestr,0,4));//取得年份
    $month=((int)substr($timestr,5,2));//取得月份
    $day=((int)substr($timestr,8,2));//取得几号
    return mktime(0,0,0,$month,$day,$year);
}

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function ci_sub_str($str, $length = 0, $append = true)
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, 'UTF-8');
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, 'UTF-8');
    }
    else
    {
        //$newstr = trim_right(substr($str, 0, $length));
        $newstr = substr($str, 0, $length);
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}


function valid_cookie($co){
    $CI=&get_instance();
    $url = $CI->config->item('ssoserver').'sso/ssoservice/getUser';
    $result = post_data($url,$co);
    if($result && $result->status == 'ok'){
        return true;
    }
    return false;
}
/*
 * $tim:当前时间戳
 * $sign ：1上一个月；0下一个月；
 */
function GetMonth($tim,$sign=1)  
{  
    $tmp_date=date("Ym",$tim);  
    $tmp_year=substr($tmp_date,0,4);  
    $tmp_mon =substr($tmp_date,4,2);  
    $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);  
    $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);  
    if($sign==0){  
        //得到当前月的下一个月   
        return $tmp_nextmonth;
    }else{  
        //得到当前月的上一个月   
        return $tmp_forwardmonth;
    }  
}
/*
 * 根据用户id得到用户昵称
 */
function getNickname($uid)
{
	$CI = & get_instance();
    $CI->load->model('config_model');
    $row = $CI->config_model->getModelsBykey('id',$uid,'nickname','identity_user');
    if($row){
        return $row->nickname;
    }else{
       return '';
    }
}



/*
 * 剩余时间
 */
//$offer_time = time() + 7 * 24 * 3600;
function days($days)
{
   $times = '';
   if($days == 0){
      return '时间不限';
   }
   $days = $days * 1;
   $days = $days - time();
   if($days <=0){
       return '已过期';
   }
   $chu = $days / (24*3600);
   $chuf = floor($chu);//天
   if($chu > $chuf){
       $shi = $days - $chuf*24*3600;
       $shi2 = $shi/3600;
       $shi3 = floor($shi2);//时
       if($shi2>$shi3){
       	   $fen = $shi - $shi3*3600;
       	   $fen = $fen/60;
       	   $fen = floor($fen);//分
       	   $times = $chuf.'天'.$shi3.'时'.$fen.'分';
       }else{
          $times = $chuf.'天'.$shi3.'时';
       }
   }else{
      $times = $chuf.'天';
   }
   return $times;
}
/*
 * 发布时间
 */
function days2($days)
{
   $times = $days;
   
   $days = $days * 1;
   $days = time() - $days;
   if($days == 0){
       return '刚刚';
   }
   $chu = $days / (24*3600);
   $chuf = floor($chu);//天
   $shi = $days - $chuf*24*3600;
   $shi2 = $shi/3600;
   $shi3 = floor($shi2);//时
   $fen = $shi - $shi3*3600;
   $fen = $fen/60;
   $fen2 = floor($fen);//分
   $strs = '';
   if($chu < 1){
       
       
       if($shi2<1){
       	   if($fen < 1){
       	      $strs = '刚刚';
       	   }else{
		       for($j=1;$j<60;$j++){
		       	  if($j<$fen){
		       	  	 $strs = $j.'分钟前';
		       	  }
		       }
       	   }
       }else{
	       for($i=1;$i<24;$i++){
	       	 if($i<$shi2){
	       		$strs = $i.'小时前';
	       	 }
	       }
      }
    
   }else if($chu<2){
      $strs = '昨天'.date('H:s',$times);
   }else if($chu<3){
   	  $strs = '前天'.date('H:s',$times);
   }else{
   	  $strs = date('Y-m-d H:i',$times);
   }
   return $strs;
}
/*
 * 时间问好
 */
function sayhello()
{
   $now = time();
   $shi = date('H',time());
   if($shi>3 && $shi<12){
       return '上午好';
   }else if($shi>12 && $shi<17){
       return '下午好';
   }else if($shi>17 && $shi<21){
      return '晚上好';
   }else if($shi>21 && $shi<24){
      return '夜里好';
   }else if($shi > 0 && $shi<3){
      return '凌晨好';
   }
}
function check_lable($uid,$pid,$lid){
	$CI = & get_instance();
    $CI->load->model('config_model');
    $arr = array('uid'=>$uid,'pid'=>$pid,'lid'=>$lid);
    $row = $CI->config_model->getModelsBykeyarray($arr,'label_project_user');
    if($row){
    	return true;
    }else{
    	return false;
    }
}
function getlablename($lid){
	$CI = & get_instance();
    $CI->load->model('config_model');
    $arr = array('id'=>$lid);
    $row = $CI->config_model->getModelsBykeyarray($arr,'label_table');
    return $row;
}
/**
 * 
 * 邮件发送
 * @param $uid 用户ID或者email地址
 * @param $mailbody
 * @param $mailsubject_nocode
 * @param $cc
 * @param $email 区分$uid 为email还是uid，false表示为uid
 */
function sendemail($uid,$mailbody,$mailsubject_nocode='创新梦工厂-系统通知',$cc='',$email=false){
	$CI = & get_instance();
	$mailConf = $CI->config->item('mail');
    $CI->load->library('Smtp');
    $smtpusermail = "cxyfzx@js.chinamobile.com";//SMTP服务器的用户邮箱 
    if($email){
    	$smtpemailto = $uid;//发送给谁
    }else{
        $smtpemailto = getEmail($uid);//发送给谁
    } 
    $mailsubject =  "=?UTF-8?B?".base64_encode($mailsubject_nocode)."?=";//邮件主题 
    $mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件 
    $smtp = new Smtp($mailConf['smtp_host'],$mailConf['smtp_port'],true,$mailConf['smtp_user'],$mailConf['smtp_pass']);//这里面的一个true是表示使用身份验证,否则不使用身份验证. 
    if($smtpemailto){
	    if('' == $cc){
	    	//$send = $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		    $log['touser']=$smtpemailto;
	    }else{
	    	//$send = $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype,$cc);
		    $log['touser']=$smtpemailto.",".$cc;
	    }
	    $send = true;
	}else{
		$log['touser']="用户无邮件地址";
		$send = false;
	}
	//记录发送日志
	$CI->load->model('config_model');
    //什么时间给谁发送了什么东西
    $log['touser']=$smtpemailto;
    if($cc !=''){
    	$log['touser'] .=",".$cc;
    }
    $log['mailtitle'] = $mailsubject_nocode;
    $log['mailbody'] = $mailbody;
    if($send){
    	$log['status'] = 1;
    }else{
    	$log['status'] = 0;
    }
    $log['ctime'] = mktime();
    $CI->config_model->insert_model("email_log",$log);
    return $send;
}

function getEmail($uid){
	$CI = & get_instance();
    $CI->load->model('user_model');
    $row = $CI->user_model->getUserFull($uid);
    if($row){
    	if($row->mail){
    		return $row->mail;
    	}
    }
    return false;
}


/**
 * 根据当前环境确定使用静态文件类型
 * $path 子路径可以是css或者js
 * $abs ture返回绝对路径
 */
function createStaticPath($path = '',$abs = FALSE){
	$src = '';
	if(defined('ENVIRONMENT')){
		if(ENVIRONMENT == 'development') {
            $src = 'assets/dev';
		}else{
            $src = 'assets/pdt';
        }
	}else{
		$src = 'assets/pdt';
	}
	if($path != ''){
		$src = $src.'/'.$path;
	}
	if($abs){
		return '/'.$src;
	}else{
	   return base_url($src);
	}
}
/**
 * 检查用户是否具有后台管理权限
 * $param $user 用户数组信息
 * @return boolean
 */
function checkAdminRight($user){
	if(is_array($user)){
		if(($user['status'] == 1) && in_array($user['auth'], array(2,999))){
			return true;
		}
	}
	return false;
}

/**
 * 检查用户是否具有上传素材权限
 * @param array $user 用户信息
 */
function check_permission($user)
{
	if(is_array($user)){
		if(($user['status'] == 1) && $user['upload_auth'] == 1){
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 检查用户管理素材权限
 * 
 * @param int $mid
 * @param int $uid
 */
function check_manager_material($mid, $uid)
{
	$CI = &get_instance();
	$CI->load->model('material_model', 'material');
	
	$check_material_query = $CI->material->check_material_of_user($mid, $uid);
	if($check_material_query['status'])
	{
		if($check_material_query['check'] == FALSE)
		{
			/*$CI->load->model('user_model', 'user');
			$user = $this->user->getUser($uid);
			if(is_array($user) && in_array($user['auth'], array(999)))
			{
				return TRUE;
			}*/
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
	return TRUE;
}

/**
 * 检查用户管理版本权限
 * 
 * @param int $vid
 * @param int $mid
 * @param int $uid
 */
function check_manager_version($vid, $mid, $uid)
{
	if(check_manager_material($mid, $uid))
	{
		return TRUE;
	}
	$CI = &get_instance();
	$CI->load->model('material_model', 'material');
	$check_version_query = $CI->material->check_version_of_user($vid, $mid, $uid);
	if($check_version_query['status'])
	{
		if($check_version_query['check'] == FALSE)
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
	
	return TRUE;
}

/**
* 检查版本是否属于素材
*
* @param int $vid
* @param int $mid
*/
function check_version_of_material($vid, $mid)
{
	$CI = &get_instance();
	$CI->load->model('material_model', 'material');
	$check_version_query = $CI->material->check_version_of_material($vid, $mid);
	if($check_version_query['status'])
	{
		if($check_version_query['check'] == FALSE)
		{
			return FALSE;
		}
	}
	else
	{
		return FALSE;
	}
	return TRUE;
}

/**
 * 生成zip文件
 * @param array $files
 */
function create_zip($files)
{
	if(empty($files))
	{
		return '';
	}
	
	$zip = new ZipArchive();
	$filename = 'uploads/zip/' . md5(uniqid()) . '.zip';
	
	if($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE)
	{
		return '';
	}
	
	$has_exists = array();
	foreach($files as $file)
	{	
		if(file_exists($file['rname']))
		{
			if(in_array($file['sname'], $has_exists))
			{
				$zip->addFile($file['rname'], iconv("UTF-8","GB2312",$file['id'] . '_' .$file['sname']));
			}
			else
			{
				$zip->addFile($file['rname'], iconv("UTF-8","GB2312",$file['sname']));
				$has_exists[] = $file['sname'];
			}
		}
	}
	$zip->close();
	return $filename;
}

/**
 * 检查用户查看/下载权限
 * @param $material
 * @param $user
 * @return $res 1:需要登陆,2无权查看,3正常
 */
function check_view_down_material($material, $user)
{
	$CI = &get_instance();
	$CI->load->model('material_model', 'material');
	$res = 3;
	if($material['vright'] == 2)
	{
		if(empty($user))
		{
			//show_error('登录后才能查看');
			$res = 1;
		}
	}
	elseif($material['vright'] == 3)
	{
		//查询允许用户
		$allow_uids = array();
		$allow_users_query = $CI->material->allow_users($material['id']);
		if($allow_users_query['status'])
		{
			$allow_users = $allow_users_query['users'];
			foreach($allow_users as $allow_user)
			{
				$allow_uids[] = $allow_user['uid'];
			}
		}
		
		if(empty($user) || ! in_array($user['id'], $allow_uids))
		{
			//show_error('您无权查看');
			$res = 2;
		}else{
			$res = 3;
		}
	}
	return $res;
		
}

/**
 * 生成访问日志
 * @param $type
 */
function create_visit($type)
{
	$CI = &get_instance();
	$CI->load->library('user_agent');
    $r['curl'] = $_SERVER['HTTP_REFERER'];//当前请求地址
    $r['browser'] = $CI->agent->browser();//浏览器
    $r['browserVer'] = $CI->agent->version();//浏览器版本
    $r['browserAll'] = $CI->agent->browser().' '.$CI->agent->version();
    $r['agent'] = $CI->agent->agent_string();
    $r['ip'] = $CI->input->ip_address();
    $r['reference'] = urldecode($CI->input->get('ur'));
    $r['isrobot'] = ($CI->agent->is_robot())?1:0;
    $r['robot'] = $CI->agent->robot();
    $r['platform'] = $CI->agent->platform();
    $r['time'] = mktime();
    $r['ctitle'] = urldecode($CI->input->get('t'));
    $user = checklogin();
    if(!$user){
    	$r['uid'] = 0;//匿名
    }else{
    	$r['uid'] = $user['id'];
    }
    $r['usign'] = get_cookie('sign',true);
    $r['type'] = $type;
    
    $CI->load->model('visit_log_model');
    $CI->visit_log_model->save($r);
}
