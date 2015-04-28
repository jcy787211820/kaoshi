/**
 * edit form data
 */
var doAgree	= function(){
	if(confirm('你确定要提交表单吗？'))
	{					
		/*
		 * post
		 */
		jQuery('body').mask('loading...');
		jQuery.post(
			'/user/writeExamApply/agree',
			jQuery('#form_edit').serialize(),
			function(json){
				jQuery('body').unmask();
				if(chkAjaxRsp( json ) == true)
				{
					alert('操作成功.');
				}
			},
			'json'
		);
	}
	return false;
};
var doRefuse	= function(){
	if(confirm('你确定要提交表单吗？'))
	{					
		/*
		 * post
		 */
		jQuery('body').mask('loading...');
		jQuery.post(
			'/user/writeExamApply/refuse',
			jQuery('#form_edit').serialize(),
			function(json){
				jQuery('body').unmask();
				if(chkAjaxRsp( json ) == true)
				{
					alert('操作成功.');
				}
			},
			'json'
		);
	}
	return false;
};
var doClose	= function(){
	if(confirm('你确定要提交表单吗？'))
	{					
		/*
		 * post
		 */
		jQuery('body').mask('loading...');
		jQuery.post(
			'/user/writeExamApply/close',
			jQuery('#form_edit').serialize(),
			function(json){
				jQuery('body').unmask();
				if(chkAjaxRsp( json ) == true)
				{
					alert('操作成功.');
				}
			},
			'json'
		);
	}
	return false;
};