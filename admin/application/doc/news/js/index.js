jQuery(document).ready(function(){
	jQuery('#search_form').pageList({
		'makeList'	: function(container, json){
			var tag = [];
			for(var i in json.lists ){
				var news_seq			= json.lists[i].news_seq;
				var area_code			= json.lists[i].area_code;
				var area_name			= json.lists[i].area_name;
				var news_title			= json.lists[i].news_title;
				var news_from			= json.lists[i].news_from;
				var news_read_count		= json.lists[i].news_read_count;
				var news_edit_time_vm	= json.lists[i].news_edit_time_vm;
				var news_edit_id		= json.lists[i].news_edit_id;
				tag.push(	
					'<tr>' +
						'<td>' +
							'<input type="checkbox" name="news_seq[]" id="news_seq_' + i + '" value="' + news_seq + '" />' +
							'<label for="news_seq_' + i + '">' + news_seq + '</label>' +
						'</td>' +
						'<td align="center"><nobr>[' + area_code + ']<br/>' + area_name + '</nobr></td>' +
						'<td align="center">' + news_title + '</td>' +
						'<td>' + news_from + '</td>' +
						'<td align="center">' + news_read_count + '</td>' +
						'<td align="center">' + news_edit_time_vm + '</td>' +
						'<td align="center">' + news_edit_id + '</td>' +
						'<td align="center">' + 
							'<a title="编辑" href="javascript:void(0)" onclick="doEdit({news_seq:' + news_seq + '})" >'+
								'<img src="/layout/images/icons/icon_edit.gif" border="0" height="16" width="16">' +
							'</a>' +
							'<a title="移除" href="javascript:void(0)" onclick="doDelete({news_seq:' + news_seq + '})">' +
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