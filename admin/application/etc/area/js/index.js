/**
 * Current children area
 */
var doSearch = function( area_parent_code ){
	area_parent_code	= typeof( area_parent_code ) == 'undefined' ? jQuery('#area_parent_code').val() : area_parent_code;
	jQuery('#area_parent_code').val( area_parent_code );
	jQuery('#search_form').submit();
};
/**
 * Parent children area
 */
var doSearchParent = function(){
	if( jQuery('#area_parent_code').val() != '' ){
		var area_parent_code = jQuery('#area_parent_code').val().slice(0,-2);
		doSearch( area_parent_code );		
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
 * Insert new area
 */
var doInsert	= function(){
	var area_parent_code	= jQuery('#area_parent_code').val();
	jQuery('#insert_form [name="area_parent_code"]').val( area_parent_code );
	layer();
	jQuery('#insert_form').get(0).submit();
};
/**
 * update area
 */
var doUpdate		= function(area_seq){
	layer();
	jQuery('#update_form_' + area_seq ).get(0).submit();
};
/**
 * delete area
 */
var doDelete	= function( area_seq ){
	if(confirm('删除当前分类，将同时删除该分类下子分类。确定要进行当前操作吗')){
		jQuery('body').mask('loading...');
		jQuery.post(
			'/etc/area/delete',
			{'area_seq'	: area_seq},
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
			
			/**
			 * init VAR
			 */
			var tag = [];
			var area_parent_name	= json.area.area_name;
			var area_depth			= json.area.area_code.length / 2 + 1;
			
			tag.push('<tr>' +
						'<th colspan="' + ( json.lists.length < 3 ? json.lists.length : 3 ) +  '">' +
							area_parent_name + ' -' + area_depth + '- 级地区' +
						'</th>' +
					'</tr>');   
			for(var i in json.lists ){
				var area_seq	= json.lists[i].area_seq;
				var area_name	= json.lists[i].area_name;
				var area_code	= json.lists[i].area_code;
				if( i % 3 == 0 ) tag.push('<tr>');
				tag.push(	
					'<td align="left">' +
						'<form action="/etc/area/form" name="update_form_' + area_seq + '" id="update_form_' + area_seq + '" method="post" target="edit_layer" onsubmit="return false">' +
							'<input type="hidden" name="action" value="edit" />' +
							'<input type="hidden" name="area_seq" value="' + area_seq + '" />' +
							'<span class="first-cell" id="update_area_name_' + area_seq + '">' + area_name + '</span>' +
							'<span class="link-span">' +
								'<a href="javascript:doSearch(\'' + area_code + '\');">' + 
									'管理' +
								'</a>&nbsp;&nbsp;' +
								'<a href="javascript:doDelete(\'' + area_seq + '\')">删除</a>&nbsp;&nbsp;' +
								'<a href="javascript:doUpdate(\'' + area_seq + '\')" id="update_set_tag_' + area_seq + '">修改</a>' +
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