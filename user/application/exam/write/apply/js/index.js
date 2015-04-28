/**
 * Init vars
 */
var WRITE_EXAM_APPLY_STATUS_AGREE	= '<?=\manager\WriteExamApply::WRITE_EXAM_APPLY_STATUS_AGREE?>';
var WRITE_EXAM_APPLY_STATUS			= '<?=$write_exam_apply["write_exam_apply_status"]?>';
/**
 * Set cert type
 */
var setCertType	= function(select){
	var write_exam_apply_cert_type	= jQuery(select).val();
	jQuery('#write_exam_apply_cert_type').val( write_exam_apply_cert_type );
};

jQuery(document).ready(function(){
	if( WRITE_EXAM_APPLY_STATUS >= WRITE_EXAM_APPLY_STATUS_AGREE ){
		jQuery('#write_exam_apply_form').find('input,select,textarea').attr('disabled','disabled');
	};
	
	jQuery('#left_menu_apply').addClass('active');
});