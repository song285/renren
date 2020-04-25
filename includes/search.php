<?php 
  function search(){
    $keys=$_POST['keys'];
    
    if (empty($keys)) {
      $GLOBALS['message']='搜索内容不能为空';
      return;
    }

    header("Location: ../songguo/search.php?con={$keys}");
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['keys'])) {
    search();
  }
  
 ?>
<!-- 搜索功能 -->
<div class="search">
  <!-- 有错误信息时展示 -->
  <?php if (isset($message)): ?>
    <strong><?php echo $message ?></strong>
  <?php endif ?>
  <form method="post" action="">
    <input type="text" name="keys" class="keys" placeholder="输入关键字">
    <input type="submit" class="btn" value="搜索">
  </form>
</div>