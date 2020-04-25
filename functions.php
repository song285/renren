<?php 
	
	require_once 'config.php';

	// 封装公共的函数
	session_start();
	/*
		获取当前登录用户信息，如果没有获取到就自动跳转到登录信息
	*/
	function getCurrentUser(){
		if (!isset($_SESSION['current_logged_user'])) {
		    // 没有当前登录用户信息
		    header('Location: ../admin/login.php');
		    exit();//成功后没必要再执行该函数下面的代码
		}
		return $_SESSION['current_logged_user'];
	}

	/*
		获取前台当前登录用户信息
	*/

	function getCurrentUser_fontend(){
		if (!isset($_SESSION['current_fontend_user'])) {
		    // // 没有当前登录用户信息
		    return false;
		}
		return $_SESSION['current_fontend_user'];
	}


	/*
		封装数据库查询函数  获取多条数据
	*/
	function get_data_all($sql){
		$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
		if(!$conn){
			// 连接失败直接退出
			exit("连接失败");
		}
		mysqli_set_charset($conn,'utf8');
		$query=mysqli_query($conn,$sql);
		if (!$query) {
			// 查询失败 需要反馈给用户
			return false;
		}

		$result=array();

		while ($row=mysqli_fetch_assoc($query)) {
			$result[]=$row;
		}

		mysqli_free_result($query);
		mysqli_close($conn);
		return $result;
	}

	/*获取单条数据*/
	function get_data_one($sql){
		$res=get_data_all($sql);
		return isset($res[0])?$res[0]:null;
	}

	/*
		执行一个增删改的语句
	*/
	function myexecute($sql){
		$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
		if(!$conn){
			// 连接失败直接退出
			exit("连接失败");
		}
		mysqli_set_charset($conn,'utf8');
		$query=mysqli_query($conn,$sql);
		if (!$query) {
			// 查询失败 需要反馈给用户
			return false;
		}

		// 对于增删改类型的操作
		$affected_rows=mysqli_affected_rows($conn);

		mysqli_close($conn);

		return $affected_rows;
	}

	// 生成不重复的随机数组
	function unique_rand($min, $max, $num) {
	    //初始化变量为0
	    $count = 0;
	    //建一个新数组
	    $return = array();
	    while ($count < $num) {
	      //在一定范围内随机生成一个数放入数组中
	      $return[] = mt_rand($min, $max);
	      //去除数组中的重复值用了“翻翻法”，就是用array_flip()把数组的key和value交换两次。这种做法比用 array_unique() 快得多。
	      $return = array_flip(array_flip($return));
	      //将数组的数量存入变量count中
	      $count = count($return);
	    }
	    //为数组赋予新的键名
	    shuffle($return);
	    return $return;
	}

	/*获取外网IP*/
	function getIP(){
		// $externalContent = file_get_contents('http://checkip.dyndns.com/');
		// preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
		// $externalIp = $m[1];
		// return $externalIp;
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
		$ip = getenv("REMOTE_ADDR"); 
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
		else 
		$ip = "unknown"; 
		return($ip);
	}

	/*
		中文转unicode
	*/
	function UnicodeEncode($str){
	    //split word
	    preg_match_all('/./u',$str,$matches);
	 
	    $unicodeStr = "";
	    foreach($matches[0] as $m){
	        //拼接
	        $unicodeStr .= "&#".base_convert(bin2hex(iconv('UTF-8',"UCS-4",$m)),16,10);
	    }
	    return $unicodeStr;
	}