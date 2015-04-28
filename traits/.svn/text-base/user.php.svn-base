<?php
namespace traits;
use	\core\Go AS Go,
	\core\Database AS Database;
trait User
{
	/**
	 * insert new user
	 * @param \manager\User $user_manager
	 * @param array $user_infos
	 */
	public function newUser(\manager\User $user_manager, array $user_infos )
	{
		/*
		 * class
		 */
		$user_info_manager	= Go::manager('UserInfo');

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/

			$user_manager->insert();
			if(!empty( $user_infos ))
			{
				foreach( $user_infos AS &$user_info )
				{
					$user_info['user_id']		= $user_manager->user_id;
				}
				$user_info_manager->inserts( $user_infos );
			}

			/*****************/
			Database::commit();
			/*****************/
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}

		/*
		 * return
		 */
		return TRUE;
	}

	/**
	 * insert update user
	 * @param \manager\User $user_manager
	 * @param array $user_infos
	 */
	public function updateUser(\manager\User $user_manager, array $user_infos )
	{
		/*
		 * class
		 */
		$user_info_manager	= Go::manager('UserInfo');

		/*
		 * user infos
		 */
		$delete_user_info_seqs		= array();	//delete seq
		$tmp_user_infos				= $user_info_manager->loadByUserId( $user_manager->user_id );
		foreach( $tmp_user_infos AS $tmp_user_info )
		{
			$is_set		= FALSE;
			foreach( $user_infos AS $key => $user_info )
			{
				if(		$tmp_user_info['user_info_type'] == $user_info['user_info_type']
					&&	$tmp_user_info['user_info_value'] == $user_info['user_info_value']
				){
					$is_set	= TRUE;
					unset( $user_infos[$key] );
				}
			}
			if( $is_set == FALSE ) $delete_user_info_seqs[]	= $tmp_user_info['user_info_seq'];
		}

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/

			$user_manager->update();
			if(!empty( $user_infos ))
			{
				foreach( $user_infos AS &$user_info )
				{
					$user_info['user_id']		= $user_manager->user_id;
				}
				$user_info_manager->inserts( $user_infos );
			}
			if(!empty( $delete_user_info_seqs )) $user_info_manager->delete( $delete_user_info_seqs );

			/*****************/
			Database::commit();
			/*****************/
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}

		/*
		 * return
		 */
		return TRUE;
	}
}