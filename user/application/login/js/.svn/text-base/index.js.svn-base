/**
 * input taget focus CSS
 */
var focusIn		= function(tag){
	jQuery(tag).css('border-color','#999999');	
	jQuery(tag).prev('label').css('color','#DDDDDD');
	focusWrite(tag);
};
var focusOut	= function(tag){
	jQuery(tag).css('border-color','#DDDDDD');
	jQuery(tag).prev('label').css('color','#AAAAAA');
	focusWrite(tag);
};
var focusWrite	= function(tag){
	if(jQuery.trim(jQuery(tag).val()) != '' )
	{
		jQuery(tag).prev('label').css('visibility','hidden');
	}
	else
	{
		jQuery(tag).prev('label').css('visibility','visible');
	}
};
var autoSetFocus	= function()
{
	jQuery('#loginForm .input').each(function(){
		focusWrite(this);
	});
};

/**
 * Form submit
 */
var doSubmit	= function (){
	jQuery('#error_message').empty().hide();
	
	/**
	 * check Account
	 */
	var user_id	= jQuery.trim(jQuery('#user_id').val());
	if(
			user_id == ''
		||	user_id.match(/^[\w]+$/) == null
	){
		jQuery('#user_id').focus();
		jQuery('#user_id').css('border-color','#FF0000');
		jQuery('#error_message').prepend('<p>请填写有效的用户名。</p>').show();
		return false;
	}	
		
	/**
	 * check password
	 */
	if(jQuery('#user_password').val().length < 6 ){
		jQuery('#user_password').focus();
		jQuery('#user_password').css('border-color','#FF0000');
		jQuery('#error_message').prepend('<p>用户密码必须填写，并且不能小于6位数。</p>').show();
		return false;
	}
	
	/**
	 * check captcha
	 */
	var captcha = jQuery.trim(jQuery('#captcha').val());
	if(
			captcha.length != 4
		||	captcha.match(/^[A-Za-z0-9]{4}$/) == null
	){
		jQuery('#captcha').focus();
		jQuery('#captcha').css('border-color','#FF0000');
		jQuery('#error_message').prepend('<p>请正确填写提示的验证码。</p>').show();
		return false;
	}

	/**
	 * login action
	 */
	jQuery('body').mask('登陆处理中,请稍后......');
	jQuery.post(
		jQuery('#loginForm').attr('action'),
		jQuery('#loginForm').serialize(),
		function(json){
			jQuery('#change_captcha').click();
			jQuery('body').unmask();
			if(chkAjaxRsp(json)){
				var http_reffrer	= jQuery('#http_reffrer').val();
				if(		http_reffrer == ''
					||	http_reffrer.toLowerCase() == '<?=getBaseConfig("USER_BASE_URI")?>/join' 
					||	http_reffrer.toLowerCase() == '<?=getBaseConfig("USER_BASE_URI")?>/login'
				){
					http_reffrer	= '<?=getBaseConfig("WWW_BASE_URI")?>';
				}
				location.href		= http_reffrer;
			}
		},
	'json');
	return false;
};

/**
 * init script
 */
jQuery(document).ready(function(){
	setInterval("autoSetFocus()", 500);
});