/*点赞功能*/
$('a[data-id]').on('click',function(){
  var _this =$(this);
  $.get('includes/zanapi.php?id='+_this.attr('data-id'),null,function(data){
    if (data !== 'false') {
      _this.html('赞('+data+')');
      _this.css('color','#ff5e52');
    }
  })
})