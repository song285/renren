<?php 
	
	header('content-type:application/json;charset=utf8'); 

    require_once '../config.php';
    require_once '../functions.php';
	
	$category_id=$_GET['id'];
    $page = $_GET['page']+4;

  $newposts=get_data_all("
    SELECT a.id,b.title AS category,a.title,a.content,a.feature,a.created,a.views,a.likes,d.nickname AS author
    FROM posts AS a,navmenus AS b,users AS d 
    WHERE a.user_id=d.id AND a.category_id=b.id AND a.status='published' AND a.category_id='{$category_id}'
    GROUP BY a.id,a.title,a.content,a.created,a.views,a.likes,d.nickname,a.feature,category
    ORDER BY a.created DESC LIMIT $page");

    $total = count($newposts);

    $json = json_encode(array(
        "success" => true,
        "total" => $total,
        "main" => $newposts
    ));

    // $json=json_encode($newposts);
    echo $json;

