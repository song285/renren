<?php
	header("Content-type:text/html;charset=utf-8");
	require_once '../config.php';
  	require_once '../functions.php';

	if($_GET['nickname']){
		$nickname=$_GET['nickname'];
		
		$rows = myexecute("SELECT * FROM user WHERE nickname='{$nickname}'");

		if($rows >= 1)
			echo "昵称已被使用";
		else
			echo "昵称可以使用";
	}
?> 