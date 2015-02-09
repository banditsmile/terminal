<?php
/**
 * Created by PhpStorm.
 * User: bandit
 * Date: 2015/2/8
 * Time: 18:23
 */
/**
 * @param $class
 * @param $method
 * @return mixed
 */
function get_describe($class,$method){
	$describe_file = dirname(__FILE__).'/describe/'.$class.'.php';
	$describe = include $describe_file;
	return isset($describe[$method]) ? $describe[$method]: '';
}