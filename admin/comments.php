<?php 
  require_once '../functions.php';
  getCurrentUser();
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>后台管理-评论</title>
  <link rel="stylesheet" href="../assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="../assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
  <script src="../assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'includes/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th class="text-center">作者</th>
            <th width="560">评论</th>
            <th class="text-center">文章</th>
            <th class="text-center">提交时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <?php $current_page='comments' ?>
  <?php include 'includes/aside.php'; ?>
  <script src="../assets/vendors/jquery/jquery.js"></script>
  <script src="../assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="../assets/vendors/jsrender/jsrender.js"></script>
  <script src="../assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <!-- 用模板引擎渲染数据 -->
  <script id="comments_tmpl" type="text/x-jsrender">
    {{for comments}}
    <tr data-id="{{:id}}" {{if status == 'held'}} class="warning" {{else status == 'rejected'}} class="danger" {{/if}}>
      <td class="text-center"><input type="checkbox"></td>
      <td class="text-center">{{:author}}</td>
      <td>{{:content}}</td>
      <td class="text-center">{{:post_title}}</td>
      <td class="text-center">{{:created}}</td>
      <td class="text-center">{{:status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许'}}</td>
      <td class="text-center">
        {{if status == 'held'}}
        <a href="javascript:;" class="btn btn-info btn-xs btn‐approved" data-id="{{:id}}">批准</a>
        <a href="javascript:;" class="btn btn-warning btn-xs btn‐reject" data-id="{{:id}}">拒绝</a>
        {{/if}}
        <a href="javascript:;" class="btn btn-danger btn-xs btn‐delete" data-id="{{:id}}">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
  <script type="text/javascript">
    // 使用分页模板以及ajax向服务端请求数据

    var current_page=1;

    function loadData(p){
      $('tbody').fadeOut();
      $.getJSON('../admin/api/commentsapi.php',{p: p},function(res){

        if (p > res.total_pages) {
          loadData(res.total_pages);
          return;
        }

        $('.pagination').twbsPagination('destroy');
        $('.pagination').twbsPagination({
          first:"\u9996\u9875",
          last:"\u672b\u9875",
          prev:"\u4e0a\u4e00\u9875",
          next:"\u4e0b\u4e00\u9875",
          startPage:p,
          totalPages:res.total_pages,
          visiblePages:5,
          initiateStartPageClick:false,
          onPageClick:function(e,p){
            // $.getJSON('../admin/api/commentsapi.php',{p: page},function(res){
            //   var html=$('#comments_tmpl').render({comments:res.comments});
            //   $('tbody').html(html);
            loadData(p);
          }
        })
        var html =$('#comments_tmpl').render(res);
        $('tbody').html(html).fadeIn();
        current_page=p;
      })
    }

    loadData(current_page);

    // 删除评论 
    // --------------------------
    // $('.btn-delete').on('click',function(){})
    // --------使用这种方法发现不行，这是因为使用模板引擎  数据还没有加载完

    // 使用委托事件来完成注册  删除评论
    $('tbody').on('click','.btn‐delete',function(){
      // 1.拿到需要删除的id
      var tr = $(this).parent().parent();
      var id = parseInt(tr.data('id'));
      // 2.发送一个ajax请求告诉服务器要删除的数据
      $.getJSON('../admin/comments-delete.php',{id:id},function(res){
        if (!res) return;
        loadData(current_page);
      })
    })

    // 批准评论
    $('tbody').on('click','.btn‐approved',function(){
      var tr = $(this).parent().parent();
      var id = parseInt(tr.data('id'));
      $.post('../admin/comments-status.php?id='+id,{status:'approved'},function(res){
        res.success && loadData(current_page);
      });
    })

    // 拒绝评论
    $('tbody').on('click','.btn‐reject',function(){
      var tr = $(this).parent().parent();
      var id = parseInt(tr.data('id'));
      $.post('../admin/comments-status.php?id='+id,{status:'rejected'},function(res){
        res.success && loadData(current_page);
      });
    })

    // 显示批量操作
    var btnBatch =$('.btn-batch');

    var checkedItems=[];

    $('tbody').on('change','td>input[type=checkbox]',function(){
      var id=parseInt($(this).parent().parent().data('id'));
      
      if ($(this).prop('checked')) {
        checkedItems.push(id);
      }else{
        checkedItems.splice(checkedItems.indexOf(id),1)
      }
      checkedItems.length ? btnBatch.fadeIn() : btnBatch.fadeOut();
    })

    // 全选与不全选
    $('th>input[type=checkbox]').on('change',function(){
      var checked=$(this).prop('checked');
      $('td>input[type=checkbox]').prop('checked',checked).trigger('change');
    })

    // 批量操作
    btnBatch.on('click','.btn-info',function(e){
      $.post('../admin/comments-status.php?id='+checkedItems.join(','),{status:'approved'},function(res){
        res.success && loadData(current_page);
      })
    })
    .on('click','.btn-warning',function(e){
      $.post('../admin/comments-status.php?id='+checkedItems.join(','),{status:'rejected'},function(res){
        res.success && loadData(current_page);
      })
    })
    .on('click','.btn-danger',function(e){
      $.get('../admin/comments-delete.php',{id:checkedItems.join(',')},function(res){
        if (!res) return;
        loadData(current_page);
      })
    })

    $(document)
     .ajaxStart(function () {
       NProgress.start()
     })
     .ajaxStop(function () {
       NProgress.done()
     })



  </script>
  <script>NProgress.done()</script>
</body>
</html>
