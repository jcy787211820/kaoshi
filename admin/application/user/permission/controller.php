<?php
/**
 * permission manager
 */
includeTraits('Login');
includeTraits('Permission');

use \core\Go AS Go;
use \core\Controller AS Controller;
use \core\Database AS Database;
use core\CException;
class Permission extends Controller
{
	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->adminCheckLogin();
		$this->checkPermission();
	}

	/**
	 * view page
	 * Enter description here ...
	 */
	public function index()
	{
		/*
		 * var data
		 */
		$assign_data		= array();

		/*
		 * view
		 */
		parent::view( $assign_data );
	}

	/**
	 * get list data
	 */
	public function listed()
	{
		/*
		 * var data
		 */
		$assign_data = array('error'=>TRUE);

		/*
		 * load class
		 */
		$permission_manager	= Go::manager('Permission');

		/*
		 * post data
		 */
		$page	= $permission_manager->validation( 'vdt_page',		initGet('page') );
		$total	= $permission_manager->validation( 'vdt_total',		initGet('total') );
		$rows	= $permission_manager->validation( 'vdt_rows',		initGet('rows') );

		/*
		 * check
		 */
		if( $page * $rows > $total && $page > 1 ) throw new AmException('Param Error.');

		/*
		 * get lists
		 */
		$lists			= $permission_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $permission_manager->preTotal();

		/*
		 * assign data
		 */
		$assign_data['total']	= $total;
		$assign_data['lists']	= $lists;
		$assign_data['error']	= FALSE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * edit form
	 * Enter description here ...
	 * @param (int) $user_group_seq
	 */
	public function form()
	{
		/*
		 * var data
		 */
		$assign_data		= array('error' => TRUE);
		$user_group_names	= array();
		$user_level_names	= array();

		/*
		 * load class
		 */
		$permission_manager			= Go::manager('Permission');
		$user_group_manager			= Go::manager('UserGroup');
		$user_level_manager			= Go::manager('UserLevel');
		$user_manager				= Go::manager('User');

		/*
		 * post data
		 */
		switch(initPost('action'))
		{
			case 'edit':
				$permission_seq		= $permission_manager->validation( 'permission_seq',		initPost('permission_seq'));
				$permission			= $permission_manager->loadBySeq( $permission_seq );
				break;
			case 'add':
			default:
				$permission	= get_object_vars( $permission_manager );
		}

		/*
		 * get permission data keys
		 */
		$permission_data_keys	= $permission_manager->getPermissionDataKeys();

		/*
		 * set permission data name values
		 */

		$permission	= $permission_manager->format( $permission );
		foreach( $permission['permission_data_vm'] AS $type => $permission_data_vm )
		{
			switch( $type )
			{
				case $permission_manager::PERMISSION_BASE_USER_GROUP:
					$user_group_names	= $user_group_manager->loadNameByCodes( $permission_data_vm['data'] );
					break;
				case $permission_manager::PERMISSION_BASE_USER_LEVEL:
					$user_level_names	= $user_level_manager->loadNames( $permission_data_vm['data'] );
					break;
				case $permission_manager::PERMISSION_BASE_USER:
					break;
				case $permission_manager::PERMISSION_BASE_IP:
					break;
				default:
					throw new CException('Permission data Error.');
			}
		}

		/*
		 * set assign data
		 */
		$assign_data['permission']				= $permission;
		$assign_data['user_group_names']		= $user_group_names;
		$assign_data['user_level_names']		= $user_level_names;
		$assign_data['permission_data_keys']	= $permission_data_keys;

		/*
		 * view
		 */
		parent::view( $assign_data );
	}
	/**
	 * insert
	 */
	public function insert()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$permission_manager			= Go::manager('Permission');

		/*
		 * post data
		 */
		$permission_manager->permission_name			= $permission_manager->validation( 'permission_name',	initPost('permission_name'));
		$permission_manager->permission_action			= $permission_manager->validation( 'permission_action',	initPost('permission_action'));
		$permission_manager->permission_data			= $permission_manager->validation( 'permission_data_vm',initPost('permission_data', array()));

		/*
		 * process
		 */
		$assign_data['error']		= $permission_manager->insert() !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * update
	 */
	public function update()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$permission_manager			= Go::manager('Permission');

		/*
		 * post data
		 */
		$permission_manager->permission_seq				= $permission_manager->validation( 'permission_seq',	initPost('permission_seq') );
		$permission_manager->permission_name			= $permission_manager->validation( 'permission_name',	initPost('permission_name') );
		$permission_manager->permission_action			= $permission_manager->validation( 'permission_action',	initPost('permission_action') );
		$permission_manager->permission_data			= $permission_manager->validation( 'permission_data_vm',initPost('permission_data', array()));

		/*
		 * process
		 */
		$assign_data['error']		= $permission_manager->update() !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * delete
	 */
	public function delete()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$permission_manager			= Go::manager('Permission');

		/*
		 * post data
		 */
		$permission_seqs	= $permission_manager->validation( 'permission_seq',	(array) initPost('permission_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $permission_manager->delete( $permission_seqs ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	/**
	 * set where by this listed
	 */
	private function _mkWhere()
	{
		/*
		 * var data
		 */
		$where	= array();

		/*
		 * load class
		*/
		$permission_manager			= Go::manager('Permission');

		/*
		 * post data
		 */
		$permission_name	= initGet('permission_name');

		/*
		 * make where value
		 */
		if( $permission_name != FALSE ) $where[]	= '`permission_name` = ' . Database::escapeString($permission_manager->validation( 'permission_name', $permission_name ));
		$where[]	= '`permission_delete_flag` = ' . Database::escapeString('F');

		/*
		 * return
		 */
		return $where;
	}
}
