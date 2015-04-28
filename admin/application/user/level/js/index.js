jQuery(document).ready(function(){
	jQuery('#form_search').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var user_level_seq			= json.lists[i].user_level_seq;
				var user_level_type_vm		= json.lists[i].user_level_type_vm;
				var user_level_name			= json.lists[i].user_level_name;
				var user_level_use_flag		= json.lists[i].user_level_use_flag;
				var user_level_description	= json.lists[i].user_level_description;
				var user_level_min_empiric	= json.lists[i].user_level_min_empiric;
				var user_level_edit_date_vm	= json.lists[i].user_level_edit_date_vm;
				var user_level_edit_id		= json.lists[i].user_level_edit_id;
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="user_level_seq[]" id="user_level_seq_' + i + '" value="' + user_level_seq + '" />' +
							'<label for="user_level_seq_' + i + '">' + user_level_seq + '</label>' +
						'</td>' +
						'<td align="center">' + user_level_type_vm + '</td>' +
						'<td><span name="user_level_name">' + user_level_name + '</span></td>' +
						'<td align="center">' + user_level_use_flag + '</td>' +
						'<td>' + user_level_description + '</td>' +
						'<td align="right">' + user_level_min_empiric + '</td>' +
						'<td align="center">' + user_level_edit_date_vm + '</td>' +
						'<td>' + user_level_edit_id + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({user_level_seq:' + user_level_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
							'<a title="移除" href="javascript:void(0)" onclick="doDelete({user_level_seq:' + user_level_seq + '})">' +
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