<?php

?>

<script type="text/javascript">
	$(document).ready(function(){
		if( $('#loginUserInfoId').html() != 'guest' ) {
			$('#loginOperationId').html('退出') ;
		} else {
			$('#loginOperationId').html('登录') ;
		}	
		
		$('#loginOperationId').click( function() {
			if( $(this).html() == '退出' ) {
				$.get('index.php?com=user&__action=logOut&responseType=plainText',function(data) {
					//if( data == 'success') {
						alert('logout success');
						$('#loginOperationId').html('登录');
						$('#loginUserInfoId').html('guest');
					//}
				});
			} else if( $(this).html() == '登录' ) {
				window.document.location.href="index.php?com=user&__action=login"
			}

		});
	});
</script>
<div>
	用户名: <span id="loginUserInfoId" style="color:red;font-weight:800;"><?php echo $view_username ?></span>&nbsp;&nbsp;<a id="loginOperationId" href="javascript:void(0)"></a>
</div>