<?php

$dir_name = dirname(__FILE__);

require($dir_name . '/json-rpc/redis-json-rpc.php');

if (function_exists('xdebug_disable')) {
	xdebug_disable();
}

class RedisDemo extends Redis{
	var $is_connected = false;
	public function __construct(){
		$this->init();
	}
	public function init(){
		$this->connect('localhost');
		$this->is_connected = true;
	}
	public function query($query) {
		do_debug($query);
		$argument = explode(' ',$query);
		if(empty($argument)){
			throw new Exception("You need to input your command'" .$query . "'");
		}
		$method = $argument[0];
		if(!method_exists($this,$method)){
			throw new Exception(__CLASS__." has no method :" .$method);
		}
		unset($argument[0]);
		try{
			$result = call_user_func_array(array($this,$method),(array)$argument);
			do_debug(print_r($result,true));
		}catch(Exception $e){
			echo $e->getMessage();
		}
		return $result;
	}
}

handle_json_rpc(new RedisDemo());


?>
