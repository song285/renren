<?php 

	// 接收客户端AJAX请求 返回评论数据

	// 载入封装的函数
	require_once '../../functions.php';


	// 处理展示数据的
  	$page = isset($_GET['p']) && is_numeric($_GET['p']) ? intval($_GET['p']) : 1;

  	if ($page <= 0) {
  		header('Location: ../admin/comments.php?p=1');
  		exit();
  	}

  	$size = isset($_GET['s']) && is_numeric($_GET['s']) ? intval($_GET['s']) : 8;

	$total_count=get_data_one("select count(1) as count from comments inner join posts on comments.post_id = posts.id")['count'];

	$total_pages = ceil($total_count / $size);

  	// 计算出越过多少条
  	$offset = ($page - 1) * $size;

	$sql="select 
		comments.*,
		posts.title as post_title 
		from comments
		inner join posts on comments.post_id = posts.id
		order by comments.status asc limit {$offset},{$size}";

	// 查询所有的评论数据
	$comments = get_data_all($sql);

	

	// 将数据转换成字符串（json格式）
	$json = json_encode(array(
		"success" => true,
		"total_pages" => $total_pages,
		"comments" => $comments
	));

	// 设置响应的响应体类型为json格式
	header("Content-Type: application/json");

	// var_dump($page);

	// 返回给客户端
	echo $json;