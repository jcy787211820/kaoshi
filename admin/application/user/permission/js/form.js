/**
 * int var data
 */
var BASE_USER_GROUP		= '<?=\manager\Permission::PERMISSION_BASE_USER_GROUP?>';
var BASE_USER_LEVEL		= '<?=\manager\Permission::PERMISSION_BASE_USER_LEVEL?>';
var BASE_USER			= '<?=\manager\Permission::PERMISSION_BASE_USER?>';
var BASE_IP				= '<?=\manager\Permission::PERMISSION_BASE_IP?>';
var permission_data		= '<?=$permission["permission_data"]?>' == '' ? {} : jQuery.parseJSON('<?=$permission["permission_data"]?>');
var user_group_names	= '<?=json_encode($user_group_names);?>' == '[]' ? {} : jQuery.parseJSON('<?=json_encode($user_group_names);?>');
var user_level_names	= '<?=json_encode($user_level_names);?>' == '[]' ? {} : jQuery.parseJSON('<?=json_encode($user_level_names);?>');

/**
 * set permission data value
 */
var setPermissionData	= function(setting_type){
	if(typeof(setting_type) != 'undefined'){
		getPermissionDataTag( setting_type );
	}
	else{
		for(var base_type in permission_data ){
			getPermissionDataTag( base_type );
		}
	}
};
/**
 * set permission data dom
 */
var getPermissionDataTag	= function(type){	
	var data_tag	= [];
	for(var i in permission_data[type].data ){
		var name = '';
		var value;
		
		value	= permission_data[type].data[i];
		switch(type){
			case BASE_USER_GROUP:
				name	= user_group_names[value];
				break;
			case BASE_USER_LEVEL:
				name	= user_level_names[value];
				break;
			case BASE_USER:
				name	= value;
				break;
			case BASE_IP:
				name	= value;
				break;
			default:
					'SYSTEM ERROR.';
		}
		
		data_tag.push('<span>' + 
							'<i>' + name + '</i>' +
							'<input type="hidden" name="permission_data[' + type + '][data][]" value="' + value + '"/>' +
							'<a href="javascript:void(0)" onclick="jQuery(this).parent().remove()" style="color:red;">x</a>'+
						'</span>');
	}
	jQuery('#permission_data_' + type ).empty().append(data_tag.join(' '));
};
/**
 * change permission data
 */
var changePermissonData	= function( base_type ){
	/*
	 * init permission data type value
	 */
	if(typeof( permission_data[base_type] ) == 'undefined')
	{
		permission_data[base_type]		= {};
		permission_data[base_type].data	= [];
	}
	/*
	 * set permission data
	 */
	switch( base_type ){
		case BASE_USER_GROUP:
			layer('/user/group', base_type );
			break;
		case BASE_USER_LEVEL:
			layer('/user/level', base_type );
			break;
		case BASE_USER:
			layer('/user/info', base_type );
			break;
		case BASE_IP:		
			if( ip = prompt("请输入Ip.")){
				if(jQuery.inArray( ip, permission_data[base_type].data ) == -1 ){
					permission_data[base_type].data.push( ip );
					setPermissionData( base_type );
				}
			}
			break;
		default:
			alert('SYSTEM ERROR.');
	}
};
/**
 * set permission data layer
 */
var layer	= function( src, type ){
	
	/*
	 * iframe tag
	 */
	var tag	= 
		'<div id="open_permission_layer">' +
			'<p><input type="button" value="添加所选" id="add_permission"/> <input type="button" value="取消" id="cancal_layer"/></p>' +
			'<iframe src="' + src + '" width="100%" height="100%" frameborder="0" ></iframe>' +
		'</div>';
	jQuery('body').append( tag );
	
	/*
	 * css style
	 */
	jQuery('#open_permission_layer').css({
		'position'	: 'absolute',
		'left'		: 0,
		'top'		: 0,
		'width'		: '100%',
		'height'	: jQuery(window).height() < jQuery('body').height() ? jQuery('body').height() : jQuery(window).height(),
		'background': '#FFFFFF'
	});
	
	jQuery('#open_permission_layer>p').css({
		'padding-right'	: '20px',
		'text-align'	: 'right'
	});	
	
	/*
	 * init permission data
	 */
	if(typeof(permission_data[type]) == 'undefined')
	{
		permission_data[type]		= {};
		permission_data[type].data	= [];
	}
	
	/*
	 * add permission data
	 */
	jQuery('#open_permission_layer :button#add_permission').click(function(){
		//get seq tag
		switch(type)
		{
			case BASE_USER_GROUP:
				check_box_tag	= jQuery('#open_permission_layer>iframe').contents().find(':checkbox[name="user_group_code"]:checked');
				break;
			case BASE_USER_LEVEL:
				check_box_tag	= jQuery('#open_permission_layer>iframe').contents().find(':checkbox[name="user_level_seq[]"]:checked');
				break;
			case BASE_USER:
				check_box_tag	= jQuery('#open_permission_layer>iframe').contents().find(':checkbox[name="user_seq[]"]:checked');
				break;
			default:
				"System error, type error";
		}
		
		//reset data value
		jQuery(check_box_tag).each(function(){			
			var check_box_val	= jQuery(this).val();
			if(jQuery.inArray( check_box_val, permission_data[type].data ) == -1 )
			{
				switch( type )
				{
					case BASE_USER_GROUP:
						permission_data[type].data.push(check_box_val);
						user_group_names[check_box_val]	= jQuery(this).closest('td').find('[id^="update_group_name"]').text();
						break;
					case BASE_USER_LEVEL:
						permission_data[type].data.push(check_box_val);
						user_level_names[check_box_val]	= jQuery(this).closest('tr').find('[name="user_level_name"]').text();
						break;
					case BASE_USER:
						var user_id		= jQuery(this).closest('tr').find('[name="user_id"]').text();
						permission_data[type].data.push( user_id );
						break;
					default:
						"System error, type error";
				}

			}
		});

		//reset tag
		setPermissionData( type );
		//layer close
		if(confirm('添加成功，是否返回权限设置页面?')){
			jQuery('#open_permission_layer :button#cancal_layer').click();
		}
	});
	
	
	/*
	 * close layer
	 */
	jQuery('#open_permission_layer :button#cancal_layer').click(function(){
		jQuery('#open_permission_layer').remove();
	});
};
/**
 * reset form data
 */
var doReset	= function(){
	jQuery('#form_edit').get(0).reset();
	permission_data		= '<?=$permission["permission_data"]?>' == '' ? {} : jQuery.parseJSON('<?=$permission["permission_data"]?>');
	jQuery('div[id^=permission_data_]').empty();
};
/**
 * edit form data
 */
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
		if(jQuery('[name="permission_seq"]').val() > 0 ){
			jQuery('#form_edit').attr('action','/user/permission/update');
		}else{
			jQuery('#form_edit').attr('action','/user/permission/insert');
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
					if( form_reset == true ) doReset();
				}
			},
			'json'
		);
	}
	return false;
};

jQuery(document).ready(function(){
	setPermissionData();
});
