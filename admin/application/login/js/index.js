var changeCaptcha	= function(current){
	jQuery(current).attr('src','/login/captcha/' + Math.random());
};
var doLogin	= function(){
	jQuery('body').mask('loading...');
	jQuery.post(
		jQuery('#loginForm').attr('action'),
		jQuery('#loginForm').serialize(),
		function(json){
			jQuery('body').unmask();
			if(chkAjaxRsp( json ) == true){
				location.href	= '/';
			}
			changeCaptcha('#js_captcha_img');
		},
	'json');
};