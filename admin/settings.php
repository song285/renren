<?php 
  require_once '../functions.php';
  getCurrentUser();

  function edit_info(){
    global $current_edit;

    // 处理图片文件
    // ==================================
    // 校验文件
    if (!isset($_FILES["logo"])) {
      $GLOBALS["message"]="请正确操作";
      return;
    }
    
    $logo=$_FILES["logo"];

    // 判断用户是否选择了文件
    if ($logo["error"]!==UPLOAD_ERR_OK) {
      // $GLOBALS["message"]="请选择图片文件";
      // return;
      // 接收数据
      $site_name=empty($_POST['site_name']) ? $current_edit['site_name'] : $_POST['site_name'];
      // 同步数据
      $current_edit['site_name']=$site_name;

      $site_description=empty($_POST['site_description']) ? $current_edit['site_description'] : $_POST['site_description'];
      // 同步数据
      $current_edit['site_description']=$site_description;

      $site_keywords=empty($_POST['site_keywords']) ? $current_edit['site_keywords'] : $_POST['site_keywords'];
      // 同步数据
      $current_edit['site_keywords']=$site_keywords;

      // 更新数据到数据库
      $rows = myexecute("update setting set name='{$site_name}', description='{$site_description}', keyword='{$site_keywords}';");

      $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
    }else{

      // 校验文件的大小
      if ($logo["size"]>20*1024*1024) {
        $GLOBALS["message"]="图片文件过大";
        return;
      }

      // 校验文件的类型
      $allowed_types=array('image/png','image/jpg','image/jpeg');
      if (!in_array($logo['type'], $allowed_types)) {
        $GLOBALS['error_message']='图片文件格式不正确';
        return;
      }

      // 把图片从临时目录移动到真实目录
      // 一般情况会将上传的文件重命名
      $targetPic="../uploads/" . uniqid() . "-" . $logo["name"];
      if (!move_uploaded_file($logo["tmp_name"], $targetPic)) {
        $GLOBALS["message"]="上传图片失败";
        return;
      }

      // 接收并保存
      $site_name=$_POST['site_name'];
      $site_description=$_POST['site_description'];
      $site_keywords=$_POST['site_keywords'];

      // 更新数据到数据库
      $rows=myexecute("update setting set icon='{$targetPic}', name='{$site_name}', description='{$site_description}', keyword='{$site_keywords}';");

    $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';
    }
  }

  if ($_SERVER['REQUEST_METHOD']==="POST") {
    edit_info();
  }

  // 查询分类数据
  $setting=get_data_all("select * from setting;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-网站设置</title>
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
        <h1>网站设置</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert <?php echo $message=='添加成功！' || $message=='更新成功！' ? ' alert-success' : ' alert-danger' ?>">
          <strong>提示:</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <?php foreach($setting as $item): ?>
        <div class="form-group">
          <label for="site_logo" class="col-sm-2 control-label">网站图标</label>
          <div class="col-sm-6">
            <input id="site_logo" name="site_logo" type="hidden">
            <label class="form-image">
              <input id="logo" type="file" accept="image/*" name="logo">
              <img src="<?php echo $item['icon'] ?>" id="icon">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="site_name" class="col-sm-2 control-label">站点名称</label>
          <div class="col-sm-6">
            <input id="site_name" name="site_name" class="form-control" type="type" placeholder="站点名称" value="<?php echo $item['name'] ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="site_description" class="col-sm-2 control-label">站点描述</label>
          <div class="col-sm-6">
            <textarea id="site_description" name="site_description" class="form-control" placeholder="站点描述" cols="30" rows="6"><?php echo $item['description'] ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="site_keywords" class="col-sm-2 control-label">站点关键词</label>
          <div class="col-sm-6">
            <input id="site_keywords" name="site_keywords" class="form-control" type="type" placeholder="站点关键词" value="<?php echo $item['keyword'] ?>">
          </div>
        </div>
        <?php endforeach ?>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-6">
            <button type="submit" class="btn btn-primary">保存设置</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page='settings' ?>
  <?php include 'includes/aside.php'; ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    // 找到file元素
    var file = document.getElementById('logo');
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
