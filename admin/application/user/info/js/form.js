/**
 * areas selected bar
 */
var makeAreaTag = function( container, area_parent_code ){
	//check area_parent_code
	area_parent_code	= typeof( area_parent_code ) == 'undefined' ? '' : area_parent_code;
	
	var areas				= jQuery.parseJSON('<?=json_encode($areas)?>');
	var def_area_code		= jQuery(container).find('[name="def_area_code"]').val();
	var has_tag				= false;	
	
	//tag
	var tag = [];
	tag.push('<select name="area" onchange="changeArea(this)">');
	tag.push('<option value="">==请选择==</option>');
	for(var i in areas){
		if( areas[i].area_code.slice(0,-2) == area_parent_code ){
			var	selected	= '';
			var area_code_length	= areas[i].area_code.length;
			if( areas[i].area_code == def_area_code.substr( 0, area_code_length )) selected = ' selected="selected" ';
			
			
			tag.push('<option value="' + areas[i].area_code + '"' + selected + '>' + areas[i].area_name +'</option>');
			has_tag	= true;
		}		
	}
	tag.push('</select>');
	
	//append
	if(has_tag == true)
	{
		jQuery(container).append(tag.join("\n"));
		jQuery(container).find('[name="area"]:last').change();
	}
};
var changeArea = function( current ){
	var current_tag_name	= jQuery(current).attr('name'); 
	var area_code			= jQuery(current).val();
	jQuery(current).nextAll('[name="'+current_tag_name+'"]').remove();
	if( area_code == '' ) return;
	makeAreaTag( jQuery(current).parent(), area_code );
};
/**
 * user groups selected bar
 */
var makeUserGroupTag = function( container, user_group_parent_code ){
	//check user_group_parent_code
	user_group_parent_code	= typeof( user_group_parent_code ) == 'undefined' ? '' : user_group_parent_code;
	
	var user_groups			= jQuery.parseJSON('<?=json_encode($user_groups)?>');
	var def_user_group_code	= jQuery(container).find('[name="def_user_group_code"]').val();
	var has_tag				= false;	
	
	//tag
	var tag = [];
	tag.push('<select name="user_group" onchange="changeUserGroup(this)">');
	for(var i in user_groups){
		if( user_groups[i].user_group_code.slice(0,-2) == user_group_parent_code ){
			var	selected	= '';
			var user_group_code_length	= user_groups[i].user_group_code.length;
			if( user_groups[i].user_group_code == def_user_group_code.substr( 0, user_group_code_length )) selected = ' selected="selected" ';
			
			
			tag.push('<option value="' + user_groups[i].user_group_code + '"' + selected + '>' + user_groups[i].user_group_name +'</option>');
			has_tag	= true;
		}		
	}
	tag.push('</select>');
	
	//append
	if(has_tag == true)
	{
		jQuery(container).append(tag.join("\n"));
		jQuery(container).find('[name="user_group"]:last').change();
	}
};
var changeUserGroup = function( current ){
	var current_tag_name	= jQuery(current).attr('name'); 
	var user_group_code			= jQuery(current).val();
	jQuery(current).nextAll('[name="'+current_tag_name+'"]').remove();
	if( user_group_code == '' ) return;
	makeUserGroupTag( jQuery(current).parent(), user_group_code );
};
/**
 * input add, input delete
 */
var addTag	= function( current ){
	var max_line	= jQuery(current).siblings('[name="max_line"]').val();
	if(jQuery(current).parent('.js_addable_tag').siblings('.js_addable_tag').length + 1 >= max_line && max_line != 0 ) return false;
	var clone	= jQuery(current).parent('.js_addable_tag').clone();
	clone.insertAfter(
			jQuery(current).parent('.js_addable_tag')
	).find(':text').val('');
	clone.find('select:first').change();
};

var removeTag	= function( current ){
	if(jQuery(current).parent().siblings('.js_addable_tag').length > 0) jQuery(current).parent().remove();
};

/**
 * form submit
 */
var doEdit	= function(){
	if(confirm('你确定要提交表单吗？')){
		
		/*
		 * is reset
		 */
		var form_reset	= false; 
		
		/*
		 * set action
		 */
		if(jQuery('[name="user_seq"]').val() > 0 ){
			jQuery('#form_edit').attr('action','/user/info/update');
		}else{
			jQuery('#form_edit').attr('action','/user/info/insert');
			form_reset = true;
		}
		/* 
		 * set useable input 
		 */
		var user_info_types			= jQuery.parseJSON('<?=json_encode($user_info_types)?>');
		var USER_INFO_TYPE_GROUP	= '<?=$user_info_manager::USER_INFO_TYPE_GROUP?>';
		var USER_INFO_TYPE_AREA		= '<?=$user_info_manager::USER_INFO_TYPE_AREA?>';
		for( var user_info_type in user_info_types){
			if(		user_info_types[user_info_type].max_input_num != 1
				||	user_info_type == USER_INFO_TYPE_GROUP
				||	user_info_type == USER_INFO_TYPE_AREA
			){
				switch( user_info_type ){
					case USER_INFO_TYPE_GROUP:
						user_info_tag	= jQuery('.js_user_group_div').map(function(){
							if(jQuery(this).find('select[name="user_group"]>option[value!=""]:selected:last').size() == 1){
								return jQuery(this).find('select[name="user_group"]>option[value!=""]:selected:last');
							}
						});
						break;
					case USER_INFO_TYPE_AREA:
						user_info_tag	= jQuery('.js_area_div').map(function(){
							if(jQuery(this).find('select[name="area"]>option[value!=""]:selected:last').size() == 1){
								return jQuery(this).find('select[name="area"]>option[value!=""]:selected:last');
							}
					    });
						break;
					default:
						user_info_tag	= jQuery('[name="user_info[' + user_info_type + ']"]');
				}
				var user_info_txt	= jQuery(user_info_tag).map(function(){
					if(jQuery(this).val() !== ''){
						return jQuery(this).val();				
					}
				}).get().join('_');
				jQuery('#form_edit').append('<input type="hidden" name="user_info[' + user_info_type+ ']" value="' + user_info_txt + '" />');
			}
		}
		
		//none post input disabled
		jQuery('.js_addable_tag').find('input,select,button').attr('disabled','disabled');
		
		/*
		 * post
		 */
		jQuery('body').mask('loading...');
		jQuery.post(
			jQuery('#form_edit').attr('action'),
			jQuery('#form_edit').serialize(),
			function(json){
				jQuery('body').unmask();
				// restore input
				jQuery('.js_addable_tag').find('input,select,button').attr('disabled',false);
				jQuery(':hidden[name^="user_info"]').remove();
				
				if(chkAjaxRsp( json ) == true){
					alert('操作成功.');
					if( form_reset == true ) jQuery('#form_edit').get(0).reset();
				}
			},
			'json'
		);
	}
	return false;
};
/**
 * init
 */
jQuery(document).ready(function(){
	jQuery('.js_user_group_div').each(function(){
		makeUserGroupTag(jQuery(this));
	});
	jQuery('.js_area_div').each(function(){
		makeAreaTag(jQuery(this));
	});
});