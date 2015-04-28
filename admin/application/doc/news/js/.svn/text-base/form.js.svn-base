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
 * categorys selected bar
 */
var makeCategoryTag = function( container, category_parent_code ){
	//check category_parent_code
	category_parent_code	= typeof( category_parent_code ) == 'undefined' ? '' : category_parent_code;
	
	var categorys			= jQuery.parseJSON('<?=json_encode($categorys)?>');
	var def_category_code	= jQuery(container).find('[name="def_category_code"]').val();
	var has_tag				= false;	
	
	//tag
	var tag = [];
	tag.push('<select name="category" onchange="changeCategory(this)">');
	for(var i in categorys){
		if( categorys[i].category_code.slice(0,-2) == category_parent_code ){
			var	selected	= '';
			var category_code_length	= categorys[i].category_code.length;
			if( categorys[i].category_code == def_category_code.substr( 0, category_code_length )) selected = ' selected="selected" ';
			
			
			tag.push('<option value="' + categorys[i].category_code + '"' + selected + '>' + categorys[i].category_name +'</option>');
			has_tag	= true;
		}		
	}
	tag.push('</select>');
	
	//append
	if(has_tag == true)
	{
		jQuery(container).append(tag.join("\n"));
		jQuery(container).find('[name="category"]:last').change();
	}
};
var changeCategory = function( current ){
	var current_tag_name	= jQuery(current).attr('name'); 
	var category_code		= jQuery(current).val();
	jQuery(current).nextAll('[name="'+current_tag_name+'"]').remove();
	if( category_code == '' ) return;
	makeCategoryTag( jQuery(current).parent(), category_code );
};

/**
 * input add, input delete
 */
var addTag	= function( current ){
	var clone	= jQuery(current).parent('.js_category_div').clone();
	clone.insertAfter(
			jQuery(current).parent('.js_category_div')
	).find(':text').val('');
	clone.find('select:first').change();
};

var removeTag	= function( current ){
	if(jQuery(current).parent().siblings('.js_category_div').length > 0) jQuery(current).parent().remove();
};
/**
 * edit
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
		if(jQuery('[name="news_seq"]').val() > 0 ){
			jQuery('#form_edit').attr('action','/doc/news/update');
		}else{
			jQuery('#form_edit').attr('action','/doc/news/insert');
			form_reset	= true;
		}
		
		/* 
		 * set useable category input 
		 */
		var category_code_text	= jQuery('.js_category_div').map(function(){
				return jQuery(this).find('[name="category"]>option[value!=""]:selected:last').val();				
		}).get().join('_');
		jQuery('#form_edit').append('<input type="hidden" name="category_code_text" value="' + category_code_text + '" />');
		//none post category input disabled
		jQuery('.js_category_div').find('input,select,button').attr('disabled','disabled');
		
		/* 
		 * set useable area input 
		 */
		var area_code	= jQuery('.js_area_div').find('[name="area"]>option[value!=""]:selected:last').val();
		area_code		= typeof(area_code) == 'undefined' ? '' : area_code;
		jQuery('#form_edit').append('<input type="hidden" name="area_code" value="' + area_code + '" />');
		//none post category input disabled
		jQuery('.js_area_div').find('input,select,button').attr('disabled','disabled');
		
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
					
					// restore category input
					jQuery('.js_category_div').find('input,select,button').attr('disabled',false);
					jQuery(':hidden[name="category_code_text"]').remove();
					
					// restore area input
					jQuery('.js_area_div').find('input,select,button').attr('disabled',false);
					jQuery(':hidden[name="area_code"]').remove();
					
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
	jQuery('.js_category_div').each(function(){
		makeCategoryTag(jQuery(this));
	});
	
	jQuery('.js_area_div').each(function(){
		makeAreaTag(jQuery(this));
	});
});