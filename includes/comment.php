<?php 

  $current_user = getCurrentUser_fontend();

  // var_dump($current_user);

  function add_comment(){
    global $current_user;

    if (!$current_user) {
      $GLOBALS['message'] = '您还没有登录，不能评论哟！';
      return;
    }

    if (empty($_POST['comment'])) {
      $GLOBALS['message'] = '少侠请留下一点东西吧！';
      return;
    }

    // 设置时区
    date_default_timezone_set('PRC');

    // 获取当前时间
    $created=date('y-m-d H:i:s',time());

    // 获取了内容
    $user_id = $current_user['id'];
    $comment = $_POST['comment'];
    $author = $current_user['nickname'];
    $phone = $current_user['phone'];
    $post_id = $_GET['id'];
    $status = 'approved';

    // 插入到数据库
    $row = myexecute("insert into comments values (null,'{$author}','{$phone}','{$created}','{$comment}','{$status}','{$post_id}',null)");

    if ($row <= 0) {
      $GLOBALS['message'] = '添加失败！';
    }else{
      $GLOBALS['message'] = '添加成功！';
    }
  }

  if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_GET['id'])) {
    add_comment();
  }

 ?>
<div class="comment-area">
  <form method="post" action="">
    <?php if (isset($message)): ?>
      <!-- 有错误信息时展示 -->
        <p><?php echo $message ?></p>
    <?php endif ?>
    <div class="comment-action-wrap">
      <img class="<?php echo $current_user !== false ? 'comment-currentUser-image' : 'comment-hidden' ?>" src="<?php echo substr($current_user['avatar'], 3); ?>">
      <div class="mini-editor" style="display: inline-block;">
        <textarea placeholder="快来评论一下吧" name="comment"></textarea>
      </div>
      <div class="comment-action-bar" style="display: inline-block;">
        <input class="comment-submit" type="submit" value="发表"></input>
      </div>
    </div>
  </form>
  <ul>
    <?php foreach ($totalcom as $item): ?>
      <li>
        <div class="userInfo">
          <a href="javascript:;">
            <img class="comment-currentUser-image" src="<?php echo $item['avatar']; ?>">
          </a>
          <div class="authorInfo">
            <a href="javascript:;" style="color: #000"><?php echo $item['author'] ?> </a>发表于 
            <p style="display: inline-block;"><?php echo mb_substr($item['created'], 0 , 10) ?></p>
          </div>
        </div>
        <div class="comment-content">
          <p style="line-height: 20px"><?php echo $item['content'] ?></p>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
  </div>
</div>