<?php 
  require_once '../functions.php';

  getCurrentUser();

  function add_slides(){
    if (empty($_POST['text']) || empty($_POST['link'])) {
      $GLOBALS['message']='请完整填写表单';
      return;
    }

    // 处理图片文件
    // ==================================
    // 校验文件
    if (!isset($_FILES["image"])) {
      $GLOBALS["message"]="请正确操作";
      return;
    }

    // 接收并保存
    // $image=$_POST['image'];
    $text=$_POST['text'];
    $link=$_POST['link'];
    
    $image=$_FILES["image"];

    // 判断用户是否选择了文件
    if ($image["error"]!==UPLOAD_ERR_OK) {
      $GLOBALS["message"]="请选择图片文件";
      return;
    }

    // 校验文件的大小
    if ($image["size"]>20*1024*1024) {
      $GLOBALS["message"]="图片文件过大";
      return;
    }

    // 校验文件的类型
    $allowed_types=array('image/png','image/jpg','image/jpeg');
    if (!in_array($image['type'], $allowed_types)) {
      $GLOBALS['error_message']='图片文件格式不正确';
      return;
    }

    // 把图片从临时目录移动到真实目录
    // 一般情况会将上传的文件重命名
    $targetPic="../uploads/" . uniqid() . "-" . $image["name"];
    if (!move_uploaded_file($image["tmp_name"], $targetPic)) {
      $GLOBALS["message"]="上传图片失败";
      return;
    }

    $rows=myexecute("insert into slides values (null,'{$targetPic}','{$text}','{$link}');");

    $GLOBALS['message'] = $rows <= 0 ? '添加失败！' : '添加成功！';

  }

  if ($_SERVER['REQUEST_METHOD']==="POST") {
    add_slides();
  }

  // 查询分类数据
  $slides=get_data_all("select * from slides;");
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-图片轮播</title>
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
        <h1>图片轮播</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
        <div class="alert <?php echo $message=='添加成功！' || $message=='更新成功！' ? ' alert-success' : ' alert-danger' ?>">
          <strong>提示:</strong><?php echo $message ?>
        </div>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <h2>添加新轮播内容</h2>
            <div class="form-group">
              <label for="image">图片</label>
              <!-- show when image chose -->
              <img class="help-block thumbnail" style="display: none;" src="" id="pic">
              <input id="image" class="form-control" name="image" type="file" accept="image/*">
            </div>
            <div class="form-group">
              <label for="text">文本</label>
              <input id="text" class="form-control" name="text" type="text" placeholder="文本">
            </div>
            <div class="form-group">
              <label for="link">链接</label>
              <input id="link" class="form-control" name="link" type="text" placeholder="链接">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete" class="btn btn-danger btn-sm" href="../admin/slides-dele.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center">图片</th>
                <th>文本</th>
                <th>链接</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($slides as $item):?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>"></td>
                  <td class="text-center"><img class="slide" src="<?php echo $item['pictrue'] ?>"></td>
                  <td><?php echo $item['content'] ?></td>
                  <td><?php echo $item['link'] ?></td>
                  <td class="text-center">
                    <a href="../admin/slides-dele.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php $current_page='slides' ?>
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

    // 找到file元素
    var file = document.getElementById('image');
    // 找到img
    var pic = document.getElementById('pic');

    // 选择完图片后事件的事件，没有
    // 但有个类似的事件，叫值改变事件(change)
    file.onchange = function(){
        // 把图片对象转成临时url
        pic.style.display='inline';
        pic.src =  URL.createObjectURL(this.files[0]);
    }
  </script>
</body>
</html>
