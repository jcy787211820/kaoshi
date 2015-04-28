<?php
namespace traits;
use	\core\Go AS Go,
	\core\MsgException AS MsgException,
	\core\CException AS CException;
trait Permission
{
	/**
	 * check permission
	 * @param array $user
	 */
	public function checkPermission(array $user = NULL )
	{
		/*
		 * load user data
		 */
		if( $user == NULL )
		{
			if(!empty( $_SESSION['user_id'] ))
			{
				$user_manager	= GO::manager('User');
				$user			= $user_manager->loadByUsrId( $_SESSION['user_id'] );
			}
		}

		/*
		 * load class
		 */
		$permission_manager	= Go::manager('Permission');
		$user_info_manager	= Go::manager('UserInfo');
		$user_level_manager	= Go::manager('UserLevel');

		/*
		 * load permission actions
		 */
		$permission_actions		= array();
		$request_path		=  trim(parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), DIRECTORY_SEPARATOR );
		$parse_paths		= explode( DIRECTORY_SEPARATOR, $request_path );
		$permission_action	= 'http://' . $_SERVER['SERVER_NAME'];
		foreach( $parse_paths AS $parse_path )
		{
			$permission_action		.=DIRECTORY_SEPARATOR . $parse_path;
			$permission_actions[]	= $permission_action;
		}
		$permissions	= $permission_manager->loadByActions( $permission_actions );

		/*
		 * check
		 */
		if(!empty( $permissions ))
		{
			$permissions	= array_map(array( $permission_manager, 'format' ), $permissions );
			foreach( $permissions AS $permission )
			{
				foreach( $permission['permission_data_vm'] AS $type => $permission_data_vm )
				{
					switch( $type )
					{
						case $permission_manager::PERMISSION_BASE_USER_GROUP:
							if(empty( $user )) throw new CException('你没有权限.');
							$permission_codes				= $permission_data_vm['data'];
							$user_group_in_permission_codes	= $user_info_manager->getUserGroupCodes( $user['user_id'], $permission_codes );
							if(
									( $permission_data_vm['is_out_flag'] == 'F' && empty( $user_group_in_permission_codes ))
								||	( $permission_data_vm['is_out_flag'] == 'T' && !empty( $user_group_in_permission_codes ))
							){
								throw new MsgException('你没有权限.');
							}
							break;
						case $permission_manager::PERMISSION_BASE_USER_LEVEL:
							if(empty( $user )) throw new CException('你没有权限.');
							$user_answer_level_seq	= $user_level_manager->getAnsUserLevelSeq( $user['user_empiric_a'] );
							$user_ask_level_seq		= $user_level_manager->getAskUserLevelSeq( $user['user_empiric_b'] );
								if(
										( 		$permission_data_vm['is_out_flag'] == 'F'
											&&	!in_array( $user_answer_level_seq,  $permission_data_vm['data'] )
											&&	!in_array( $user_ask_level_seq,  $permission_data_vm['data'] )
										)
									||
										(		$permission_data_vm['is_out_flag'] == 'T'
											&& (
														in_array( $user_answer_level_seq,  $permission_data_vm['data'] )
													||
														in_array( $user_ask_level_seq,  $permission_data_vm['data'] )
												)
										)
								){
									throw new MsgException('你没有权限.');
								}
							break;
						case $permission_manager::PERMISSION_BASE_USER:
								if(empty( $user )) throw new CException('你没有权限.');
								if(
										( $permission_data_vm['is_out_flag'] == 'F' && !in_array( $user['user_id'],  $permission_data_vm['data'] ))
									||	( $permission_data_vm['is_out_flag'] == 'T' && in_array( $user['user_id'],  $permission_data_vm['data'] ))
								){
									throw new MsgException('你没有权限.');
								}
							break;
						case $permission_manager::PERMISSION_BASE_IP:
								if(
										( $permission_data_vm['is_out_flag'] == 'F' && !in_array( $_SERVER['REMOTE_ADDR'], $permission_data_vm['data'] ))
									||	( $permission_data_vm['is_out_flag'] == 'T' && in_array( $_SERVER['REMOTE_ADDR'], $permission_data_vm['data'] ))
								){
									throw new MsgException('你没有权限.');
								}
							break;
						default:
							throw new CException('SYSTEM ERROR.');
					}
				}
			}
		}
	}
}