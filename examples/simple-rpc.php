<?php
/**
 * Created by PhpStorm.
 * User: bandit
 * Date: 2015/2/8
 * Time: 18:21
 */
include 'functions.php';
$result = get_defined_functions();
response($result,0,NULL);