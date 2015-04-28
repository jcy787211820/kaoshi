/**
// * Init vars
// */
var PHP_CATEGORYS					= jQuery.parseJSON('<?=json_encode( $categorys )?>');
var PHP_CATEGORY_MAP_CODES			= jQuery.parseJSON('<?=json_encode( $category_map_codes )?>');
var PHP_TEST_ANSWER_JSON			= jQuery.parseJSON('<?=addslashes($test["test_answer_json"])?>');
var PHP_TEST_CHECK_FLAG				= '<?=$test["test_check_flag"]?>';
var PHP_TEST_TYPE					= '<?=$test["test_type"]?>';
var PHP_TEST_TYPE_RADIO				= '<?=\manager\Test::TEST_TYPE_RADIO?>';
var PHP_TEST_TYPE_CHECKBOX			= '<?=\manager\Test::TEST_TYPE_CHECKBOX?>';
var PHP_TEST_TYPE_T_F				= '<?=\manager\Test::TEST_TYPE_T_F?>';

/**
 * answer option target
 */
var makeTabContent	= function( container, count ){
	var letters				='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var container_index		= jQuery(container).index();

	/*
	 * init answer option range
	 */
	var tag					= [];
	for(var i = 0; i < count; i++){
		tag.push(
			'<div class="input-group">'
+				'<span class="input-group-addon">' + letters[i] + '</span>'
+				'<input type="text" class="form-control" name="test_answer_content[' + container_index + '][' + i + ']" value="" placeholder="请输入选项内容" />'
+				'<span class="input-group-addon">'
+					'<input type="checkbox" name="test_real_answer[' + container_index + '][' + i + ']" value="T" onclick="checkRealAnswer(this)"/>'
+				'</span>'
+			'</div>'
		);
	}
	
	/*
	 * edit current index option tag
	 */
	var dropdown_tag	= [];
	for(var i = 0; i < count; i++){
		dropdown_tag.push('<li><a href="javascript:void(0);" onclick="deleteTestOpt(\'' + container + '\',' + i + ')">删除选项 ' + letters[i] + '</a></li>');
	}
	tag.push(
		'<div class="text-center">'
+			'<div class="btn-group dropdown">'
+				'<button type="button" id="add_answer_option_btn_' + container_index + '" class="btn btn-default" onclick="addTestOpt(\'' + container + '\')">增加选择项</button> '
+				'<button type="button" id="delete_answer_option_btn_' + container_index + '" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">删除<span class="caret"></span></button>'
+				'<ul role="menu" class="dropdown-menu" aria-labelledby="delete_answer_option_btn_' + container_index + '">'
+					dropdown_tag.join("\n")
+				'</ul>'
+			'</div>'
+		'</div>'
);
	tag.push('<span class="help-block">请在正确选项后面打钩.</span>');
	
	jQuery(container).html(tag.join("\n"));
};
/*
 * choose tab menu
 */
var makeTab			= function(index){
	var option_count_tag	= [];
	for(var i = 2; i < 27; i++ ){
		option_count_tag.push(
			'<li>'
+				'<a data-toggle="tab" tabindex="-1" href="#test_answer_option_index_' + index + '" onclick="makeTabContent(\'#test_answer_option_index_' + index + '\','+ i +')">'
+					'@ ' + i + '个选项'
+				'</a>'
+			'</li>'
		);
	}
	
	var tab_force_style			= 'style="display:inline-block;border-right:0;margin-right:0;border-radius:4px 0 0 0;"';
	var dropdown_force_style	= 'style="display:inline-block;border-radius:0 4px 0 0;"';
	var option_number			= jQuery('#test_answer_option_tab>li.dropdown').size() + 1;
	jQuery('#test_answer_option_tab').append(
		'<li class="dropdown">'
+			'<input type="hidden" name="test_answer_option_tab_index" value="' + index + '"/>'
+			'<a data-toggle="tab" href="#test_answer_option_index_' + index + '"' + tab_force_style + '>选项 ' + option_number + '</a>'
+			'<a id="test_answer_option_drop_' + index + '" data-toggle="dropdown" href="#" class="dropdown-toggle"' + dropdown_force_style + '>'
+				'<span class="caret"></span>'
+			'</a>'
+			'<ul role="menu" class="dropdown-menu" aria-labelledby="test_answer_option_drop_' + index + '">'
+				(index > 1 ? '<li><a href="javascript:void(0)" onclick="deleteTab(' + index + ')">删除当前选项</a></li>' : '')
+				'<li class="divider"></li>'
+				'<li class="dropdown-header">初始化选项范围</li>'
+				'<li class="divider"></li>'
+					option_count_tag.join("\n")
+			'</ul>'
+		'</li>'
	);

	jQuery('#test_answer_option_tab_content').append(
			'<div class="tab-pane fade in active" id="test_answer_option_index_' + index + '"></div>'
	);	
	
	makeTabContent('#test_answer_option_index_' + index, 3 );
	jQuery('#test_answer_option_drop_' + index).next().find('a:eq(1)').tab('show');
};

