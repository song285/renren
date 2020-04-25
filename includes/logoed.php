<?php 
	require_once 'config.php';
  require_once 'functions.php';

  $current_user = getCurrentUser_fontend();

  $user_info = get_data_all("
  	SELECT * FROM user WHERE id='{$current_user['id']}'");
?>
<div class="widgets">
  <div class="slink">
    <div class="logoed-box">
    	<?php foreach ($user_info as $item): ?>
    		<div>
	    		<img class="user-avatar" src="<?php echo $item['avatar']; ?>">
	    		<p class="name" style="padding: 5px 0;"><?php echo $item['nickname'] ?></p>
	    		<p class="logout"><a href="../songguo/index.php?action=logout">退出</a></p>
	    	</div>
    	<?php endforeach ?>
    </div>
  </div>
</div>