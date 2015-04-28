/**
 * Current User Group
 */
var doSearch = function( user_group_parent_code ){
	user_group_parent_code	= typeof( user_group_parent_code ) == 'undefined' ? jQuery('#user_group_parent_code').val() : user_group_parent_code;
	jQuery('#user_group_parent_code').val( user_group_parent_code );
	jQuery('#search_form').submit();
};
/**
 * Parent children user group
 */
var doSearchParent = function(){
	if( jQuery('#user_group_parent_code').val() != '' ){
		var user_group_parent_code = jQuery('#user_group_parent_code').val().slice(0,-2);
		doSearch( user_group_parent_code );		
	}
};
/**
 * Form layer
 */
var layer	= function(){
	
	/*
	 * iframe tag
	 */
	var tag	= 
		'<div id="edit_layer">' +
			'<iframe name="edit_layer" width="100%" height="100%" frameborder="0" ></iframe>' +
		'</div>';
	jQuery('body').append( tag );
	
	/*
	 * css style
	 */
	jQuery('#edit_layer').css({
		'position'	: 'absolute',
		'left'		: 0,
		'top'		: 0,
		'width'		: '100%',
		'height'	: jQuery(window).height() < jQuery('body').height() ? jQuery('body').height() : jQuery(window).height(),
		'background': '#FFFFFF'
	});
};
/**
 * Insert new group
 */
var doInsert	= function(){
	var user_group_parent_code	= jQuery('#user_group_parent_code').val();
	jQuery('#insert_form [name="user_group_parent_code"]').val( user_group_parent_code );
	layer();
	jQuery('#insert_form').get(0).submit();
};
/**
 * update group
 */
var doUpdate		= function(user_group_seq){
	layer();
	jQuery('#update_form_' + user_group_seq ).get(0).submit();
};
/**
 * delete group
 */
var doDelete	= function( user_group_seq ){
	if(confirm('删除当前分类，将同时删除该分类下子分类。确定要进行当前操作吗')){
		jQuery('body').mask('loading...');
		jQuery.post(
			'/user/group/delete',
			{'user_group_seq'	: user_group_seq },
			function( json ){
				jQuery('body').unmask();
				if(chkAjaxRsp( json ) == true){
					alert('操作成功.');
					doSearch();
				}
			},
			'json'
		);
	}
};
/**
 * Ready load
 */
jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			
			/*
			 * init VAR
			 */
			var tag = [];			
			var user_group_parent_name	= json.user_group.user_group_name;
			var user_group_depth		= json.user_group.user_group_code.length / 2 + 1;
			
			/*
			 * title
			 */
			tag.push('<tr>' +
						'<th colspan="' + ( json.lists.length < 3 ? json.lists.length : 3 ) +  '">' +
						user_group_parent_name + ' -' + user_group_depth + '- 级分类' +
						'</th>' +
					'</tr>');   
			
			for(var i in json.lists ){
				var user_group_seq	= json.lists[i].user_group_seq;
				var user_group_code	= json.lists[i].user_group_code;
				var user_group_name	= json.lists[i].user_group_name;
				var css_gray		= json.lists[i].user_group_use_flag == 'F' ? ' style="color:gray;"' : '';
				
				if( i % 3 == 0 ) tag.push('<tr>');
				tag.push(	
					'<td align="left"' + css_gray + '>' +
						'<form action="/user/group/form" name="update_form_' + user_group_seq + '" id="update_form_' + user_group_seq + '" method="post" target="edit_layer" onsubmit="return false">' +
							(jQuery('#open_permission_layer',parent.document).size() == 1 ? '<input type="checkbox" name="user_group_code" value="' + user_group_code + '" />' : '' ) +
							'<input type="hidden" name="action" value="edit" />' +
							'<input type="hidden" name="user_group_seq" value="' + user_group_seq + '" />' +
							'<span class="first-cell" id="update_group_name_' + user_group_seq + '">' + user_group_name + '</span>' +
							'<span class="link-span">' +
								'<a href="javascript:doSearch(\'' + user_group_code + '\');"' + css_gray + '>' + 
									'管理' +
								'</a>&nbsp;&nbsp;' +
								'<a href="javascript:doDelete(\'' + user_group_seq + '\')"' + css_gray + '>删除</a>&nbsp;&nbsp;' +
								'<a href="javascript:doUpdate(\'' + user_group_seq + '\')" id="update_set_tag_' + user_group_seq + '"' + css_gray + '>修改</a>' +
							'</span>' +
						'</form>' +
					'</td>'
				);
				if( i % 3 == 2 ) tag.push('</tr>');
			}
			jQuery(container).html(tag.join("\n"));
		}
	});
	doSearch();
});