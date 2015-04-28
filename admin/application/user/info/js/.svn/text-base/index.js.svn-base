jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var user_seq			= json.lists[i].user_seq;
				var user_id				= json.lists[i].user_id;
				var user_point			= json.lists[i].user_point;
				var user_cash			= json.lists[i].user_cash;
				var user_empiric_a		= json.lists[i].user_empiric_a;
				var user_empiric_b		= json.lists[i].user_empiric_b;
				var user_friend_count	= json.lists[i].user_friend_count;
				var user_blacklist_count= json.lists[i].user_blacklist_count;
				var user_active_flag	= json.lists[i].user_active_flag;
				
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="user_seq[]" id="user_seq_' + i + '" value="' + user_seq + '" />' +
							'<label for="user_seq_' + i + '">' + user_seq + '</label>' +
						'</td>' +
						'<td align="center"><span name="user_id">' + user_id + '</span></td>' +
						'<td align="right">' + user_point + '</td>' +
						'<td align="right">' + user_cash + '</td>' +
						'<td align="right">' + user_empiric_a + '/' + user_empiric_b + '</td>' +
						'<td align="right">' + user_friend_count + '</td>' +
						'<td align="right">' + user_blacklist_count + '</td>' +
						'<td align="center">' + user_active_flag + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({user_seq:' + user_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
							'<a title="移除" href="javascript:void(0)" onclick="doDelete({user_seq:' + user_seq + '})">' +
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