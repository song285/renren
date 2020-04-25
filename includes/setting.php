<?php 
	$setting=get_data_all("SELECT * FROM setting WHERE id=1 LIMIT 1;");

 ?>
<?php foreach ($setting as $item): ?>
    <title><?php echo $item['name']; ?></title>
    <link rel="icon" href="<?php echo substr($item['icon'], 3) ?>" type="image/*">
    <meta name="Description" content="<?php echo $item['description']; ?>" />
    <meta name="Keywords" content="<?php echo $item['keyword']; ?>" />
 <?php endforeach ?>