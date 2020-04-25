<?php 

require_once '../functions.php';

header("Content-Type: application/json");

if (empty($_GET['id'] || empty($_POST['status']))) {
	exit(json_encode(array(
		"success" => false,
		"message" => "缺少必要参数"
	)));
}

$status=$_POST['status'];
$id=$_GET['id'];

$rows = myexecute("update comments set status ='{$status}' where id in ({$id})");

echo json_encode(array(
	"success" => $rows > 0
));