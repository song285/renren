<?php
	header("Content-type:text/html;charset=utf-8");
	require_once '../config.php';
  require_once '../functions.php';

	if($_GET['phone']){
		$phone=$_GET['phone'];
		
		$rows = myexecute("SELECT * FROM user WHERE phone='{$phone}'");

		if($rows >= 1)
			echo "手机已被使用";
		else
			echo "手机可以使用";
	}
?> 