var collapse	= function(tag){
	var li		= tag;
	if(jQuery(tag).attr('name') == 'menu-collapse-all'){
		li = jQuery('[name="menu"]');
		if(jQuery(tag).hasClass('collapse') == true){
			jQuery(tag).removeClass('collapse').addClass('explode');
			jQuery(li).removeClass('collapse').addClass('explode');
			jQuery(li).next('ul').show();
		}else{
			jQuery(tag).removeClass('explode').addClass('collapse');
			jQuery(li).removeClass('explode').addClass('collapse');
			jQuery(li).next('ul').hide();	
		}
		
	}else{
		if( jQuery(li).hasClass('collapse') == true ){
			jQuery(li).removeClass('collapse').addClass('explode');
			jQuery(li).next('ul').show();
		}else{
			jQuery(li).removeClass('explode').addClass('collapse');
			jQuery(li).next('ul').hide();	
		}			
	}
};

jQuery(document).ready(function(){
	jQuery('#main-frame',parent.document).attr(
		'src', jQuery('.menu-item:eq(0)>a').attr('href')
	);
});
