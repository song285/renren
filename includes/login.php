<?php 

	// 引入数据库配置文件
  require_once '../songguo/config.php';

  // session_start();

  function login(){

  	// global $currrent_user;

  	$username = $_POST['username'];
  	$password = $_POST['password'];

  	if (empty($username) || empty($password)) {
  		$GLOBALS['err_message'] = '请输入账号或密码';
  		return;
  	}

  	// 连接数据库
    $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    if (!$conn) {
      exit("连接失败");
    }

    $result=mysqli_query($conn,sprintf("select * from user where phone='{$username}' limit 1;"));

    if (!$result) {
      $GLOBALS['err_message']="登录失败，请重试";
      return;
    }

    $user=mysqli_fetch_assoc($result);

    if (!preg_match("/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/", $username)) {
      $GLOBALS['err_message']="请输入正确的手机号";
      return;
    }

    if (!$user) {
      $GLOBALS['err_message']="账号不存在";
      return;
    }

    if ($user['password'] !== $password) {
      $GLOBALS['err_message']="密码不准确";
      return;
    }

    mysqli_free_result($result);
    mysqli_close($conn);

    $_SESSION['current_fontend_user']=$user;

    header("Refresh:0");

  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    login();
  }

 ?>
<div class="widgets">
  <div class="slink">
  	<?php if (isset($err_message)): ?>
      <!-- 有错误信息时展示 -->
      <div style="color: #ff5e52;">
        <p><?php echo $err_message ?></p>
      </div>
  	<?php endif ?>
  	<div class="login-box">
	  	<p id="message" style="color: #ff5e52;"></p>
	    <form method="POST" action="">
	      <input class="phone" id="phone" type="phone" name="username" placeholder="手机号码">
	      <input class="password" type="password" name="password" placeholder="密码">
	      <div class="login-btn">
	        <input type="submit" class="loginbtn" value="登录"></input>
	        <a href="../songguo/register.php">注册</a>
	      </div>
	    </form>
    </div>
  </div>
</div>
<script src="../songguo/assets/vendors/jquery/jquery.js"></script>
<script>
	// 手机格式验证
	$("#phone").blur(function(){ 
		var str = $("#phone").val();
	  var reg = /^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[678])[0-9]{8}$/;
	  if (!reg.test(str)){
	    $("#message").html("请输入正确的手机号");
	    return false;
	  }
	})
</script>