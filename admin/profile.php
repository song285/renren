<?php 
  require_once '../functions.php';
  getCurrentUser();

  global $id;

  $id=getCurrentUser();

  function edit_info(){
    global $current_edit;

    // 处理图片文件
    // ==================================
    // 校验文件
    if (!isset($_FILES["avatar"])) {
      $GLOBALS["message"]="请正确操作";
      return;
    }
    
    $avatar=$_FILES["avatar"];

    // $id=$_GET['id'];

    // 判断用户是否选择了文件
    if ($avatar["error"]!==UPLOAD_ERR_OK) {
      // $GLOBALS["message"]="请选择图片文件";
      // return;

      // 接收数据
      $email=empty($_POST['email']) ? $current_edit['email'] : $_POST['email'];
      // 同步数据
      $current_edit['email']=$email;

      $slug=empty($_POST['slug']) ? $current_edit['slug'] : $_POST['slug'];
      // 同步数据
      $current_edit['slug']=$slug;

      $nickname=empty($_POST['nickname']) ? $current_edit['nickname'] : $_POST['nickname'];
      // 同步数据
      $current_edit['nickname']=$nickname;

      $bio=empty($_POST['bio']) ? $current_edit['bio'] : $_POST['bio'];
      // 同步数据
      $current_edit['bio']=$bio;

      // 更新数据到数据库
      $rows = myexecute("update users set slug='{$slug}', nickname='{$nickname}', bio='{$bio}' where email='{$email}';");

      $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';

    }else{
      
      // 校验文件的大小
      if ($avatar["size"] > 20*1024*1024) {
        $GLOBALS["message"]="图片文件过大";
        return;
      }

      // 校验文件的类型
      $allowed_types=array('image/png','image/jpg','image/jpeg');
      if (!in_array($avatar['type'], $allowed_types)) {
        $GLOBALS['error_message']='图片文件格式不正确';
        return;
      }

      // 把图片从临时目录移动到真实目录
      // 一般情况会将上传的文件重命名
      $targetPic="../uploads/" . uniqid() . "-" . $avatar["name"];
      if (!move_uploaded_file($avatar["tmp_name"], $targetPic)) {
        $GLOBALS["message"]="上传图片失败";
        return;
      }

      // 接收并保存
      $email=$_POST['email'];
      $slug=$_POST['slug'];
      $nickname=$_POST['nickname'];
      $bio=$_POST['bio'];

      // 更新数据到数据库
      $rows = myexecute("update users set avatar='{$targetPic}', slug='{$slug}', nickname='{$nickname}', bio='{$bio}' where email='{$email}';");

      $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
    }
  }

  if ($_SERVER['REQUEST_METHOD']==="POST") {
    edit_info();
  }

  // 查询分类数据
  $users=get_data_all("select * from users where id='{$id['id']}';");

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-个人资料</title>
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
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert <?php echo $message=='添加成功！' || $message=='更新成功！' ? ' alert-success' : ' alert-danger' ?>">
          <strong>提示:</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <?php foreach($users as $item): ?>
          <div class="form-group">
            <label class="col-sm-3 control-label">头像</label>
            <div class="col-sm-6">
              <label class="form-image">
                <input id="avatar" type="file" accept="image/*" name="avatar">
                <img id="icon" src="<?php echo $item['avatar'] ?>">
                <i class="mask fa fa-upload"></i>
              </label>
            </div>
          </div>
          <div class="form-group">
            <label for="email" class="col-sm-3 control-label">邮箱</label>
            <div class="col-sm-6">
              <input id="email" class="form-control" name="email" type="type" placeholder="邮箱" readonly value="<?php echo $item['email'] ?>">
              <p class="help-block">登录邮箱不允许修改</p>
            </div>
          </div>
          <div class="form-group">
            <label for="slug" class="col-sm-3 control-label">别名</label>
            <div class="col-sm-6">
              <input id="slug" class="form-control" name="slug" type="type" placeholder="slug" value="<?php echo $item['slug'] ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="nickname" class="col-sm-3 control-label">昵称</label>
            <div class="col-sm-6">
              <input id="nickname" class="form-control" name="nickname" type="type" placeholder="昵称" value="<?php echo $item['nickname'] ?>">
              <p class="help-block">限制在 2-16 个字符</p>
            </div>
          </div>
          <div class="form-group">
            <label for="bio" class="col-sm-3 control-label">简介</label>
            <div class="col-sm-6">
              <textarea id="bio" class="form-control" placeholder="介绍一下自己吧" name="bio" cols="30" rows="6"><?php echo $item['bio'] ?></textarea>
            </div>
          </div>
        <?php endforeach ?>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page='profile' ?>
  <?php include 'includes/aside.php'; ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    // 找到file元素
    var file = document.getElementById('avatar');
    // 找到img
    var icon = document.getElementById('icon');

    // 选择完图片后事件的事件，没有
    // 但有个类似的事件，叫值改变事件(change)

    file.onchange = function(){
      // 把图片对象转成临时url
      // icon.style.display='inline';
      icon.src =  URL.createObjectURL(this.files[0]);
    }
  </script>
</body>
</html>
