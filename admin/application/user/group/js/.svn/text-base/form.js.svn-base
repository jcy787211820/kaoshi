/**
 * cancel edit
 */
var doClose	= function(){
	parent.jQuery('#edit_layer').remove();
}
/**
 * edit form data
 */
var doEdit	= function(){
	if(confirm('你确定要提交表单吗？'))
	{			
		/*
		 * set action
		 */
		if(jQuery('[name="user_group_seq"]').val() > 0 ){
			jQuery('#form_edit').attr('action','/user/group/update');
		}else{
			jQuery('#form_edit').attr('action','/user/group/insert');
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
					parent.doSearch();
					parent.jQuery('#edit_layer').remove();
					doClose();
				}
			},
			'json'
		);
	}
	return false;
};