var df_list_data	= {};
var chkAjaxRsp	= function(json){
	if(typeof(json.error) == 'undefined'){
		alert('致命错误.');
		return false;
	}
	if(json.error == true){
		if(typeof(json.message) == 'undefined') alert('致命错误.');
		else alert(json.message);
		return false;
	}
	return true;
};

var doBatchAction	= function(){
	var action_uri	= jQuery('#edit_form').attr('action') + jQuery('#batch_action').val();
	if(confirm( '确定要进行当前操作吗?' ) == true){
		jQuery.post(
			action_uri,
			jQuery('#edit_form').serialize(),
			function(json){
				if(chkAjaxRsp( json ) == true ){
					alert('操作成功.');
					jQuery('#search_form').submit();
				}
			},
			'json'
		);
	};
};

var doEdit = function( edit_json_data ){
	var action_uri	= jQuery('#edit_form').attr('action') + 'form';
	var form = [];
	form.push('<form name="temp_form" id="temp_form" action="' + action_uri + '" method="post" target="_self">');
	for(var i in edit_json_data ){
		form.push('<input type="hidden" name="' + i + '" value="' + edit_json_data[i] + '" />');
	}
	form.push('<input type="hidden" name="action" value="edit" />');
	form.push('</form>');
	jQuery('#temp_form').remove();
	jQuery('body').append(form.join("\n"));
	jQuery('#temp_form').submit();
};

var doDelete	= function( post_data ){
	var action_uri	= jQuery('#edit_form').attr('action') + 'delete';
	if(confirm( '确定要进行当前操作吗?' ) == true){
		jQuery.post(
			action_uri,
			post_data,
			function(json){
				if(chkAjaxRsp( json ) == true ){
					alert('操作成功.');
					jQuery('#search_form').submit();
				}
			},
			'json'
		);
	};
};
var pageList	= function( option ){
	var form		= jQuery( this );
	var container	= jQuery( 'table>tbody' );
	var page_tag	= jQuery( '.js_paging' );
	var makeList	= function( container, lists ){};

	var pageBar	= function( total ){
		jQuery(form).find('[name="total"]').val( total );
		
		var sch_htm	= jQuery(form).children().clone(true);
		var pag_now	= parseInt(jQuery(form).find('[name="page"]').val(),10);
		var pag_row	= parseInt(jQuery(form).find('[name="rows"]').val(),10);
		var pag_siz	= parseInt(jQuery(form).find('[name="size"]').val(),10);
		var pag_tot	= Math.ceil( total / pag_row );		
		var pag_bas	= pag_siz * Math.ceil( pag_now / pag_siz );
		pag_bas	= pag_bas < pag_tot ? pag_bas : pag_tot;

		var tag = [];
		tag.push('<span>共' + pag_tot + '页/' + total + '个结果.</span>');
		if( pag_bas > 0 ){
			var pag_prv = pag_now - 1;
			var pag_nxt = pag_now + 1;
			tag.push('<a href="javascript:void(0)" class="js_page_list_i" data-page="1">第一页</a>');
			if( pag_prv > 0 ) tag.push('<a href="javascript:void(0)" class="js_page_list_i" data-page="' + pag_prv + '">上一页</a>');
			for(var i = 1; i <= pag_bas; i++){
				if( pag_now == i ) tag.push('<a href="javascript:void(0)" class="js_page_list_i on" data-page="' + i + '">' + i + '</a>');
				else tag.push('<a href="javascript:void(0)" class="js_page_list_i" data-page="' + i + '">' + i + '</a>');
			}
			if( pag_nxt <= pag_tot ) tag.push('<a href="javascript:void(0)" class="js_page_list_i" data-page="' + pag_nxt + '">下一页</a>');
			tag.push('<a href="javascript:void(0)" class="js_page_list_i" data-page="' + pag_tot + '">最后页</a>');
			
			tag.push('第<input type="text" name="goPage" value="' + pag_now + '" size="3" />页');
			tag.push('<input type="button" value="跳转" class="js_page_list_i" />');
		}
		jQuery(page_tag).html(tag.join("\n"));
		
		jQuery('.js_page_list_i').click(function(){
			var page	= parseInt(jQuery(this).attr('data-page'),10);
			if(jQuery(this).attr('type') == 'button') var page	= parseInt(jQuery(this).prev('[name="goPage"]').val(),10);
			
			jQuery(form).html( sch_htm );
			
			var total	= parseInt(jQuery(form).find('[name="total"]').val(),10);
			var rows	= parseInt(jQuery(form).find('[name="rows"]').val(),10);
			var totpag	= Math.ceil( total / rows );
			if( page > totpag ) page = totpag;
			if( page < 1 || isNaN( page ) == true ) page = 1;

			jQuery(form).find('[name="page"]').val( page );
			jQuery(form).submit();			
		});
	};
	
	
	var init	= function(option){
		for(var i in option ){
			switch( i ){
				case 'container':
					container	= option[i];
					break;
				case 'makeList':
					makeList	= option[i];
					break;
			}
		}
	};
	
	jQuery(container).parent().find('[name="checkall"]').click(function(){
		var checked	= jQuery(this).get(0).checked;
		jQuery(container).find(':checkbox').each(function(){
			jQuery(this).get(0).checked = checked;
		});
	});
	
	jQuery(form).find(':submit').click(function(){
		jQuery('[name=total]').val(0);
		jQuery('[name=page]').val(1);
	});
	
	jQuery(form).submit(function(){
		init( option );
		if(jQuery(this).attr('method') == 'get'){
			jQuery('body').mask('loading......');
			jQuery.get(jQuery(this).attr('action'),jQuery(this).serialize(),function(json){			
				jQuery('body').unmask();
				if(chkAjaxRsp(json))
				{
					makeList( container, json);
					pageBar(json.total);
				}
			},'json');
		}else{
			jQuery('body').mask('loading......');
			jQuery.post(jQuery(this).attr('action'),jQuery(this).serialize(),function(json){			
				jQuery('body').unmask();
				if(chkAjaxRsp(json))
				{
					makeList( container, json);
					pageBar(json.total);
				}
			},'json');			
		}
	});
};
jQuery.fn.extend({
	pageList : pageList
});