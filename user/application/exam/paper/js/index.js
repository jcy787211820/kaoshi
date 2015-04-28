/**
// * Init vars
// */
var PHP_CATEGORYS			= jQuery.parseJSON('<?=json_encode( $categorys )?>');

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

/**
 * init do function
 */
jQuery(document).ready(function(){
	setCategoryTag('.js-category-container');	
	$(".form-date").datetimepicker({
		'language': 'zh-CN',
        'weekStart': 1,
        'todayBtn':  1,
		'autoclose': 1,
		'todayHighlight': 1,
		'startView': 2,
		'minView': 2,
		'forceParse': 0,
		'format':'yyyy-mm-dd'
	});

	jQuery('#left_menu_my_test').addClass('active');
	
	jQuery('#form_search').pageList({
		'container'	: '#list_table>tbody',
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				tag.push(	
					'<tr>'
+						'<td class="text-center">' + json.lists[i].test_type_vw +'</td>'
+						'<td>' + json.lists[i].test_question_vw + '</td>'
+						'<td class="text-center">' + json.lists[i].test_price + '</td>'
+						'<td class="text-center">' + json.lists[i].test_check_flag_vw + '</td>'
+						'<td class="text-center">' + json.lists[i].test_insert_datetime_vw + '</td>'
+						'<td class="text-center"><a href="/exam/test/form/'+json.lists[i].test_seq_vw+'">修改</a></td>'
+					'</tr>'
				);
			}
			jQuery(container).html(tag.join("\n"));
		}
	});
	jQuery('#form_search').submit();
});