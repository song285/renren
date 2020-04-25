<?php 
  require_once '../functions.php';
  getCurrentUser();

  $current_user=getCurrentUser();

  function edit_info(){
    global $current_user;

    if (empty($_POST['oldpwd']) || empty($_POST['newpwd']) || empty($_POST['confirmpwd'])) {
      $GLOBALS['message'] = '内容不能为空';
      return;
    }

    // 接收并保存
    $oldpwd=$_POST['oldpwd'];
    $newpwd=$_POST['newpwd'];
    $confirmpwd=$_POST['confirmpwd'];

    if ($oldpwd!==$current_user['password']) {
      $GLOBALS['message'] = '旧密码不准确';
      return;
    }

    if ($newpwd!==$confirmpwd) {
      $GLOBALS['message'] = '两次密码不一致';
      return;
    }

    // 更新数据到数据库
    $rows=myexecute("update users set password='{$confirmpwd}' where id='{$current_user['id']}';");

    $GLOBALS['message'] = $rows <= 0 ? '修改失败！' : '修改成功！';

    sleep(2);
    
    header('Location: ../admin/login.php?action=logout');
  }

  if ($_SERVER['REQUEST_METHOD']==="POST") {
    edit_info();
  }

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-修改密码</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert <?php echo $message === '修改成功！' ? ' alert-success' : ' alert-danger' ?>">
          <strong>提示:</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" type="password" placeholder="旧密码" name="oldpwd">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" type="password" placeholder="新密码" name="newpwd">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" type="password" placeholder="确认新密码" name="confirmpwd">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page='password-reset' ?>
  <?php include 'includes/aside.php'; ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
<!--   <script>
      let time=3;
      // while(time>0){
      //   document.querySelector('#time').innerText=time;
      //   if (time<=0) {
      //     window.location.href='../admin/login.php?action=logout';
      //     break;
      //   }
      //   time--;
      // }
    $('.col-sm-offset-3').on('click','.btn-primary',function(){
      setInterval(function(){
        if (time<=0) {
          window.location.href='../admin/login.php?action=logout';
        }
        document.querySelector('#time').innerText=time;
        time--;
        console.log(time);
      },1000);
    })
        

  </script> -->
</body>
</html>