/*
 * chage select test type
 */
var changeTestType	= function(){
	var test_type	= jQuery('#test_type').val();
	switch( test_type )
	{
		case PHP_TEST_TYPE_T_F:
			jQuery('#add_answer_option_btn').hide();
			jQuery('#test_answer_container').html(
				'<div class="radio-inline">'
+					'<input type="radio" name="test_real_answer" value="T" id="test_real_answer_T" /><label class="pull-normal" for="test_real_answer_T">正确</label>'
+				'</div>'
+				'<div class="radio-inline">'
+					'<input type="radio" name="test_real_answer" value="F" id="test_real_answer_F" /><label class="pull-normal" for="test_real_answer_F">错误</label>'
+				'</div>'
			);
			break;
		default:
			jQuery('#add_answer_option_btn').show();
			jQuery('#test_answer_container').html(
				'<ul class="nav nav-tabs" id="test_answer_option_tab"></ul>'
+				'<div class="tab-content" id="test_answer_option_tab_content"></div>'
			);	
			makeTab(1);		
	}
};

/*
 * add tab tag
 */
var addAnswerOption	= function(){
	var index	= parseInt(jQuery('#test_answer_option_tab>li.dropdown:last').find('[name="test_answer_option_tab_index"]').val(),10) + 1;
	makeTab( index );
	if( jQuery('#test_answer_option_tab>li.dropdown').size() >= 5 ) jQuery('#add_answer_option_btn').hide();
};

/*
 * delete tab
 */
var deleteTab	= function(index){
	jQuery('#test_answer_option_drop_' + index).closest('li.dropdown').nextUntil().each(function(){
		jQuery(this).children('a:first').html('选项 ' + jQuery(this).index());
	});
	
	if(jQuery('#test_answer_option_index_' + index).next().size() > 0){
		jQuery('#test_answer_option_index_' + index).next().addClass('fade in active');
		jQuery('#test_answer_option_drop_' + index).closest('li.dropdown').next().children('a:first').tab('show');		
	}else{
		jQuery('#test_answer_option_index_' + index).prev().addClass('fade in active');
		jQuery('#test_answer_option_drop_' + index).closest('li.dropdown').prev().children('a:first').tab('show');				
	}
	
	jQuery('#test_answer_option_drop_' + index).closest('li.dropdown').remove();
	jQuery('#test_answer_option_index_' + index).remove();
	
	jQuery('#add_answer_option_btn').show();
};

/*
 * add test option
 */
var addTestOpt	= function(container){
	var letters				= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var add_index			= jQuery(container).find('div.input-group').size();
	var container_index		= jQuery(container).index();
	
	jQuery(container).find('div.input-group').last().after(
		'<div class="input-group">'
+			'<span class="input-group-addon">' + letters[add_index] + '</span>'
+			'<input type="text" class="form-control" name="test_answer_content[' + container_index + '][' + add_index + ']" value="" placeholder="请输入选项内容" />'
+			'<span class="input-group-addon">'
+				'<input type="checkbox" name="test_real_answer[' + container_index + ']['+add_index+']" value="T" onclick="checkRealAnswer(this)" />'
+			'</span>'
+		'</div>'
	);
	
	jQuery(container).find('ul.dropdown-menu').append(
		'<li><a href="javascript:void(0);" onclick="deleteTestOpt(\'' + container + '\',' + add_index + ')">删除选项 ' + letters[add_index] + '</a></li>'
	);
	
	if( add_index >= 25 )		jQuery('#add_answer_option_btn_' + container_index).attr('disabled','disabled');
	else if( add_index >= 2 )	jQuery('#delete_answer_option_btn_' + container_index).attr('disabled',false);
};

