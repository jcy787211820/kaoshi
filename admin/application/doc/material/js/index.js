jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var study_data_seq			= json.lists[i].study_data_seq;
				var study_data_title		= json.lists[i].study_data_title;
				var study_data_from			= json.lists[i].study_data_from;
				var study_data_read_count	= json.lists[i].study_data_read_count;
				var study_data_edit_time_vm	= json.lists[i].study_data_edit_time_vm;
				var study_data_edit_id		= json.lists[i].study_data_edit_id;
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="study_data_seq[]" id="study_data_seq_' + i + '" value="' + study_data_seq + '" />' +
							'<label for="study_data_seq_' + i + '">' + study_data_seq + '</label>' +
						'</td>' +
						'<td align="center">' + study_data_title + '</td>' +
						'<td>' + study_data_from + '</td>' +
						'<td align="center">' + study_data_read_count + '</td>' +
						'<td align="center">' + study_data_edit_time_vm + '</td>' +
						'<td align="center">' + study_data_edit_id + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({study_data_seq:' + study_data_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
							'<a title="移除" href="javascript:void(0)" onclick="doDelete({study_data_seq:' + study_data_seq + '})">' +
								'<img src="/layout/images/icons/icon_drop.gif" border="0" width="16" height="16">' +
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