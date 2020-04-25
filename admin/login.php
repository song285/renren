<?php 

  // 引入数据库配置文件
  require_once '../config.php';

  // 给用户找一个箱子 如果之前有就用之前的，没有就给个新的
  session_start();

  function login(){
    // 1.接收并校验
    // 2.持久化
    // 3.响应
    $email=$_POST['email'];
    $password=$_POST['password'];

    // 连接数据库
    $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

    if (!$conn) {
      exit("连接失败");
    }
    mysqli_set_charset($conn,'utf8');
    $result=mysqli_query($conn,sprintf("select * from users where email='{$email}' limit 1;"));

    if (!$result) {
      $GLOBALS['message']="登录失败，请重试";
      return;
    }

    $user=mysqli_fetch_assoc($result);

    if (!$user) {
      $GLOBALS['message']="邮箱不存在";
      return;
    }

    if ($user['password'] !== $_POST['password']) {
      $GLOBALS['message']="密码不准确";
      return;
    }

    if ($user['status'] === 'unactivated') {
      $GLOBALS['message']="账号未激活";
      return;
    }

    if ($user['status'] === 'forbidden') {
      $GLOBALS['message']="账号已禁用";
      return;
    }

    if ($user['status'] === 'trashed') {
      $GLOBALS['message']="账号已注销";
      return;
    }

    if (empty($email)) {
      $GLOBALS['message']="邮箱不能为空";
      return;
    }

    if (empty($email)) {
      $GLOBALS['message']="密码不能为空";
      return;
    }

    if (!preg_match("/^([\w\-]+\@[\w\-]+\.[\w\-]+)$/", $email)) {
      $GLOBALS['message']="请输入正确的邮箱";
      return;
    }

    mysqli_free_result($result);
    mysqli_close($conn);

    // 存一个登录标识
    // $_SESSION['is_logged']=true;
    $_SESSION['current_logged_user']=$user;

    header('Location: ../admin/index.php');



  }


  if ($_SERVER['REQUEST_METHOD']==='POST') {
    login();
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 删除登录标识
    unset($_SESSION['current_logged_user']);
    header('Location: ../admin/login.php');
  }


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台登录</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/animated/animate.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="login">
    <form novalidate class="login-wrap <?php echo isset($message)?' shake animated':'' ?>" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off">
      <img class="avatar" src="../assets/img/default.png">
      <?php if (isset($message)): ?>
        <!-- 有错误信息时展示 -->
        <div class="alert alert-danger">
          <strong>错误！</strong> <?php echo $message ?>
        </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" value="<?php echo isset($_POST['email'])?$_POST['email']:'';?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" type="password" class="form-control" placeholder="密码" name="password">
      </div>
      <button class="btn btn-primary btn-block" href="index.php">登 录</button>
    </form>
  </div>
  <script src="../assets/vendors/jquery/jquery.min.js"></script>
  <script type="text/javascript">
    $(function($){
      // 目标：用户输入自己的邮箱后，页面上显示对应的头像
      var emailFormat=/\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/;
      $('#email').on('blur',function(){
        var value=$(this).val();
        if (!value || !emailFormat.test(value)) return;

        // 获取这个邮箱对应的头像地址，显示到上面的img元素上
        $.get("../admin/api/avatar.php",{email:value},function(res){
          if (!res) return;
          $('.avatar').fadeOut(function(){
            $(this).on("load",function(){
              $(this).fadeIn();
            }).attr('src',res);
          })
        });

      });
    })
  </script>
</body>
</html>
