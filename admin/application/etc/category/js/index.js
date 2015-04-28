/**
 * Current children category
 */
var doSearch = function( category_parent_code ){
	category_parent_code	= typeof( category_parent_code ) == 'undefined' ? jQuery('#category_parent_code').val() : category_parent_code;
	jQuery('#category_parent_code').val( category_parent_code );
	jQuery('#search_form').submit();
};
/**
 * Parent children category
 */
var doSearchParent = function(){
	if( jQuery('#category_parent_code').val() != '' ){
		var category_parent_code = jQuery('#category_parent_code').val().slice(0,-2);
		doSearch( category_parent_code );		
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
 * Insert new category
 */
var doInsert	= function(){
	var category_parent_code	= jQuery('#category_parent_code').val();
	jQuery('#insert_form [name="category_parent_code"]').val( category_parent_code );
	layer();
	jQuery('#insert_form').get(0).submit();
};
/**
 * update category
 */
var doUpdate		= function(category_seq){
	layer();
	jQuery('#update_form_' + category_seq ).get(0).submit();
};
/**
 * delete category
 */
var doDelete	= function( category_seq ){
	if(confirm('删除当前分类，将同时删除该分类下子分类。确定要进行当前操作吗')){
		jQuery('body').mask('loading...');
		jQuery.post(
			'/etc/category/delete',
			{'category_seq'	: category_seq},
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
			var category_parent_name	= json.category.category_name;
			var category_depth			= json.category.category_code.length / 2 + 1;
			
			/*
			 * title
			 */
			tag.push('<tr>' +
						'<th colspan="' + ( json.lists.length < 3 ? json.lists.length : 3 ) +  '">' +
							category_parent_name + ' -' + category_depth + '- 级分类' +
						'</th>' +
					'</tr>');   
			
			for(var i in json.lists ){
				var category_seq	= json.lists[i].category_seq;
				var category_code	= json.lists[i].category_code;
				var category_name	= json.lists[i].category_name;
				var css_gray		= json.lists[i].category_use_flag == 'F' ? ' style="color:gray;"' : '';
				
				if( i % 3 == 0 ) tag.push('<tr>');
				tag.push(	
					'<td align="left"' + css_gray + '>' +
						'<form action="/etc/category/form" name="update_form_' + category_seq + '" id="update_form_' + category_seq + '" method="post" target="edit_layer" onsubmit="return false">' +
							'<input type="hidden" name="action" value="edit" />' +
							'<input type="hidden" name="category_seq" value="' + category_seq + '" />' +
							'<span class="first-cell" id="update_category_name_' + category_seq + '">' + category_name + '</span>' +
							'<span class="link-span">' +
								'<a href="javascript:doSearch(\'' + category_code + '\');"' + css_gray + '>' + 
									'管理' +
								'</a>&nbsp;&nbsp;' +
								'<a href="javascript:doDelete(\'' + category_seq + '\')"' + css_gray + '>删除</a>&nbsp;&nbsp;' +
								'<a href="javascript:doUpdate(\'' + category_seq + '\')" id="update_set_tag_' + category_seq + '"' + css_gray + '>修改</a>' +
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