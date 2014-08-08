<?php

/**
 *
 * query_cache
 *
 * 查询缓存，返回数据集。如果缓存不存在或系统不支持缓存，则直接从model中提取。
 *
 *
 * @param   string  $model  Model name from which we retrieve data
 * @param   string  $method Method name of such model
 * @param   array   $params Params given to such method 
 * @param   int $ttl    time to live of the cache item (in seconds) ,-1 表示不缓存，直接向数据库查询
 * @return  mixed   Cached data
 */
function query_cache($model, $method, $params = array(), $ttl = 10000)
{
    $CI = & get_instance();
    $CI ->load->driver('cache', array('adapter' => 'redis'));
    // Required model not loaded?
    // Load models on demand
    //if( ! in_array($model, $CI->load->_ci_models, TRUE))
    //{
        $CI->load->model($model);
    //}
    //generate cache key
    $tmp = '';
    if(!empty($params)){
        $tmp.="_".md5(serialize($params));
    }
    $key = $model.'_'.$method.$tmp;
    // Ref this model
    $handler = & $CI->$model;
    // Cache is disabled when we are in DEV or other unkonwn situations
    if(( ! $CI->cache->redis->is_supported()) || (-1 == $ttl))
    {
        return call_user_func_array(array($handler, $method), $params);
    }

    // Valid cache item? If so, we've done!
    $data = $CI->cache->redis->get($key);
    if(!$data)
    {
        // Fetch data from model
        $data = call_user_func_array(array($handler, $method), $params);
        // WARINING: EMPTY results (such as 0, FALSE) may be ignored!
        if( ! empty($data))
        {
            // Make the results cacheable!
            $CI->cache->redis->save($key, $data, $ttl);
        }
    }
    
    return $data;

}

/**
 *
 * update_cache
 *
 * 更新缓存，返回数据集。
 *
 *
 * @param   string  $model  Model name from which we retrieve data
 * @param   string  $method Method name of such model
 * @param   array   $params Params given to such method 
 * @param   int $ttl    time to live of the cache item (in seconds)
 * @return  mixed   Cached data
 */
function update_cache($model, $method, $params = array(), $ttl = 10000)
{
    $CI = & get_instance();
    $CI ->load->driver('cache', array('adapter' => 'redis'));
    
    if(  $CI->cache->redis->is_supported())
    {
        $tmp = '';
        if(!empty($params)){
            $tmp.="_".md5(serialize($params));
        }
        $key = $model.'_'.$method.$tmp;
        $CI->load->model($model);
        $handler = & $CI->$model;
        $CI->cache->redis->delete($key);   
        $data = call_user_func_array(array($handler, $method), $params);
        // WARINING: EMPTY results (such as 0, FALSE) may be ignored!
        if( ! empty($data))
        {
            $CI->cache->redis->save($key, $data, $ttl);
        }
    }
    return $data;
}
/**
 *
 * delete_cache
 *
 * 删除缓存。
 *
 *
 * @param   string  $model  Model name from which we retrieve data
 * @param   string  $method Method name of such model
 * @param   array   $params Params given to such method 
 * @return  true/false
 */
function delete_cache($model, $method, $params = array()){
    $CI = & get_instance();
    $CI ->load->driver('cache', array('adapter' => 'redis'));
    
    if( ! $CI->cache->redis->is_supported())
    {
        $tmp = '';
        if(!empty($params)){
            $tmp.="_".md5(serialize($params));
        }
        $key = $model.'_'.$method.$tmp;
    
        return $CI->cache->redis->delete($key);   
    }
    return FALSE;
}

/**
 *
 * getCache
 *
 * 查询缓存，返回数据集。
 *
 *
 * @param   string  $key  缓存key
 * @param   string  $$region 缓存区
 * @return  mixed   Cached data
 */
function getCache($key,$region = '')
{
	if(''!=$key){
	    $CI = & get_instance();
	    $CI ->load->driver('cache', array('adapter' => 'redis'));
	    // Valid cache item? If so, we've done!
	    $data = $CI->cache->redis->get($key,$region);
	    return $data;
	}

}

/**
 *
 * getCache
 *
 * 查询缓存，返回数据集。
 *
 *
 * @param   string  $key  缓存key
 * @param   string  $val  缓存值
 * @param   string  $region 缓存区
 * @param   string  $ttl 缓存时间
 * @return  mixed   Cached data
 */
function setCache($key,$val,$region = '',$ttl = 10000)
{
    if(''!=$key){
        $CI = & get_instance();
        $CI ->load->driver('cache', array('adapter' => 'redis'));
        $value = serialize($val);
        $data = $CI->cache->redis->save($key, $value, $ttl, $region);
        return $data;
    }

}
/**
 *
 * delCache
 *
 * 清除$key。
 *
 *
 * @param   string  $key  缓存key
 * @return  true/false
 */
function delCache($key, $region=''){
    $CI = & get_instance();
    $CI ->load->driver('cache', array('adapter' => 'redis'));
    
    if( $CI->cache->redis->is_supported())
    {
        return $CI->cache->redis->delete($key,$region);   
    }
    return FALSE;
}

function flushAll(){
    $CI = & get_instance();
    $CI ->load->driver('cache', array('adapter' => 'redis'));
    
    if( $CI->cache->redis->is_supported())
    {
        return $CI->cache->redis->clean();   
    }
    return FALSE;
}