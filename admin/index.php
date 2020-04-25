<?php 

  require_once '../functions.php';
  // 判断用户是否已经登录
  getCurrentUser();

  // 获取站点内容统计数据
  $postsPublished=get_data_one("select count(1) as num from posts;");//文字总数
  $postsDrafted=get_data_one("select count(1) as num from posts where status ='drafted';");//文章草稿
  $categories=get_data_one("select count(1) as num from navmenus;");//分类
  $comments=get_data_one("select count(1) as num from comments;");//评论
  $comHeld=get_data_one("select count(1) as num from comments where status='held';");//待审核评论
  $userAct=get_data_one("select count(1) as num from user where status='activated';");//用户数量
  $userUnact=get_data_one("select count(1) as num from user where status='unactivated';");//待激活数量
  $userForb=get_data_one("select count(1) as num from user where status='forbidden';");//已封数量

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-首页</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
    <script src="../assets/vendors/echart/echarts.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="jumbotron text-center">
        <h1>松果看吧</h1>
        <p>信息创造价值</p>
        <p><a class="btn btn-primary btn-lg" href="post-add.php" role="button">写文章</a></p>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">站点内容统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $postsPublished['num'] ?></strong>篇文章（<strong><?php echo $postsDrafted['num'] ?></strong>篇草稿）</li>
              <li class="list-group-item"><strong><?php echo $categories['num'] ?></strong>个分类</li>
              <li class="list-group-item"><strong><?php echo $comments['num'] ?></strong>条评论（<strong><?php echo $comHeld['num'] ?></strong>条待审核）</li>
            </ul>
          </div>
        </div>
        <div class="col-md-4">
          <div id="chart" style="width: 400px;height:200px;"></div>
        </div>
        <div class="col-md-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">用户情况统计：</h3>
            </div>
            <ul class="list-group">
              <li class="list-group-item"><strong><?php echo $userAct['num'] ?></strong>个注册用户</li>
              <li class="list-group-item"><strong><?php echo $userUnact['num'] ?></strong>个待激活</li>
              <li class="list-group-item"><strong><?php echo $userForb['num'] ?></strong>已封禁</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page='index' ?>
  <?php include 'includes/aside.php'; ?>
  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script type="text/javascript">
    var myChart = echarts.init(document.getElementById('chart'));
    var option = {
        title : {
            text: '站点内容统计',
            x:'center'
        },
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['文章','栏目分类','评论']
        },
        series : [
            {
                name: '数据统计',
                type: 'pie',
                radius : '55%',
                center: ['50%', '60%'],
                data:[
                    {value:<?php echo $postsPublished['num'] ?>, name:'文章'},
                    {value:<?php echo $categories['num'] ?>, name:'栏目分类'},
                    {value:<?php echo $comments['num'] ?>, name:'评论'},
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    };
    myChart.setOption(option);
  </script>

</body>
</html>
