<?php
/**
 * user Info manager
 */
includeTraits('Login');
includeTraits('User');
includeTraits('Permission');
use Core\MsgException;
use \core\Go AS Go;
use \core\Controller AS Controller;
use \core\Database AS Database;

class Info extends Controller
{
	use \traits\login, \traits\user, \traits\permission;

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
		$user_manager	= Go::manager('User');

		/*
		 * post data
		 */
		$page	= $user_manager->validation( 'vdt_page',		initGet('page') );
		$total	= $user_manager->validation( 'vdt_total',		initGet('total') );
		$rows	= $user_manager->validation( 'vdt_rows',		initGet('rows') );


		/*
		 * get lists
		 */
		$lists			= $user_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $user_manager->preTotal();

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
		$user_infos		= array();

		/*
		 * load class
		 */
		$user_manager			= Go::manager('User');
		$user_group_manager		= Go::manager('UserGroup');
		$area_manager			= Go::manager('Area');
		$user_info_manager		= Go::manager('UserInfo');


		/*
		 * post data
		 */
		switch( initPost('action') )
		{
			case 'edit':
				$user_seq		= $user_manager->validation( 'user_seq',	initPost('user_seq'));
				$user			= $user_manager->loadBySeq( $user_seq );

				$user_infos		= $user_info_manager->loadByUserId( $user['user_id'] );
				$user_infos		= $user_info_manager->resetListsKey( $user_infos, 'user_info_type', TRUE );
				break;
			case 'add':
			default:
				$user			= get_object_vars( $user_manager );
		}

		/*
		 * set data
		 */
		$user			= $user_manager->format( $user );
		$user_groups	= $user_group_manager->loadUsed();
		$areas			= $area_manager->loadAll();


		/*
		 * set user data
		 */
		$assign_data['user']	=  $user;

		/*
		 * get used groups
		 */
		$assign_data['user_groups']	= $user_groups;

		/*
		 * set area
		 */
		$assign_data['areas']	= $areas;

		/*
		 * get user info types
		 */
		$assign_data['user_info_types']		= $user_info_manager->getUserInfoTypes();
		$assign_data['user_info_manager']	= $user_info_manager;
		$assign_data['user_infos']			= $user_infos;

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
		$user_manager			= Go::manager('User');

		/*
		 * post data
		 */
		$user_manager->user_id				= $user_manager->validation( 'user_id',			initPost('user_id'));
		$user_manager->user_password		= $user_manager->validation( 'user_password',	initPost('user_password', FALSE, FALSE));
		$user_manager->user_active_flag		= $user_manager->validation( 'user_active_flag',initPost('user_active_flag'));
		$user_infos							= $this->_makeUserInfo(initPost('user_info'));

		/*
		 * init data
		 */
		$user_manager->user_admin_add_flag	= 'T';
		$user_manager->user_password		= $user_manager->encryptsPassword( $user_manager->user_password );

		/*
		 * process
		 */
		$assign_data['error']	= self::newUser( $user_manager, $user_infos ) !== TRUE;

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
		$user_manager		= Go::manager('User');

		/*
		 * post data
		 */
		$user_manager->user_seq			= $user_manager->validation( 'user_seq',			initPost('user_seq'));
		$user_manager->user_id			= $user_manager->validation( 'user_id',				initPost('user_id'));
		$user_manager->user_active_flag	= $user_manager->validation( 'user_active_flag',	initPost('user_active_flag'));
		$user_infos						= $this->_makeUserInfo(initPost('user_info'));

		/*
		 * process
		 */
		$assign_data['error']	= self::updateUser( $user_manager, $user_infos ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * update
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
		$user_manager			= Go::manager('User');

		/*
		 * post data
		 */
		$user_seqs	= $user_manager->validation( 'user_seq',	(array) initPost('user_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $user_manager->delete( $user_seqs ) !== TRUE;

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
		$user_manager			= Go::manager('User');

		/*
		 * post data
		 */
		$user_seqs	= (array) $user_manager->validation( 'user_seq',	initPost('user_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $user_manager->used( $user_seqs ) !== TRUE;

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
		$user_manager			= Go::manager('User');

		/*
		 * post data
		*/
		$user_seqs	= (array) $user_manager->validation( 'user_seq',	initPost('user_seq'), TRUE );

		/*
		 * process
		*/
		$assign_data['error']	= $user_manager->unused( $user_seqs ) !== TRUE;

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
		$user_manager		= Go::manager('User');

		/*
		 * post data
		 */
		$user_id			= initGet('user_id');

		/*
		 * make where value
		 */
		if( $user_id != FALSE ) $where[]	= '`user_id` = ' . Database::escapeString($user_manager->validation( 'user_id', $user_id ));

		/*
		 * return
		 */
		return $where;
	}

	/**
	 * set user infos
	 * @param (array) $user_infos
	 * @throws MsgException
	 */
	private function _makeUserInfo( $user_infos )
	{
		/*
		 * VAR
		 */
		$result = array();

		/*
		 * check
		 */
		if(empty( $user_infos ) == TRUE) return $result;
		if(!is_array( $user_infos )) throw new MsgException('数据错误【user info】');

		/*
		 * load class
		 */
		$user_info_manager			= Go::manager('UserInfo');

		/*
		 * set value
		 */
		foreach( $user_infos AS $key => $user_info_value_txt )
		{
			$user_info_values	= empty( $user_info_value_txt ) ? array() : explode( '_', $user_info_value_txt );
			$user_info_values	= array_unique( $user_info_values );

			if( $key == $user_info_manager::USER_INFO_TYPE_GROUP || $key == $user_info_manager::USER_INFO_TYPE_AREA )
			{
				foreach( $user_info_values AS $index => $user_info_value )
				{
					foreach( $user_info_values AS $chk_user_info_value )
					{
						if(stripos( $user_info_value, $chk_user_info_value ) === 0 && $user_info_value != $chk_user_info_value )
						{
							unset( $user_info_values[$index] );
							break;
						}
					}
				}
			}

			foreach( $user_info_values AS $user_info_value )
			{
				$user_info_manager->validationUserInfo( $key, $user_info_value );

				$result[]				= array(
					'user_info_type'	=> $key,
					'user_info_value'	=> $user_info_value,
				);
			}
		}

		/**
		 * check user group ,must be setlast depth
		 */

		/*
		 * return
		 */
		return $result;
	}
}
