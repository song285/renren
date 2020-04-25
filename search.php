<?php 
  require_once 'config.php';
  require_once 'functions.php';

  $setting=get_data_all("SELECT * FROM setting WHERE id=1 LIMIT 1;");

  $current_user = getCurrentUser_fontend();

  $keys=$_GET['con'];

  $searchpost=get_data_all("
    SELECT a.id,b.title as category,a.title,a.content,a.feature,a.created,a.views,a.likes,b.title AS cate,count(c.id) AS comment,d.nickname AS author 
    FROM posts AS a,navmenus AS b,comments AS c,users AS d 
    WHERE a.id=c.post_id AND a.user_id=d.id AND a.category_id=b.id AND a.status='published' AND a.title LIKE '%{$keys}%'
    GROUP BY a.id,a.title,a.content,a.created,a.views,a.likes,cate,d.nickname,a.feature");
  
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
      <!-- 搜索结果 -->
      <div class="panel new">
        <?php foreach ($searchpost as $item): ?>
          <h3><?php echo $item['category']; ?></h3>
          <div class="entry">
            <div class="head">
              <span class="sort"><?php echo $item['category']; ?></span>
              <a href="../songguo/detail.php?id=<?php echo $item['id'] ?>"><?php echo $item['title']; ?></a>
            </div>
            <div class="main">
              <p class="info"><?php echo $item['author']; ?> 发表于 <?php echo mb_substr($item['created'],0,10) ?></p>
              <p class="brief"><?php echo $item['content']; ?></p>
              <p class="extra">
                <span class="reading">阅读(<?php echo $item['views']; ?>)</span>
                <span class="comment">评论(<?php echo $item['comment']; ?>)</span>
                <a href="javascript:;" class="like" data-id="<?php echo $item['id'] ?>">
                  <span>赞(<?php echo $item['likes']; ?>)</span>
                </a>
              </p>
              <a href="javascript:;" class="thumb">
                <img src="<?php echo substr($item['feature'],3); ?>" alt="">
              </a>
            </div>
        <?php endforeach ?>
        </div>
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