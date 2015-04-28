jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var permission_seq			= json.lists[i].permission_seq;
				var permission_name			= json.lists[i].permission_name;
				var permission_action		= json.lists[i].permission_action;
				var permission_edit_time_vm	= json.lists[i].permission_edit_time_vm;
				var permission_edit_id		= json.lists[i].permission_edit_id;
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="permission_seq[]" id="permission_seq_' + i + '" value="' + permission_seq + '" />' +
							'<label for="permission_seq_' + i + '">' + permission_seq + '</label>' +
						'</td>' +
						'<td align="center">' + permission_name + '</td>' +
						'<td>' + permission_action + '</td>' +
						'<td align="center">' + permission_edit_time_vm + '</td>' +
						'<td align="center">' + permission_edit_id + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({permission_seq:' + permission_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
							'<a title="移除" href="javascript:void(0)" onclick="doDelete({permission_seq:' + permission_seq + '})">' +
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