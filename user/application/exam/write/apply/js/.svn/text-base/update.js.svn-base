var error = '<?=$error;?>';
var message = '<?=$message;?>';
jQuery(document).ready(function(){
	if(parent.chkAjaxRsp({'error':error,'message':message}) == true)
	{
		parent.jQuery.msgbox({
			'message'	: '修改成功.',
			'type'		: 'info',
			'callback'	: function(){ parent.location.href='/exam/write/apply'; }
		});
	}
});