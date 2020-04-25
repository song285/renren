<?php 

	$menus=get_data_all("SELECT * FROM navmenus;");
 ?>
<!-- 导航功能 -->
<div class="header">
  <h1 class="logo"><a href="index.php"><img src="assets/img/logo.png" alt="松果看吧"></a></h1>
  <ul class="nav">
    <?php foreach ($menus as $item): ?>  
      <li><a href="../songguo/list.php?id=<?php echo $item['id'] ?>"><?php echo $item['title'] ?></a></li>
    <?php endforeach ?>
  </ul>
  <!-- 搜索功能 -->
  <?php include 'includes/search.php'; ?>
</div>