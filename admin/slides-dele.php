<?php 

	require_once '../functions.php';

	/*根据客户端传进来的ID 进行删除*/
	if (empty($_GET['id'])) {
		exit('缺少必要的参数');
	}

	// 前置转换 防止 sql注入
	$id=$_GET['id'];
	// $slug=$_GET['slug'];


	$rows=myexecute("delete from slides where id in (" . $id . ");");

	// var_dump($id);
	// var_dump($slug);

	header('Location: ../admin/slides.php');