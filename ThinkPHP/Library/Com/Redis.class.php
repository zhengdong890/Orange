<?php
namespace Com;
header("content-type:text/html;charset=utf-8");
class Redis{
	public function __construct(){
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
	}
	
	public function set($key , $val){
	    if(is_array($val)){
	        $val = serialize($val);
	    }
	    return $this->redis->set($key , $val);
	}
	
	public function get($key , $type = 'string'){
	    $val = $this->redis->get($key);
	    if($type == 'array'){
	        $val = unserialize($val);
	    }
	    return $val;
	}
}