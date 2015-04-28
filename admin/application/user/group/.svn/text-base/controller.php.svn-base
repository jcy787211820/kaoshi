<?php
/**
 * user group manager
 */
includeTraits('Login');
includeTraits('Permission');
use \core\Go AS Go;
use \core\Controller AS Controller;
class Group extends Controller
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
		$assign_data	= array();

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
		$assign_data = array('error' => TRUE );

		/*
		 * load class
		 */
		$user_group_manager	= Go::manager('UserGroup');

		/*
		 * get post data
		 */
		$user_group_parent_code			= $user_group_manager->validation( 'user_group_parent_code',	initGet('user_group_parent_code'));

		/*
		 * load user group data
		 * @var $lists
		 */
		$lists	= $user_group_manager->loadByParentCode( $user_group_parent_code );

		/*
		 * get parent user group
		 */
		$user_group	= empty( $user_group_parent_code ) ? get_object_vars( $user_group_manager ) : $user_group_manager->loadByCode( $user_group_parent_code );

		/*
		 * assign data
		 */
		$assign_data['lists']		= $lists;
		$assign_data['user_group']	= $user_group;
		$assign_data['error']		= FALSE;

		/*
		 * echo data
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
		$assign_data			= array();
		$user_group_parent_code	= NULL;

		/*
		 * load class
		 */
		$user_group_manager	= Go::manager('UserGroup');

		/*
		 * post data
		 */
		switch(initPost('action'))
		{
			case 'edit':
				$user_group_seq	= $user_group_manager->validation( 'user_group_seq',	initPost('user_group_seq'));
				$user_group		= $user_group_manager->loadBySeq( $user_group_seq );
				break;
			case 'add':
				$user_group_parent_code	= $user_group_manager->validation( 'user_group_parent_code',	initPost('user_group_parent_code'));
			default:
			$user_group	= get_object_vars( $user_group_manager );
		}

		/*
		 * format
		 */
		$user_group					= $user_group_manager->format( $user_group );

		/*
		 * set assign data
		 */
		$assign_data['user_group']				= $user_group;
		$assign_data['user_group_parent_code']	= $user_group_parent_code;

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
		$user_group_manager			= Go::manager('UserGroup');

		/*
		 * post data
		 */
		$user_group_parent_code						= $user_group_manager->validation( 'user_group_parent_code',	initPost('user_group_parent_code'));
		$user_group_manager->user_group_name		= $user_group_manager->validation( 'user_group_name',			initPost('user_group_name'));
		$user_group_manager->user_group_description	= $user_group_manager->validation( 'user_group_description',	initPost('user_group_description'));
		$user_group_manager->user_group_use_flag	= $user_group_manager->validation( 'user_group_use_flag',		initPost('user_group_use_flag'));

		/*
		 * process
		 */
		$assign_data['error']			= $user_group_manager->insert( $user_group_parent_code ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode($assign_data);
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
		$user_group_manager			= Go::manager('UserGroup');

		/*
		 * post data
		 */
		$user_group_manager->user_group_name			= $user_group_manager->validation( 'user_group_name',			initPost('user_group_name') );
		$user_group_manager->user_group_description		= $user_group_manager->validation( 'user_group_description',	initPost('user_group_description') );
		$user_group_manager->user_group_use_flag		= $user_group_manager->validation( 'user_group_use_flag',		initPost('user_group_use_flag') );
		$user_group_manager->user_group_seq				= $user_group_manager->validation( 'user_group_seq',			initPost('user_group_seq') );

		/*
		 * process
		 */
		$assign_data['error']			= $user_group_manager->update() !== TRUE;

		/*
		 * echo
		 */
		echo json_encode($assign_data);
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
		$user_group_manager			= Go::manager('UserGroup');

		/*
		 * post data
		 */
		$user_group_seqs		= $user_group_manager->validation( 'user_group_seq',	(array) initPost('user_group_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= $user_group_manager->delete( $user_group_seqs ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
}
