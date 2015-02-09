<?php
/**
 * Created by PhpStorm.
 * User: bandit
 * Date: 2015/2/8
 * Time: 18:33
 */

require('json-rpc.php');
require('functions.php');

if (function_exists('xdebug_disable')) {
	xdebug_disable();
}

class Controller {
	static $login_documentation = "login to the server (return token)";
	public function login($user, $passwd) {
		if (strcmp($user, 'demo') == 0 && strcmp($passwd, 'demo') == 0) {
			// If you need to handle more than one user you can create
			// new token and save it in database
			// UPDATE users SET token = '$token' WHERE name = '$user'
			return md5($user . ":" . $passwd);
		} else {
			throw new Exception("Wrong Password");
		}
	}

	static $ls_documentation = "list directory if token is valid";
	public function ls($token, $path = null) {
		if (strcmp(md5("demo:demo"), $token) == 0) {
			if (preg_match("/\.\./", $path)) {
				throw new Exception("No directory traversal Dude");
			}
			$base = preg_replace("/(.*\/).*/", "$1", $_SERVER["SCRIPT_FILENAME"]);
			$path = $base . ($path[0] != '/' ? "/" : "") . $path;
			$dir = opendir($path);
			while($name = readdir($dir)) {
				$fname = $path."/".$name;
				if (!is_dir($name) && !is_dir($fname)) {
					$list[] = $name;
				}
			}
			closedir($dir);
			return $list;
		} else {
			throw new Exception("Access Denied");
		}
	}
	static $whoami_documentation = "return user information";
	public function whoami($token) {
		return array("your User Agent" => $_SERVER["HTTP_USER_AGENT"],
			"your IP" => $_SERVER['REMOTE_ADDR'],
			"you acces this from" => $_SERVER["HTTP_REFERER"]);
	}

	public function describe($token=''){
		return array('describe'=>'aaaaaaaaaaaaaaaaaa');
	}

	static $redis_documentation = "在浏览器里面像终端一样调用php redis扩展里面的函数例如set aa bb";
	public function redis($token,$param=''){
		do_debug($token);
		$instance = new Redis();
		do_debug('redis connect:'.$instance->connect('localhost'));
		$return  = $this->do_command($token,$instance);
		return $return;
	}

	static $memcache_documentation = "在浏览器里面像终端一样调用php memcache扩展里面的函数例如get";
	public function memcache($token,$param=''){
		do_debug($token);
		$instance = new Memcache();
		do_debug('memcache connect:'.$instance->connect('localhost'));
		$return  = $this->do_command($token,$instance);
		return $return;
	}

	protected function do_command($token,$instance){
		$input = explode(' ',$token);
		$method = $input[0];
		unset($input[0]);
		$param = $input;
		if($method == 'describe'){
			if(is_array($param) and count($param)){
				$return =  get_describe(strtolower(get_class($instance)),$param[0]);
			}else{
				$return = 'please input a valid method name after describe';
			}
		}else{
			try{
				$return = call_user_func_array(array($instance,$method),$param);
				do_debug('command output:'.$return);
			}catch(Exception $e){
				$return = $e->getMessage();
				do_debug($return);
			}
			$return = is_string($return) ? $return : json_encode($return);
		}
		return $return;
	}
}

handle_json_rpc(new Controller());