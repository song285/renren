<?php 
	require_once 'config.php';
  require_once 'functions.php';

  function register(){
  	if (empty($_POST['phone']) || empty($_POST['password']) || empty($_POST['confirm']) || empty($_POST['nickname'])) {
  		$GLOBALS['message'] = '请填写完整内容';
  		return;
  	}

  	$avatar = $_FILES['avatar'];
  	
  	if ($avatar["error"]!==UPLOAD_ERR_OK) {
  		$GLOBALS['message'] = '请上传头像';
  		return;
  	}

  	// 校验文件的大小
	if ($avatar["size"]>20*1024*1024) {
		$GLOBALS["message"]="图片文件过大";
		return;
	}

	// 校验文件的类型
	$allowed_types=array('image/png','image/jpg','image/jpeg');
	if (!in_array($avatar['type'], $allowed_types)) {
		$GLOBALS['message']='图片文件格式不正确';
		return;
	}

	// 把图片从临时目录移动到真实目录
	// 一般情况会将上传的文件重命名
	$targetPic="../songguo/uploads/" . uniqid() . $avatar["name"];
	if (!move_uploaded_file($avatar["tmp_name"], $targetPic)) {
		$GLOBALS["message"]="上传图片失败";
		return;
	}

  	// 获取网页内容
  	$nickname = $_POST['nickname'];
  	$phone = $_POST['phone'];
  	$password = $_POST['password'];
  	$confirm = $_POST['confirm'];

  	if (!preg_match("/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/", $phone)) {
      $GLOBALS['message']="请输入正确的手机号";
      return;
    }

  	if ($password !== $confirm) {
  		$GLOBALS['message'] = '两次密码不一致';
  		return;
  	}

  	$row = myexecute("insert into user values(null,'{$targetPic}','{$nickname}','{$phone}','{$password}','activated')");

  	$GLOBALS['message'] = $row <= 0 ? '注册失败' : '注册成功';
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    register();
  }
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- 网站设置 -->
  <?php include 'includes/setting.php'; ?>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.css">
  <script src="assets/vendors/jquery/jquery.js"></script>
	<style type="text/css">
		body{background-color: #fff}
	</style>
</head>
<body>
	<div class="form-area">
		<ul class="account-selector">
			<li class="active">手机注册</li>
		</ul>
		<div class="item">
			<?php if (isset($message)): ?>
		        <!-- 有错误信息时展示 -->
		        <div style="color: #ff5e52;">
		          <p><?php echo $message ?></p>
		        </div>
		  	<?php endif ?>
		  	<p id="message" style="color: #ff5e52;"></p>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="on" enctype="multipart/form-data">
				<div style="position: relative;">
					<img id="img" src="" style="height: 53px;width: 53px;position: absolute;top: 1px;right: 1px;border-radius: 3px;">
					<input class="text rect" type="file" id="avatar" name="avatar" accept="image/*">
				</div>
				<input class="text rect" type="text" id="nickname" name="nickname" placeholder="昵称">
				<input class="text rect" type="tel" id="phone" name="phone" placeholder="手机号" autocomplete="off">
				<input class="text rect" type="password" name="password" placeholder="密码">
				<input class="text rect" type="password" name="confirm" placeholder="确认密码">
				<input class="register-btn rect" type="submit" value="注册">
			</form>
			<div class="register-tip">点击注册，即表示您阅读且同意<a href="javascript:;">《松果服务协议》</a></div>
			<div class="switch-to-login">已有账号？<a href="../songguo/">立即登录</a></div>
		</div>
	</div>
	<script>
	  var file = document.getElementById('avatar');
	  var img = document.getElementById('img');

	  file.onchange = function(){
	    // 把图片对象转成临时url
	    // icon.style.display='inline';
	    img.src =  URL.createObjectURL(this.files[0]);
	  }

		//异步验证昵称
		$("#nickname").blur(function(){ //文本框鼠标焦点消失事件
			$.get("/songguo/includes/check_nickname.php?nickname="+$("#nickname").val(),null,function(data){
				$("#message").html(data); 
			});
		})

		//异步验证手机号
		$("#phone").blur(function(){ //文本框鼠标焦点消失事件
			// 手机格式验证
			var str = $("#phone").val();
		    var reg = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/;
		    if (!reg.test(str)){
			    $("#message").html("请检查手机号码是否正确");
			    return false;
		    }

			$.get("/songguo/includes/check_phone.php?phone="+$("#phone").val(),null,function(data){
				$("#message").html(data); 
			});
		})

	</script>
</body>
</html>