<div id="redirectDiv">
<div id="redirctMessage">
<H2><?php echo  $redirect_message ?></H2>
<div style="font-weight:800;padding:5px 0px;"><?php echo  str_replace('%TIME%', $redirect_waitTime , I18n::get('redirect_time_prompt') ); ?></div>
<div><a href="<?php echo $redirect_url ?>"><span style="font-wieght:700;"><?php echo  $redirect_title ?></span></a></div>
</div>
</div>
<script type="text/javascript">
setInterval(function(){
var cur = $('.redirect-time').html();
if(cur>0) {var next = cur-1;}$('.redirect-time').html(next);
},1000);
</script>