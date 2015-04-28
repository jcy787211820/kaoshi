jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var write_exam_apply_seq			= json.lists[i].write_exam_apply_seq;
				var user_id							= json.lists[i].user_id;
				var write_exam_apply_cert_type		= json.lists[i].write_exam_apply_cert_type;
				var write_exam_apply_cert_id		= json.lists[i].write_exam_apply_cert_id;
				var write_exam_apply_status_vw		= json.lists[i].write_exam_apply_status_vw;
				var write_exam_apply_edit_time_vw	= json.lists[i].write_exam_apply_edit_time_vw;
				var write_exam_apply_edit_id		= json.lists[i].write_exam_apply_edit_id;
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="write_exam_apply_seq[]" id="write_exam_apply_seq_' + i + '" value="' + write_exam_apply_seq + '" />' +
							'<label for="write_exam_apply_seq_' + i + '">' + write_exam_apply_seq + '</label>' +
						'</td>' +
						'<td align="center">' + user_id + '</td>' +
						'<td align="center">' + write_exam_apply_cert_type + '</td>' +
						'<td align="center">' + write_exam_apply_cert_id + '</td>' +
						'<td align="center">' + write_exam_apply_status_vw + '</td>' +
						'<td align="center">' + write_exam_apply_edit_time_vw + '</td>' +
						'<td align="center">' + write_exam_apply_edit_id + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({write_exam_apply_seq:' + write_exam_apply_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
						'</td>' +
					'</tr>'	
				);
			}
			jQuery(container).html(tag.join("\n"));
		}
	});
	jQuery('#search_form').submit();
});