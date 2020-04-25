<?php

	require_once '../functions.php';

	

	/*根据客户端传进来的ID 进行删除*/
	if (empty($_GET['id'])) {
		exit(json_encode(array(
			"success" => false,
			"message" => "缺少必要参数"
		)));
	}

	// 前置转换 防止 sql注入
	$id=$_GET['id'];
	// $slug=$_GET['slug'];


	$rows=myexecute("delete from comments where id in (" . $id . ");");

    header("Content-Type: application/json");

	echo json_encode($rows > 0);