<?php
/**
 * user Level manager
 */
includeTraits('Login');
includeTraits('Permission');
use \core\Go AS Go,
	\core\Controller AS Controller,
	\core\Database AS Database,
	\core\MsgException As MsgException;
class Level extends Controller
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
		$user_level_manager	= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$page	= $user_level_manager->validation( 'vdt_page',		initGet('page') );
		$total	= $user_level_manager->validation( 'vdt_total',		initGet('total') );
		$rows	= $user_level_manager->validation( 'vdt_rows',		initGet('rows') );

		/*
		 * check
		 */
		if( $page * $rows > $total + $rows && $page > 1 ) throw new MsgException('Param Error.');

		/*
		 * get lists
		 */
		$lists			= $user_level_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $user_level_manager->preTotal();

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
		$assign_data	= array();

		/*
		 * load class
		 */
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$user_level					= array();
		switch( initPost('action') )
		{
			case 'edit':
				$user_level_seq	= $user_level_manager->validation( 'user_level_seq',			initPost('user_level_seq'));
				$user_level		= $user_level_manager->loadBySeq( $user_level_seq );
				break;
			case 'add':
			default:
				$user_level	= get_object_vars( $user_level_manager );
		}

		/*
		 * get user level type
		 */
		$user_level_types	= $user_level_manager->getUserLevelTypes();

		/*
		 * set assign data
		 */
		$user_level							= $user_level_manager->format( $user_level );
		$assign_data['user_level']			= $user_level;
		$assign_data['user_level_types']	= $user_level_types;

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
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$user_level_manager->user_level_type			= $user_level_manager->validation( 'user_level_type',			initPost('user_level_type'));
		$user_level_manager->user_level_name			= $user_level_manager->validation( 'user_level_name',			initPost('user_level_name') );
		$user_level_manager->user_level_min_empiric		= $user_level_manager->validation( 'user_level_min_empiric',	initPost('user_level_min_empiric') );
		$user_level_manager->user_level_use_flag		= $user_level_manager->validation( 'user_level_use_flag',		initPost('user_level_use_flag') );
		$user_level_manager->user_level_description		= $user_level_manager->validation( 'user_level_description',	initPost('user_level_description') );

		/*
		 * process
		 */
		$assign_data['error']		= $user_level_manager->insert() !== TRUE;

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
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$user_level_manager->user_level_seq				= $user_level_manager->validation( 'user_level_seq',			initPost('user_level_seq'));
		$user_level_manager->user_level_type			= $user_level_manager->validation( 'user_level_type',			initPost('user_level_type'));
		$user_level_manager->user_level_name			= $user_level_manager->validation( 'user_level_name',			initPost('user_level_name') );
		$user_level_manager->user_level_min_empiric		= $user_level_manager->validation( 'user_level_min_empiric',	initPost('user_level_min_empiric') );
		$user_level_manager->user_level_use_flag		= $user_level_manager->validation( 'user_level_use_flag',		initPost('user_level_use_flag') );
		$user_level_manager->user_level_description		= $user_level_manager->validation( 'user_level_description',	initPost('user_level_description') );

		/*
		 * process
		 */
		$assign_data['error']		= $user_level_manager->update() !== TRUE;

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
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$user_level_seqs	= $user_level_manager->validation( 'user_level_seq',	(array) initPost('user_level_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $user_level_manager->delete( $user_level_seqs ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * use flag T
	 */
	public function used()
	{
		/*
		 * var data
		*/
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		*/
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		*/
		$user_level_seqs	= $user_level_manager->validation( 'user_level_seq',	initPost('user_level_seq'), TRUE );

		/*
		 * process
		*/
		$assign_data['error']	= $user_level_manager->used( $user_level_seqs ) !== TRUE;

		/*
		 * echo
		*/

		echo json_encode( $assign_data );
	}
	/**
	 * use flag F
	 */
	public function unused()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$user_level_manager			= Go::manager('UserLevel');

		/*
		 * post data
		 */
		$user_level_seqs	= $user_level_manager->validation( 'user_level_seq',	initPost('user_level_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $user_level_manager->unused( $user_level_seqs ) !== TRUE;

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
		 * post data
		 */
		$user_level_name	= initGet('user_level_name');

		/*
		 * make where value
		 */
		if($user_level_name != FALSE ) $where[]	= '`user_level_name` = ' . Database::escapeString( $user_level_name );

		/*
		 * return
		 */
		return $where;
	}
}
