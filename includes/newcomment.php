<?php 
  $comments=get_data_all("
    SELECT a.id, b.avatar,b.nickname,a.content,a.created 
    FROM comments as a,user as b 
    WHERE a.author=b.nickname ORDER BY created DESC LIMIT 6;");
  
 ?>
<!-- 最新评论 -->
<div class="widgets">
  <h4>最新评论</h4>
  <ul class="body discuz">
    <?php foreach ($comments as $item): ?>
      <li>
        <a href="javascript:;">
          <div class="avatar">
            <img src="<?php echo $item['avatar']; ?>" alt="">
          </div>
          <div class="txt">
            <p>
              <span><?php echo $item['nickname'] ?></span><?php echo mb_substr($item['created'],0,10) ?> 说:
            </p>
            <p class="cont"><?php echo $item['content'] ?></p>
          </div>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>