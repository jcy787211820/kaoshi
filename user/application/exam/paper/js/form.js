/**
 * Init vars
 */
var PHP_CATEGORYS		= jQuery.parseJSON('<?=json_encode( $categorys )?>');
var _encode_seq			= 0;

/**
 * category html target
 */
var setCategoryTag	= function( container, select ){
	/*
	 * clear next all bar
	 */
	jQuery(select).nextAll('[name^="category_code"]').remove();
	if(jQuery(select).val() == '')	return false;

	/*
	 * vars
	 */
	var select_code		= typeof(select) == 'undefined' ? '' : jQuery(select).val();
	var has_children	= false;
	
	/*
	 * make target
	 */
	var tags	= []; 
	tags.push('<select class="form-control" style="width:120px; float:left;" name="category_code[]" onchange="setCategoryTag(jQuery(this).closest(\'.js-category-container\'),this)">');
	tags.push('<option value="">-选择分类-</option>');
	for(var i in PHP_CATEGORYS){
		if( PHP_CATEGORYS[i].category_code.length  - select_code.length != 2 )	continue;
		if( PHP_CATEGORYS[i].category_code.substr( 0, select_code.length ) != select_code )	continue;
		tags.push('<option value="' + PHP_CATEGORYS[i].category_code + '">' + PHP_CATEGORYS[i].category_name + '</option>');
		has_children	= true;
	}
	tags.push('</select>');
	
	/*
	 * append target
	 */
	if( has_children == true )
	{
		jQuery(container).append(tags.join("\n"));
		jQuery(container).find('[name^="category_code"]:last').change();
	}
};

var addCategoryTag	= function (){
	var tag					= '<div class="col-sm-9 pull-right js-category-container"></div>';
	jQuery('.js-category-container:last').after( tag );
	setCategoryTag(jQuery('.js-category-container:last'));
	jQuery('.js-category-container').filter(function(){
	    if(jQuery(this).find('button:contains(删除)').length == 0){
	        return true;
	    }
	}).prepend('<button type="button" class="btn btn-primary" onclick="delCategoryTag(this)">删除</button>');

	if(jQuery('.js-category-container').length >= 5){
		jQuery('#add_category_container_btn').hide();
	}	
};

var delCategoryTag	= function(button){
	jQuery(button).closest('.js-category-container').remove();
	if(jQuery('.js-category-container').length <= 1){
		jQuery('.js-category-container').find('button:contains(删除)').remove();
	}
	if(jQuery('.js-category-container').length < 5){
		jQuery('#add_category_container_btn').show();
	}
};

var setDefaultMapCode	= function()
{
	var category_map_codes		= PHP_CATEGORY_MAP_CODES;
	var need_add_category_tag	= false;
	for(var i in category_map_codes ){
		if( need_add_category_tag == true )	addCategoryTag();
		for( var j = 2, e = category_map_codes[i].length; j <= e; j += 2 ){
			var change_code	= category_map_codes[i].substr( 0, j );
			jQuery('.js-category-container:last').children('select:last').val( change_code );
			jQuery('.js-category-container:last').children('select:last').change();
		}
		need_add_category_tag	= true;
	}
};

/**
 * add test paper unit
 */
var addTestPaperUnit = function(){
	var encode_seq	= _encode_seq;
	jQuery.msgbox({
		'message'	: '添加新试题类型.',
		'type'    	: 'prompt',
		'inputs'  	: [
		          	   {'type' : 'text', 'label' : '标题 "例:单项选择题(每个选项1分，共20分)"', 'name' : 'test_paper_unit_title'},
		          	   {'type' : 'textarea', 'label' : '说明','name' : 'test_paper_unit_description'}
		          	  ],
		'buttons'	: [{'type' : 'yes', 'value' : 'OK'},{'type' : 'close', 'value' : 'Exit'}],
		'callback'	: function(result){
			console.log( result );
			var test_paper_unit_title	= '';
			var test_paper_unit_description	= '';
			for( var i in result ){
				switch( result[i].name ){
					case 'test_paper_unit_title' :
						test_paper_unit_title	= result[i].value;
						break;
					case 'test_paper_unit_description' :
						test_paper_unit_description	= result[i].value;
						break;
				}
			}
			
			jQuery('body').mask('loading...');
			jQuery.post(
				'/exam/paper/unitInsert',
				{
					'encode_seq'					: encode_seq,
					'parent_test_paper_unit_seq'	: '0',
					'test_paper_unit_title'			: test_paper_unit_title,
					'test_paper_unit_description'	: test_paper_unit_description
				},
				function(json){
					jQuery('body').unmask();
					if(chkAjaxRsp(json) == true)
					{
						loadTestPaperUnitList();
					}
				},
				'json'
			);
		}
	});
};

var loadTestPaperUnitList	= function(){
	jQuery('body').mask('loading...');
	jQuery.get(
		'/exam/paper/unitList/' + _encode_seq,
		function(json){
			jQuery('body').unmask();
			if(chkAjaxRsp(json) == true)
			{
				var tag = [];
				for( var i in json.lists )
				{
					tag.push(
							'<div>' + json.lists[i].test_paper_unit_title + '</div>'
					);
				}
//				jQuery.msgbox({
//					'message'	: success_massage,
//					'type'		: 'info',
//					'callback'	: function(){
//					}
//				});
			}
		},
		'json'
	);
};

/**
 * edit action
 */
var doSubmit	= function( form, is_submit )
{
	var  confirm_massage	= '确定要提交吗?';
	var  success_massage	= '成功,请等待管理员审核.';
	
	jQuery.msgbox({
		'message'	: confirm_massage,
		'type'		: 'confirm',
		'callback'	: function(is_confirm){
			/*
			 * post
			 */
			if( is_confirm == true ){
				if(is_submit == true )
				{
					jQuery('body').mask('loading...');
					jQuery(form).get(0).submit();
					jQuery('body').unmask();
				}
				else
				{
					jQuery('body').mask('loading...');
					jQuery.post(
						jQuery(form).attr('action'),
						jQuery(form).serialize(),
						function(json){
							jQuery('body').unmask();
							if(chkAjaxRsp(json) == true)
							{
								jQuery.msgbox({
									'message'	: success_massage,
									'type'		: 'info',
									'callback'	: function(){
										if(jQuery('#test_paper_insert_time_view_div,#test_paper_edit_time_view_div').hasClass('hidden')){
											jQuery('#test_paper_insert_time_view_tag').html(json.test_paper_edit_datetime_vw);
											jQuery('#test_paper_insert_time_view_div,#test_paper_edit_time_view_div').removeClass('hidden');
										}
										jQuery('#test_paper_edit_time_view_tag').html(json.test_paper_edit_datetime_vw);									
										jQuery('#form_edit_paper').attr('action', '/exam/paper/update/' + json.encode_seq);
										jQuery('#form_edit_file').attr('action', '/exam/paper/file/' + json.encode_seq);
										_encode_seq	= json.encode_seq;
									}
								});
							}
						},
						'json'
					);
				}
			}
		}
	});
	return false;
};

/**
 * init do function
 */
jQuery(document).ready(function(){
	
	setCategoryTag('.js-category-container');	
//	setDefaultMapCode();
	jQuery('.tooltips' ).tooltip();
	jQuery('#left_menu_my_test').addClass('active');
});