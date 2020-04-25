<?php 
  require_once '../functions.php';

  getCurrentUser();

  function add_category(){
    if (empty($_POST['name']) || empty($_POST['slug'])) {
      $GLOBALS['message']='请完整填写表单';
      return;
    }

    // 接收并保存
    $name=$_POST['name'];
    $slug=$_POST['slug'];


    $rows=myexecute("insert into navmenus values (null,'{$name}','{$slug}',null)");

    $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';

  }

  function edit_category(){
    global $current_edit;

    // 接收数据
    $id=$current_edit['id'];
    $name=empty($_POST['name']) ? $current_edit['name'] : $_POST['name'];
    // 同步数据
    $current_edit['name']=$name;

    $slug=empty($_POST['slug']) ? $current_edit['slug'] : $_POST['slug'];
    // 同步数据
    $current_edit['slug']=$slug;

    // 更新数据到数据库
    $rows = myexecute("update navmenus set slug='{$slug}', title='{$name}' where id='{$id}';");

    $GLOBALS['message'] = $rows <= 0 ? '更新失败！' : '更新成功！';
  }

  // 判断当前是什么操作 编辑还是添加
  if (empty($_GET['id'])) {
    // 添加
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      add_category();
    }
  }else{
    // 编辑
    // 获取需要修改的信息
    $current_edit=get_data_one("select * from navmenus where id=" . $_GET['id']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      edit_category();
    }
  }

  // 查询分类数据
  $categories=get_data_all("select * from navmenus;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-分类目录</title>
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
        <h1>分类目录</h1>
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
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit['title']; ?>">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit['slug']; ?>">
              </div>
              <div class="form-group">
                <button class="btn btn-primary" type="submit">保存</button>
                <a class="btn btn-default" href="../admin/categories.php">取消</a>
              </div>
            </form>
          <?php else: ?>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
              <h2>添加新分类目录</h2>
              <div class="form-group">
                <label for="name">名称</label>
                <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
              </div>
              <div class="form-group">
                <label for="slug">别名</label>
                <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
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
            <a id="btn_delete" class="btn btn-danger btn-sm" href="../admin/category-dele.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>别名</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item): ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>" data-slug="<?php echo $item['slug'];?>"></td>
                  <td><?php echo $item['title'];?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td class="text-center">
                    <a href="../admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                    <a href="../admin/category-dele.php?id=<?php echo $item['id']; ?>&slug=<?php echo $item['slug'] ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='categories' ?>
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
