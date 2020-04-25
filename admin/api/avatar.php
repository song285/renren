<?php 

	// 根据用户邮箱获取用户头像

	// 1.接收传递过来的参数
	// 2.查询对应的头像地址
	// echo

	require_once '../../config.php';

	if (empty($_GET['email'])) {
		exit("缺少必要参数");
	}

	$email= $_GET['email'];


	$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

	if (!$conn) {
		exit("连接数据库失败");
	}

	$res=mysqli_query($conn,sprintf("select avatar from users where email='{$email}' limit 1;"));
	if (!$res) {
		exit("查询失败");
	}

	$row=mysqli_fetch_assoc($res);

	echo $row['avatar'];