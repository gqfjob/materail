<?php
function getRedis($prefix='',$config=false){
    $CI =& get_instance();    
    $CI->config->load('redis', TRUE, TRUE);
    $tmp = $CI->config->item('redis');
    if(!$config){
        $redis_config = $tmp['redis'];
    }else{
        $redis_config = $config;
    }
    try{
        $redis = new Redis();
        $redis->connect($redis_config['host'], $redis_config['port'], $redis_config['timeout']);
        if('' != $prefix){
            $redis->setOption(Redis::OPT_PREFIX, $prefix);
        }
    }catch (RedisException $e){
        //log err
        log_message('error', 'Redis connection refused. ' . $e->getMessage());
        $redis = NULL;
    }
    return $redis;
}