var doLogout	= function(){
	if(confirm( '确定要退出登录吗?' ) == true){
		jQuery.post(
			'/login/logout',
			function(json){
				if(chkAjaxRsp( json ) == true ){
					parent.location.href	= '/login';
				}
			},
			'json'
		);
	};
};
