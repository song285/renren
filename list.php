<?php 
  require_once 'config.php';
  require_once 'functions.php';

  $current_user = getCurrentUser_fontend();

  $category_id = $_GET['id'];

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
      <!-- 文章列表 -->
      <div class="panel new" data-id="<?php echo $category_id ?>">
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
    // 使用数据懒加载来实现下滑获取数据
    var page = 0; 
    var total_page=0;
    loadingFunction(page);
    $(window).scroll(function () {
      var scrollTop = $(this).scrollTop();
      var scrollHeight = $(document).height();
      var windowHeight = $(this).height();
      if(page<=total_page){
        if (scrollTop + windowHeight == scrollHeight) {
       　　//此处是滚动条到底部时候触发的事件，在这里写要加载的数据，或者是拉动滚动条的操作
    　　  loadingFunction(page)
          page+=4;
        }
      }else{}
    });

  function loadingFunction(page){
    var dta='';
    $.getJSON('includes/listapi.php?page='+page+'&id='+$('.panel').attr('data-id'),function(data){
      total_page = data.total;
      $.each(data.main,function(h,s){
        dta += "\
        <h3>"+s['category']+"</h3>\
        <div class='entry'>\
          <div class='head'>\
            <span class='sort'>"+s['category']+"</span>\
            <a href='../songguo/detail.php?id="+s['id']+"'>"+s['title']+"</a>\
          </div>\
          <div class='main'>\
            <p class='info'>"+s['author']+" 发表于 "+s['created'].substr(0,10)+"</p>\
            <p class='brief'>"+s['content']+"</p>\
            <p class='extra'><span class='reading'>阅读("+s['views']+")</span>\
              <a href='javascript:;' class='like' data-id='"+s['id']+"'>\
                <span>赞("+s['likes']+")</span>\
              </a>\
            </p>\
            <a href='javascript:;' class='thumb'>\
              <img src='"+s['feature']+"'>\
            </a>\
          </div>\
        </div>";
      })
      $('.panel').html(dta);
    })
  }
  </script>
</body>
</html>