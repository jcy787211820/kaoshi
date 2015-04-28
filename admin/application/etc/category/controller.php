<?php
/**
 * area manager
 */
includeTraits('Login');
includeTraits('Permission');
use \core\Go AS Go;
use \core\Controller AS Controller;
class Category extends Controller
{
	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();

		$this->adminCheckLogin();
		$this->checkPermission();
	}

	/**
	 * @view
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
		$assign_data = array('error' => TRUE );

		/*
		 * load class
		 */
		$category_manager	= Go::manager('Category');

		/*
		 * get post data
		 */
		$category_parent_code			= $category_manager->validation( 'category_parent_code',			initGet('category_parent_code'));
		$category_study_data_flag	= $category_manager->validation( 'category_study_data_flag',	initGet('category_study_data_flag','F'));
		$category_news_flag				= $category_manager->validation( 'category_news_flag',				initGet('category_news_flag','F'));
		$category_exercise_flag			= $category_manager->validation( 'category_exercise_flag',			initGet('category_exercise_flag','F'));

		/*
		 * load area data
		 */
		$where		= array();
		if( $category_study_data_flag == 'T' ) $where[]	= "`category_study_data_flag` = 'T'";
		if( $category_news_flag == 'T' ) $where[]	= "`category_news_flag` = 'T'";
		if( $category_exercise_flag == 'T' ) $where[]	= "`category_exercise_flag` = 'T'";
		$lists		= $category_manager->loadByParentCode( $category_parent_code, $where );

		$category	= empty( $category_parent_code ) ? get_object_vars( $category_manager ) : $category_manager->loadByCode( $category_parent_code );

		/*
		 * assign data
		 */
		$assign_data['lists']	= $lists;
		$assign_data['category']= $category;
		$assign_data['error']	= FALSE;

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
		$category_parent_code	= NULL;

		/*
		 * load class
		*/
		$category_manager	= Go::manager('Category');

		/*
		 * post data
		*/
		$category	= array();
		switch( initPost('action') )
		{
			case 'edit':
				$category_seq	= $category_manager->validation( 'category_seq',	initPost('category_seq'));
				$category		= $category_manager->loadBySeq( $category_seq );
				break;
			case 'add':
				$category_parent_code	= $category_manager->validation( 'category_parent_code',	initPost('category_parent_code'));
			default:
				$category	= get_object_vars( $category_manager );
		}

		/*
		 * format
		 */
		$category							= $category_manager->format( $category );

		/*
		 * set assign data
		 */
		$assign_data['category']			= $category;
		$assign_data['category_parent_code']= $category_parent_code;

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
		$category_manager			= Go::manager('Category');

		/*
		 * post data
		 */
		$category_parent_code							= $category_manager->validation( 'category_parent_code',		initPost('category_parent_code'));
		$category_manager->category_name				= $category_manager->validation( 'category_name',				initPost('category_name'));
		$category_manager->category_description			= $category_manager->validation( 'category_description',		initPost('category_description'));
		$category_manager->category_use_flag			= $category_manager->validation( 'category_use_flag',			initPost('category_use_flag'));
		$category_manager->category_study_data_flag	= $category_manager->validation( 'category_study_data_flag',initPost('category_study_data_flag'));
		$category_manager->category_news_flag			= $category_manager->validation( 'category_news_flag',			initPost('category_news_flag'));
		$category_manager->category_exercise_flag		= $category_manager->validation( 'category_exercise_flag',		initPost('category_exercise_flag'));


		/*
		 * process
		 */
		$assign_data['error']	=	$category_manager->insert( $category_parent_code ) !== TRUE;

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
		$category_manager			= Go::manager('Category');

		/*
		 * post data
		 */
		$category_manager->category_seq					= $category_manager->validation( 'category_seq',				initPost('category_seq'));
		$category_manager->category_name				= $category_manager->validation( 'category_name',				initPost('category_name'));
		$category_manager->category_description			= $category_manager->validation( 'category_description',		initPost('category_description'));
		$category_manager->category_use_flag			= $category_manager->validation( 'category_use_flag',			initPost('category_use_flag'));
		$category_manager->category_study_data_flag	= $category_manager->validation( 'category_study_data_flag',initPost('category_study_data_flag'));
		$category_manager->category_news_flag			= $category_manager->validation( 'category_news_flag',			initPost('category_news_flag'));
		$category_manager->category_exercise_flag		= $category_manager->validation( 'category_exercise_flag',		initPost('category_exercise_flag'));

		/*
		 * process
		 */
		$assign_data['error']	= $category_manager->update() !== TRUE;

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
		$category_manager	= Go::manager('Category');

		/*
		 * post data
		 */
		$category_manager->category_seq	= $category_manager->validation( 'category_seq',	initPost('category_seq'));

		/*
		 * process
		*/
		$assign_data['error']	= $category_manager->delete() !== TRUE;

		/*
		 * echo
		 */
		echo json_encode($assign_data);
	}
}