var deleteTestOpt	= function( container, delete_index ){
	var letters			= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var inputs			= jQuery(container).find('[name^="test_answer_content"]').map(function(){return jQuery(this).val();});
	var option_count	= jQuery(container).find('div.input-group').size();
	var container_index		= jQuery(container).index();
	
	var tag				= [];
	var dropdown_tag	= [];
	
	for(var i = 0, letter_index = 0; i< option_count; i++){
		if( i != delete_index ){
			
			tag.push(
				'<div class="input-group">'
+					'<span class="input-group-addon">' + letters[letter_index] + '</span>'
+					'<input'
+						' type="text" '
+						' class="form-control" '
+						' name="test_answer_content[' + container_index + '][' + letter_index + ']" '
+						' value="'+inputs[i]+'" '
+						' placeholder="请输入选项内容" '
+					' />'
+					'<span class="input-group-addon">'
+						'<input type="checkbox" name="test_real_answer[' + container_index + '][' + letter_index + ']" value="T" onclick="checkRealAnswer(this)" />'
+					'</span>'
+				'</div>'
			);

			dropdown_tag.push(
				'<li>'
+					'<a href="javascript:void(0);" onclick="deleteTestOpt(\'' + container + '\',' + letter_index + ')">'
+						'删除选项 ' + letters[letter_index] 
+					'</a>'
+				'</li>'
			);
			
			letter_index++;
		}
	}
	
	jQuery(container).find('.input-group').remove();
	jQuery(container).prepend(tag.join("\n"));
	
	jQuery(container).find('ul.dropdown-menu').children('li').remove();
	jQuery(container).find('ul.dropdown-menu').append(dropdown_tag.join("\n"));
	
	if(dropdown_tag.length <= 2)		jQuery('#delete_answer_option_btn_' + container_index).attr('disabled','disabled');
	else if(dropdown_tag.length <= 25 )	jQuery('#add_answer_option_btn_' + container_index).attr('disabled',false);
};

var checkRealAnswer	= function(input){
	if(jQuery('#test_type').val() == PHP_TEST_TYPE_RADIO){
		jQuery(input).closest('.input-group').siblings().find('[name^="test_real_answer"]').attr('checked',false);
	}
};

/*
 * set default answer option
 */
var setDefaultAnswer = function()
{
	var test_answer_json	= PHP_TEST_ANSWER_JSON;
	var test_type			= PHP_TEST_TYPE;
	if( test_type == PHP_TEST_TYPE_T_F ){
		jQuery('[name="test_real_answer"][value="'+test_answer_json['answers']+'"]').click();
	}else{
		var need_add_answer_option	= false;
		for(var i in test_answer_json['answers'] ){
			if( need_add_answer_option == true) addAnswerOption();
			for(var j in test_answer_json['answers'][i]) {
				jQuery('[name="test_answer_content['+i+']['+j+']"]').val( test_answer_json['answers'][i][j]['answer_content'] );
				if(test_answer_json['answers'][i][j]['is_real_answer'] == 'T'){
					jQuery('[name="test_real_answer['+i+']['+j+']"]').click();
				}
			}
			need_add_answer_option = true;
		}
	}
	if( PHP_TEST_CHECK_FLAG == 'T' )
	{
		jQuery('#test_answer_container').find('input,button').attr('disabled','disabled');
		jQuery('#test_answer_container').find('.help-block').html('已通过审核，正式发布的题目，不允许再修改。');
	}
};

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
 * edit action
 */
var doSubmit	= function(type)
{
	var  confirm_massage	= type == 'update' ? '确定要修改试题吗?' : '确定要提交试题吗?';
	var  success_massage	= type == 'update' ? '试题修改成功.请等待管理员审核' : '试题添加成功,请等待管理员审核.';
	
	jQuery.msgbox({
		'message'	: confirm_massage,
		'type'		: 'confirm',
		'callback'	: function(is_confirm){
			/*
			 * post
			 */
			if( is_confirm == true ){
				jQuery('body').mask('loading...');
				jQuery.post(
					jQuery('#form_edit').attr('action'),
					jQuery('#form_edit').serialize(),
					function(json){
						jQuery('body').unmask();
						if(chkAjaxRsp(json) == true)
						{
							jQuery.msgbox({
								'message'	: success_massage,
								'type'		: 'info',
								'callback'	: function(){
									if( type == 'insert' ){
										jQuery('#form_edit').attr('action', '/exam/test/update/' + json.encode_seq);
										jQuery('#submit_button').attr('onclick', 'doSubmit("update")');
										jQuery('#test_insert_time_view_div,#test_edit_time_view_div').removeClass('hidden');
										jQuery('#test_insert_time_view_tag').html(json.test_insert_datetime_vw);
									}
									jQuery('#test_edit_time_view_tag').html(json.test_edit_datetime_vw);
								}
							});
						}
					},
					'json'
				);
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
	setDefaultMapCode();

	changeTestType();
	setDefaultAnswer();
	
	jQuery('#left_menu_my_test').addClass('active');
});