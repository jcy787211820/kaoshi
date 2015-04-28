var doEdit	= function(){
	if(confirm('你确定要提交表单吗？'))
	{	
		/*
		 * is reset
		 */
		var form_reset	= false; 
		
		/*
		 * set action
		 */
		if(jQuery('[name="user_level_seq"]').val() > 0 ){
			jQuery('#form_edit').attr('action','/user/level/update');
		}else{
			jQuery('#form_edit').attr('action','/user/level/insert');
			form_reset	= true;
		}
		
		/*
		 * post
		 */
		jQuery('body').mask('loading...');
		jQuery.post(
			jQuery('#form_edit').attr('action'),
			jQuery('#form_edit').serialize(),
			function(json){
				jQuery('body').unmask();
				if(chkAjaxRsp( json ) == true)
				{
					alert('操作成功.');
					if( form_reset == true ) jQuery('#form_edit').get(0).reset();
				}
			},
			'json'
		);
	}
	return false;
};