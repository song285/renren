<?php 

	header("Content-type:text/html;charset=utf-8");

	require_once '../config.php';
  require_once '../functions.php';

  $id = $_GET['id'];

  // 记录访问者的IP  实现点赞功能

  $ip = getIP();

  $dataip = myexecute("SELECT * FROM zanip where ip='{$ip}' AND postid='{$id}'");

  if ($dataip <= 0) {
  	$rows = myexecute("UPDATE posts SET likes=likes+1 WHERE id='{$id}'");
  	$res = get_data_one("SELECT * FROM posts WHERE id='{$id}'");
  	myexecute("INSERT INTO zanip VALUES (null,'{$ip}','{$id}')");
  	echo $res['likes'];
  }else{
  	echo 'false';
  }