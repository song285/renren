<?php 
  require_once '../functions.php';

  getCurrentUser();

  function add_users(){
    if (empty($_POST['title']) || empty($_POST['link'])) {
      $GLOBALS['message']='请完整填写表单';
      return;
    }

    // 接收并保存
    $title=$_POST['title'];
    $link=$_POST['link'];

    $rows=myexecute("insert into navmenus values (null,'{$title}','{$link}')");

    $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';

  }

  function edit_user(){
    global $current_edit;

    // 接收数据
    $id=$current_edit['id'];
    $title=empty($_POST['title']) ? $current_edit['title'] : $_POST['title'];
    // 同步数据
    $current_edit['title']=$title;

    $slug=empty($_POST['slug']) ? $current_edit['slug'] : $_POST['slug'];
    // 同步数据
    $current_edit['slug']=$slug;

    $link=empty($_POST['link']) ? $current_edit['link'] : $_POST['link'];
    // 同步数据
    $current_edit['link']=$link;

    // 更新数据到数据库
    $rows = myexecute("update navmenus set title='{$title}',slug='{$slug}', link='{$link}' where id='{$id}';");

    $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
  }

  // 判断当前是什么操作 编辑还是添加
  if (empty($_GET['id'])) {
    // 添加
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      add_users();
    }
  }else{
    // 编辑
    // 获取需要修改的信息
    $current_edit=get_data_one("select * from navmenus where id=" . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_user();
    }
  }

  // 查询分类数据
  $menus=get_data_all("select * from navmenus;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-导航菜单</title>
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
        <h1>导航菜单</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert <?php echo $message=='添加成功！' || $message=='更新成功！' ? ' alert-success' : ' alert-danger' ?>">
          <strong>提示:</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <?php if (isset($current_edit)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $current_edit['id'] ?>" method="post">
              <h2>正在编辑【<?php echo $current_edit['title'] ?>】</h2>
              <div class="form-group">
                <label for="title">标题</label>
                <input id="title" class="form-control" name="title" type="text" placeholder="标题" value="<?php echo $current_edit['title']; ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="别名" value="<?php echo $current_edit['slug']; ?>">
              </div>
              <div class="form-group">
                <label for="link">链接</label>
                <input id="link" class="form-control" name="link" type="text" placeholder="链接" value="<?php echo $current_edit['link']; ?>">
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存</button>
                <a class="btn btn-default" href="../admin/nav-menus.php">取消</a>
              </div>
            </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
              <h2>添加新导航链接</h2>
              <div class="form-group">
                <label for="title">标题</label>
                <input id="title" class="form-control" name="title" type="text" placeholder="标题">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="别名">
              </div>
              <div class="form-group">
                <label for="link">链接</label>
                <input id="link" class="form-control" name="link" type="text" placeholder="链接">
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">添加</button>
              </div>
            </form>
          <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="../admin/menus-dele.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>标题</th>
                <th>链接</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($menus as $item):?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>"></td>
                  <td><?php echo $item['title'] ?></td>
                  <td><?php echo $item['slug'] ?></td>
                  <td><?php echo $item['link'] ?></td>
                  <td class="text-center">
                    <a href="../admin/nav-menus.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                    <a href="../admin/menus-dele.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='nav-menus' ?>
  <?php include 'includes/aside.php'; ?>
  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
    $(function($){
      var checkedboxs=$("tbody input");
      var btnDelete=$("#btn_delete");

      var allcheckeds=[];
      checkedboxs.on('change',function(){
        var id=$(this).data('id');
        var slug=$(this).data('slug');
        if ($(this).prop('checked')) {
          if (allcheckeds.includes(id)) {
            return;
          }else{
            allcheckeds.push(id);
          }
        }else{
          allcheckeds.splice(allcheckeds.indexOf(id),1);
        }
        allcheckeds.length ? btnDelete.fadeIn() : btnDelete.fadeOut();
        btnDelete.prop('search','?id=' + allcheckeds);

      })

      // 全选和全不选
      $('thead input').on('change', function () {
        // 1. 获取当前选中状态
        var checked = $(this).prop('checked');
        // 2. 设置给标体中的每一个
        checkedboxs.prop('checked', checked).trigger('change');
        // $tbodyCheckboxs.attr('checked', checked).trigger('change')
      })
      
    })
  </script>
</body>
</html>
