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
 * check user id
 */
var checkUserId	= function( none_ajax ){
	/*
	 * INIT 
	 */
	var error	= false;
	var message	= '';
	jQuery('#user_id_error').empty();
	focusOut('#user_id');
	
	/*
	 * check
	 */
	var user_id	= jQuery.trim(jQuery('#user_id').val());
	if( user_id == '' ){
		error	= true;
		message	= '请填写用户名!';
	}	
	else if( user_id.match(/^[\w]+$/) == null){
		error	= true;
		message	= '用户名只能由字母,数字,下划线组成!';
	}
	else if( user_id.length > 45 ){
		error	= true;
		message	= '用户Id不能超过45个字符!';
	}
	else if(typeof( none_ajax ) == 'undefined' || none_ajax == false){
		jQuery.post(
			'/join/isExist',
			jQuery('#user_id').serialize(),
			function(json){
				if(typeof(json.error) == 'undefined' || json.error == true){
					if(typeof(json.message) == 'undefined') json.message = '致命错误.';
					error	= true;
					jQuery('#user_id').focus();
					jQuery('#user_id').css('border-color','#FF0000');
					jQuery('#user_id_error').prepend( json.message );
				}
				else if( json.is_exist == true )
				{
					error	= true;
					jQuery('#user_id').focus();
					jQuery('#user_id').css('border-color','#FF0000');
					jQuery('#user_id_error').prepend('用户已经存在,请重新填写!');				
				}
			},
		'json');
	}
	else{
		return true;
	}
	
	/*
	 * return
	 */
	if( error == true ){
		jQuery('#user_id').focus();
		jQuery('#user_id').css('border-color','#FF0000');
		jQuery('#user_id_error').prepend( message );
	}
	return false;
};
/**
 * check password
 */
var checkPassword	= function(){
	/*
	 * INIT 
	 */
	var error	= false;
	var message	= '';
	jQuery('#user_password_error').empty();
	focusOut('#user_password');
	
	/*
	 * check
	 */
	var user_password	= jQuery('#user_password').val();
	if(user_password.length < 6 ){
		error	= true;
		message	= '用户密码必须填写，并且不能小于6位数!';
	}
	else if(user_password.length > 100 ){
		error	= true;
		message	= '用户密码不能大于100位字符!';
	}
	
	/*
	 * return
	 */
	if( error == true ){
		jQuery('#user_password').focus();
		jQuery('#user_password').css('border-color','#FF0000');
		jQuery('#user_password_error').prepend( message );
		return false;
	}
	return true;
};

/**
 * check confirm password
 */
var checkConfirmPassword	= function(){
	/*
	 * INIT 
	 */
	var error	= false;
	var message	= '';
	jQuery('#user_password_confirm_error').empty();
	focusOut('#user_password_confirm');
	
	/*
	 * check
	 */
	var user_password			= jQuery('#user_password').val();
	var user_password_confirm	= jQuery('#user_password_confirm').val();
	if( user_password !== user_password_confirm ){
		error	= true;
		message	= '两次输入的密码不一致!';		
	}
	
	/*
	 * return
	 */
	if( error == true ){
		jQuery('#user_password_confirm').focus();
		jQuery('#user_password_confirm').css('border-color','#FF0000');
		jQuery('#user_password_confirm_error').prepend( message );
		return false;
	}
	return true;
};

/**
 * check captcha
 */
var checkCaptcha	= function(){
	/*
	 * INIT 
	 */
	var error	= false;
	var message	= '';
	jQuery('#captcha_error').empty();
	focusOut('#captcha_confirm');
	
	/*
	 * check
	 */
	var captcha = jQuery.trim(jQuery('#captcha').val());
	if( captcha.match(/^[A-Za-z0-9]{4}$/) == null ){
		error	= true;
		message	= '请输入图片中的字母和数字!';
	}
	
	/*
	 * return
	 */
	if( error == true ){
		jQuery('#captcha').focus();
		jQuery('#captcha').css('border-color','#FF0000');
		jQuery('#captcha_error').prepend( message );
		return false;
	}
	return true;
};
/**
 * Form submit
 */
var doSubmit	= function (){
	/**
	 * login action
	 */
	if(checkUserId(true) == true && checkPassword() == true && checkConfirmPassword() == true && checkCaptcha() == true)
	{
		jQuery('body').mask('会员注册处理中,请稍后......');
		jQuery.post(
			jQuery('#joinForm').attr('action'),
			jQuery('#joinForm').serialize(),
			function(json){
				jQuery('#change_captcha').click();
				jQuery('body').unmask();
				if(chkAjaxRsp(json)){
					jQuery.msgbox({
						'message'	: '注册成功，将进入登陆页面!',
						'callback'	: function(result){ location.href="/login"; }
					});
				}
			},
		'json');
	}
	return false;
};

/**
 * init script
 */
jQuery(document).ready(function(){
	setInterval("autoSetFocus()", 500);
});