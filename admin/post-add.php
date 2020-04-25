<?php 
  require_once '../functions.php';

  getCurrentUser();

  $current_user=getCurrentUser();

  $data=get_data_all("select * from navmenus");

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 数据校验
    // ‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐
    if (empty($_POST['slug']) || empty($_POST['title']) || empty($_POST['content']) || empty($_POST['status']) || empty($_POST['category'])) {
      // 缺少必要数据
      $message = '请完整填写所有内容';
    } else if (myexecute("select count(1) as count from posts where slug = '{$_POST['slug']}'")['count'] > 0) {
     // slug 重复
      $message = '别名已经存在，请修改别名';
    } else {
      // 数据合法
      // 接收数据
      // ‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐
      $slug = $_POST['slug'];
      $title = $_POST['title'];
      $feature = ''; // 图片稍后再考虑
      $created = date('y-m-d h:i:s',time());
      $content = $_POST['content'];
      $status = $_POST['status']; 
      $user_id = $current_user['id'];// 作者 ID 可以从当前登录用户信息中获取
      $category_id = $_POST['category'];
      
      // 接收文件并保存
      // ‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐‐
      // 如果选择了文件 $_FILES['feature']['error'] => 0
      if (empty($_FILES['feature']['error'])) {
        // PHP 在会自动接收客户端上传的文件到一个临时的目录
        $temp_file = $_FILES['feature']['tmp_name'];
        // 我们只需要把文件保存到我们指定上传目录
        $target_file = '../uploads/' . $_FILES['feature']['name'];

        if (move_uploaded_file($temp_file, $target_file)) {
          $image_file = '../uploads/' . $_FILES['feature']['name'];
          // var_dump($image_file);
          $feature = isset($image_file) ? $image_file : '';
        }
      }

      // 拼接查询语句
      $sql = sprintf("INSERT INTO posts VALUES (null, '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', %d, %d)",$slug,$title,$feature,$created,$content,$status,$user_id,$category_id);
      // 执行 SQL 保存数据
      if (myexecute($sql) > 0) {
        // 保存成功
        header('Location: ../admin/posts.php');
        exit;
      } else {
        // 保存失败，请重试
        $message = '保存失败，请重试';
      }
    }
  }
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-写文章</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert alert-danger">
          <strong>错误！</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='post' enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo
isset($_POST['title']) ? $_POST['title'] : '' ?>" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">正文</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($data as $key): ?>
                <option value="<?php echo $key['id'] ?>"><?php echo $key['title'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <!-- <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="date">
          </div> -->
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page='post-add' ?>
  <?php include 'includes/aside.php'; ?>

  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
