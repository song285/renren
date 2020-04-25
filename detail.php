<?php 
  require_once 'config.php';
  require_once 'functions.php';

  $current_user = getCurrentUser_fontend();

  // var_dump($current_user);

  $post_id=$_GET['id'];

  $newposts=get_data_all("
    SELECT a.id as postid,b.id,a.title,a.content,a.feature,a.created,a.views,a.likes,b.title AS cate,d.nickname AS author 
    FROM posts AS a,navmenus AS b,users AS d 
    WHERE a.user_id=d.id AND a.category_id=b.id AND a.status='published' AND a.id='{$post_id}'
    GROUP BY a.title,a.content,a.created,a.views,a.likes, cate,d.nickname,a.feature LIMIT 6");

  $totalcom=get_data_all("
    SELECT *
    FROM comments,user WHERE post_id='{$post_id}' AND comments.author=user.nickname AND comments.status='approved';");

  // 记录访问者的IP  实现阅读功能

  $ip = getIP();

  $dataip = myexecute("SELECT * FROM ips where ip='{$ip}' AND post_id='{$post_id}'");

  if($dataip <= 0){
    foreach ($newposts as $value) {
      $views = $value['views'];
    }
    myexecute("UPDATE posts SET views=views + 1 WHERE id='{$post_id}'");
    myexecute("INSERT INTO ips VALUES (null,'{$ip}','{$post_id}')");
  }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- 网站设置 -->
  <?php include 'includes/setting.php'; ?>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.css">
</head>
<body>
  <div class="wrapper">
    <!-- 导航功能 -->
    <?php include 'includes/navmenus.php'; ?>
    <div class="aside">
      <!-- 登录区块 -->
      <?php if ($current_user !== false): ?>
        <?php include 'includes/logoed.php'; ?>
      <?php else: ?>
        <?php include 'includes/login.php'; ?>
      <?php endif ?>
      <!-- 随机推荐 -->
      <?php include 'includes/randpost.php'; ?>
      <!-- 最新评论 -->
      <?php include 'includes/newcomment.php'; ?>
    </div>
    <div class="content">
      <!-- 文章详情 -->
      <div class="article">
        <?php foreach ($newposts as $item): ?>
          <div class="breadcrumb">
            <dl>
              <dt>当前位置：</dt>
              <dd><a href="../songguo/list.php?id=<?php echo $item['id'] ?>"><?php echo $item['cate'] ?></a></dd>
              <dd class="stitle"><?php echo $item['title'] ?></dd>
            </dl>
          </div>
          <div class="meta">
            <span><?php echo $item['author'] ?> 发布于 <?php echo mb_substr($item['created'], 0,10) ?></span>
            <span><a href="javascript:;" data-id="<?php echo $item['postid'] ?>">赞(<?php echo $item['likes'] ?>)</a></span>
            <span>阅读(<?php echo $item['views'] ?>)</span>
            <span>评论(<?php echo count($totalcom) ?>)</span>
          </div>
          <h2 class="title">
            <p><?php echo $item['title'] ?></p>
          </h2>
          <p class="myarticle"><?php echo $item['content'] ?></p>
        <?php endforeach ?>
        <!-- 最新评论 -->
        <div class="meta">
          <p class="show-comment">最新评论</p>
        </div>
        <!-- 评论区 -->
        <?php include 'includes/comment.php'; ?>
      </div>
    </div>
    <!-- <div class="footer">
      <p>闽江学院软件学院 宋健版权</p>
    </div> -->
  </div>
  <script src="assets/vendors/jquery/jquery.js"></script>
  <script src="assets/vendors/swipe/swipe.js"></script>
  <script src="assets/vendors/myjs/songguo.js"></script>
</body>
</html>