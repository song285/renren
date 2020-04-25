<?php 
  require_once 'config.php';
  require_once 'functions.php';

  $current_user = getCurrentUser_fontend();

  $slides=get_data_all("SELECT * FROM slides;");
  
  $hotposts=get_data_all("
    SELECT a.id,a.title,a.content,a.feature,a.created,a.views,a.likes,b.title AS cate,count(c.id) AS comment,d.nickname AS author 
    FROM posts AS a,navmenus AS b,comments AS c,users AS d 
    WHERE a.id=c.post_id AND a.user_id=d.id AND a.category_id=b.id AND a.status='published' 
    GROUP BY a.id,a.title,a.content,a.created,a.views,a.likes, cate,d.nickname,a.feature 
    ORDER BY views DESC,likes DESC LIMIT 5;");

  $newposts=get_data_all("
    SELECT a.id,a.title,a.content,a.feature,a.created,a.views,a.likes,b.title AS cate,d.nickname AS author
    FROM posts AS a,navmenus AS b,users AS d 
    WHERE a.user_id=d.id AND a.category_id=b.id AND a.status='published'
    GROUP BY a.id,a.title,a.content,a.created,a.views,a.likes,cate,d.nickname,a.feature ORDER BY a.created DESC LIMIT 5");

  foreach ($newposts as $value) {
    $likes = $value['likes'];
    $id = $value['id'];
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 删除登录标识
    unset($_SESSION['current_fontend_user']);
    header('Location: ../songguo/index.php');
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
      <!-- 轮播图 -->
      <div class="swipe">
        <ul class="swipe-wrapper">
          <?php foreach ($slides as $item): ?>
            <li>
              <a href="javascript:;">
                <img src="<?php echo substr($item['pictrue'], 3) ?>">
                <span><?php echo $item['content'] ?></span>
              </a>
            </li>
          <?php endforeach ?>
        </ul>
        <p class="cursor"><span class="active"></span><?php for ($i=count($slides)-1; $i > 0 ; $i--) { 
          echo '<span></span>';
        } ?></p>
        <a href="javascript:;" class="arrow prev"><i class="fa fa-chevron-left"></i></a>
        <a href="javascript:;" class="arrow next"><i class="fa fa-chevron-right"></i></a>
      </div>
      <!-- 热门排行 -->
      <div class="panel top">
        <h3>热门排行</h3>
        <ol>
          <?php foreach ($hotposts as $key => $value): ?>
            <li>
              <i><?php echo $key+1 ?></i>
              <a href="../songguo/detail.php?id=<?php echo $value['id'] ?>" class="title"><?php echo $value['title'] ?></a>
              <a href="javascript:;" class="like" data-id="<?php echo $value['id'] ?>">赞(<?php echo $value['likes'] ?>)</a>
              <span>阅读 (<?php echo $value['views'] ?>)</span>
            </li>
          <?php endforeach ?>
        </ol>
      </div>
      <!-- 最新发布 -->
      <div class="panel new">
        <h3>最新发布</h3>
        <?php foreach ($newposts as $item): ?>
          <div class="entry">
            <div class="head">
              <span class="sort"><?php echo $item['cate']; ?></span>
              <a href="../songguo/detail.php?id=<?php echo $item['id'] ?>"><?php echo $item['title']; ?></a>
            </div>
            <div class="main">
              <p class="info"><?php echo $item['author']; ?> 发表于 <?php echo mb_substr($item['created'],0,10) ?></p>
              <p class="brief"><?php echo $item['content']; ?></p>
              <p class="extra">
                <span class="reading">阅读(<?php echo $item['views']; ?>)</span>
                <!-- <span class="comment">评论(<?php echo $item['comment']; ?>)</span> -->
                <a href="javascript:;" class="like" data-id="<?php echo $item['id'] ?>">
                  <span>赞(<?php echo $item['likes']; ?>)</span>
                </a>
              </p>
              <a href="javascript:;" class="thumb">
                <img src="<?php echo $item['feature']; ?>" alt="">
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
  <script>
    var swiper = Swipe(document.querySelector('.swipe'), {
      auto: 3000,
      transitionEnd: function (index) {
        // index++;
        $('.cursor span').eq(index).addClass('active').siblings('.active').removeClass('active');
      }
    });

    // 上/下一张
    $('.swipe .arrow').on('click', function () {
      var _this = $(this);

      if(_this.is('.prev')) {
        swiper.prev();
      } else if(_this.is('.next')) {
        swiper.next();
      }
    })

    /*点赞功能*/
    // $('a[data-id]').on('click',function(){
    //   var _this =$(this);
    //   $.get('includes/zanapi.php?id='+_this.attr('data-id'),null,function(data){
    //     if (data !== 'false') {
    //       _this.html('赞('+data+')');
    //       _this.css('color','#ff5e52');
    //     }
    //   })
    // })
  </script>
</body>
</html>