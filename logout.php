<?php 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 删除登录标识
    unset($_SESSION['current_fontend_user']);
    header('Location: ../songguo/index.php');
}
