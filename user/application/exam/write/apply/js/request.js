var error = '<?=$error;?>';
var message = '<?=$message;?>';
jQuery(document).ready(function(){
	if(parent.chkAjaxRsp({'error':error,'message':message}) == true)
	{
		parent.jQuery.msgbox({
			'message'	: '申请成功.',
			'type'		: 'info'
		});
		parent.location.href='/exam/write/apply';
	}
});