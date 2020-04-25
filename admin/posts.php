<?php 
  require_once '../functions.php';

  // 当前登录用户
  getCurrentUser();

  //每页要展示的条数
  $size = 8;

  // 接收参数并筛选
  $where = '1=1';
  $search='';

  // 获取所有的分类到列表框
  $categories = get_data_all("select * from navmenus");
  $author = get_data_all("select * from users");

  // 分类筛选
  if (isset($_GET['category']) && $_GET['category'] !== 'all') {
    $where .= ' and posts.category_id = ' . $_GET['category'];
    $search .= '&category=' . $_GET['category'];
  }
  // 状态筛选
  if (isset($_GET['status']) && $_GET['status'] !== 'all') {
    $where .= " and posts.status = '{$_GET['status']}'";
    $search .= '&status=' . $_GET['status'];
  }
  // 作者筛选
  if (isset($_GET['authors']) && $_GET['authors'] !== 'all') {
    $where .= " and users.id = '{$_GET['authors']}'";
    $search .= '&authors=' . $_GET['authors'];
  }


  // 获取页码
  $total_count = (int)get_data_one("select 
    count(1) as count
    from posts 
    inner join navmenus on posts.category_id=navmenus.id 
    inner join users on posts.user_id=users.id
    where {$where};")['count'];  //1000
  $total_pages = (int)ceil($total_count / $size);//125

  // 处理展示数据的
  if (empty($_GET['page']) || $_GET['page'] < 1) {
    $page = 1;
  }else if($_GET['page'] > $total_pages){
    $page = $total_pages;
  }else{
    $page = (int)$_GET['page'];
  }


  // 显示的页码数量
  $visibles = 5;

  // 计算最大和最小展示的页码   
  $begin = $page - ($visibles - 1) / 2;  //4-2=2
  $end = $begin + $visibles - 1;  //2+5-1=6

  // 处理是否符合逻辑
  $begin = $begin < 1 ? 1 : $begin; //2   
  $end = $begin + $visibles - 1;  //2+5-1=6
  $end = $end > $total_pages ? $total_pages : $end;
  $begin = $begin < 1 ? 1 : $begin;


  // 计算出越过多少条
  $offset = ($page - 1) * $size;

  // 显示所有文章
  $posts=get_data_all("SELECT 
    posts.id,
    posts.title,
    users.nickname AS user_name,
    navmenus.title AS category_name,
    posts.created,
    posts.status 
    FROM posts 
    INNER JOIN navmenus ON posts.category_id=navmenus.id 
    INNER JOIN users ON posts.user_id=users.id
    WHERE {$where}
    ORDER BY posts.created DESC
    LIMIT {$offset},{$size};");


  // 转换文章状态
  function show_status($status){
    $dict=array(
      'published' => '已发布',
      'drafted' => '草稿',
      'trashed' => '回收站'
    );

    return isset($dict[$status]) ? $dict[$status] : "未知";
  }

  // 转换文章的日期  年月日
  function show_date($created){
    $timestamp=strtotime($created);
    return date('Y年m月d日<b\r>H:i:s',$timestamp);
  }

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-所有文章</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <div class="page-action">
        <a id="btn_delete" class="btn btn-danger btn-sm" href="../admin/posts-dele.php" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
          <select name="authors" class="form-control input-sm">
            <option value="all">所有用户</option>
            <?php foreach ($author as $item): ?>
              <option value="<?php echo $item['id'] ?>"<?php echo isset($_GET['authors']) && $_GET['authors'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['nickname'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id'] ?>"<?php echo isset($_GET['category']) && $_GET['category'] == $item['id'] ? ' selected' : '' ?>><?php echo $item['title'] ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>回收站</option>
            <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>草稿</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li style="<?php echo isset($_GET['page']) && $_GET['page'] > 3 ? 'display: inline' : 'display: none'; ?>"><a href="?page=1<?php echo  $search ?>">首页</a></li>
          <li style="display: inline"><a href="?page=<?php echo $_GET['page'] < 1 ? '1' : $_GET['page']-1 ?>">上一页</a></li>
          <?php for ($i=$begin; $i <= $end; $i++):?>
            <li <?php echo $i === $page ? ' class=active' : '' ?>><a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <li style="<?php echo isset($_GET['page']) && $_GET['page'] > $total_pages - 2 ? 'display: none' : 'display: inline'; ?>"><a href="?page=<?php echo empty($_GET['page']) ? '2' : $_GET['page']+1 ?>">下一页</a></li>
          <li style="<?php echo isset($_GET['page']) && $_GET['page'] > $total_pages - 2 ? 'display: none' : 'display: inline'; ?>"><a href="?page=<?php echo $total_pages . $search ?>">末页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th class="text-center">作者</th>
            <th class="text-center">分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
            <tr>
              <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'];?>" data-slug="<?php echo $item['slug'];?>"></td>
              <td><?php echo $item['title'];?></td>
              <td class="text-center"><?php echo $item['user_name'];?></td>
              <td class="text-center"><?php echo $item['category_name'];?></td>
              <td class="text-center"><?php echo show_date($item['created']);?></td>
              <td class="text-center"><?php echo show_status($item['status']);?></td>
              <td class="text-center">
                <!-- <a href="../admin/post-add.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a> -->
                <a href="../admin/posts-dele.php?id=<?php echo $item['id'] ?>" class="btn btn-danger btn-xs">删除</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php $current_page='posts';?>
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
