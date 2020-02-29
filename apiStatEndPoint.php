<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("Backend.php");
$server = new Backend("http://become.weblife.co.il/api/users.php","become","become-2019");
$server->AjaxResponse2();
?>