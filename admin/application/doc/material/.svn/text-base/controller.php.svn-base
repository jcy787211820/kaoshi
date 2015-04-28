<?php
/**
 * Study data manager
 */
includeTraits('Login');
includeTraits('Permission');
includeTraits('StudyData');
use \core\Go AS Go;
use \core\Controller AS Controller;
use \core\Database AS Database;
class Material extends Controller
{
	use \traits\login,
		\traits\permission,
		\traits\studydata;

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
		$study_data_manager	= Go::manager('StudyData');

		/*
		 * post data
		 */
		$page	= $study_data_manager->validation( 'vdt_page',		initGet('page') );
		$total	= $study_data_manager->validation( 'vdt_total',		initGet('total') );
		$rows	= $study_data_manager->validation( 'vdt_rows',		initGet('rows') );

		/*
		 * check
		 */
		if( $page * $rows > $total && $page > 1 ) throw new AmException('Param Error.');

		/*
		 * get lists
		 */
		$lists			= $study_data_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $study_data_manager->preTotal();

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
		$study_data_manager					= Go::manager('StudyData');
		$category_manager					= Go::manager('Category');
		$category_map_study_data_manager	= Go::manager('CategoryMapStudyData');

		/*
		 * post data
		 */
		$study_data					= array();
		switch( initPost('action') )
		{
			case 'edit':
				$study_data_seq	= $study_data_manager->validation( 'study_data_seq',	initPost('study_data_seq'));
				$study_data		= $study_data_manager->loadBySeq( $study_data_seq );
				break;
			case 'add':
			default:
				$study_data		= get_object_vars( $study_data_manager );
		}
		$study_data							= $study_data_manager->format( $study_data );

		/*
		 * set categorys
		 */
		$categorys							= $category_manager->loadUsedAll('category_study_data_flag');

		$category_map_study_datas				= array();
		if( $study_data['study_data_seq'] > 0 )
		{
			$category_map_study_data_manager->setFields(array('category_code'));
			$category_map_study_datas	= $category_map_study_data_manager->loadByStudyDataSeq( $study_data['study_data_seq'] );
		}

		/*
		 * set assign data
		 */
		$assign_data['study_data']					= $study_data;
		$assign_data['categorys']					= $categorys;
		$assign_data['category_map_study_datas']	= $category_map_study_datas;

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
		$study_data_manager			= Go::manager('StudyData');

		/*
		 * post data
		 */
		$study_data_manager->study_data_title	= $study_data_manager->validation('study_data_title',	initPost('study_data_title'));
		$study_data_manager->study_data_from	= $study_data_manager->validation('study_data_from',	initPost('study_data_from'));
		$study_data_manager->study_data_content	= $study_data_manager->validation('study_data_content',	initPost('study_data_content'));
		$category_codes							= explode('_',(string)initPost('category_code_text'));

		/*
		 * process
		 */
		$assign_data['error']		= self::newStudyData( $study_data_manager, $category_codes ) !== TRUE;

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
		$study_data_manager			= Go::manager('StudyData');

		/*
		 * post data
		 */
		$study_data_manager->study_data_seq		= $study_data_manager->validation('study_data_seq',		initPost('study_data_seq'));
		$study_data_manager->study_data_title	= $study_data_manager->validation('study_data_title',	initPost('study_data_title'));
		$study_data_manager->study_data_from	= $study_data_manager->validation('study_data_from',	initPost('study_data_from'));
		$study_data_manager->study_data_content	= $study_data_manager->validation('study_data_content',	initPost('study_data_content'));
		$category_codes							= explode('_',(string)initPost('category_code_text'));

		/*
		 * process
		 */
		$assign_data['error']		= self::updateStudyData( $study_data_manager, $category_codes ) !== TRUE;

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
		$study_data_manager	= Go::manager('StudyData');

		/*
		 * post data
		 */
		$study_data_seqs	= 		$study_data_manager->validation( 'study_data_seq',	(array) initPost('study_data_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= self::deleteStudyData( $study_data_seqs ) !== TRUE;

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
		$study_data_title	= initGet('study_data_title');

		/*
		 * make where value
		 */
		if(strlen((string) $study_data_title ) != 0 ) $where[]	= '`study_data_title` LIKE ' . Database::escapeString( $study_data_title . '%' );

		/*
		 * return
		 */
		return $where;
	}
}
