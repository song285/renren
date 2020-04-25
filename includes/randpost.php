<?php 
  
  $posts=get_data_all("
    SELECT a.id,a.title,a.feature,a.views
    FROM posts AS a,navmenus AS b,users AS d 
    WHERE a.user_id=d.id AND a.category_id=b.id AND a.status='published' 
    GROUP BY a.id,a.title,a.feature,a.views");

  // 随机推荐
  $randnum=unique_rand(0, count($posts)-1, 5);

  for ($i=0; $i < 5; $i++) { 
    $randposts[$i] = $posts[$randnum[$i]];
  }

  
 ?>
<div class="widgets">
  <h4>随机推荐</h4>
  <ul class="body random">
    <?php foreach ($randposts as $item): ?>
      <li>
        <a href="../songguo/detail.php?id=<?php echo $item['id'] ?>">
          <p class="title"><?php echo $item['title'] ?></p>
          <p class="reading">阅读(<?php echo $item['views'] ?>)</p>
          <div class="pic">
            <img src="<?php echo $item['feature'] ?>" alt="">
          </div>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>