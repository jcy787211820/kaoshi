<?php
/**
 * news manager
 */
includeTraits('Login');
includeTraits('Permission');
includeTraits('News');
use \core\Go AS Go;
use \core\Controller AS Controller;
use \core\Database AS Database;
class News extends Controller
{
	use \traits\login,
		\traits\permission,
		\traits\news;

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
		$news_manager	= Go::manager('News');

		/*
		 * post data
		 */
		$page	= $news_manager->validation( 'vdt_page',		initGet('page') );
		$total	= $news_manager->validation( 'vdt_total',		initGet('total') );
		$rows	= $news_manager->validation( 'vdt_rows',		initGet('rows') );

		/*
		 * check
		 */
		if( $page * $rows > $total && $page > 1 ) throw new AmException('Param Error.');

		/*
		 * get lists
		 */
		$lists			= $news_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $news_manager->preTotal();

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
	 * @param (int) $news_seq
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
		$news_manager				= Go::manager('News');
		$category_manager			= Go::manager('Category');
		$area_manager				= Go::manager('Area');
		$category_map_news_manager	= Go::manager('CategoryMapNews');

		/*
		 * post data
		 */
		$news	= array();
		switch( initPost('action') )
		{
			case 'edit':
				$news_seq	= $news_manager->validation( 'news_seq',	initPost('news_seq'));
				$news		= $news_manager->loadBySeq( $news_seq );
				break;
			case 'add':
			default:
				$news		= get_object_vars( $news_manager );
		}
		$news				= $news_manager->format( $news );

		/*
		 * set category data
		 */
		$categorys						= $category_manager->loadUsedAll('category_news_flag');

		$category_map_newss				= array();
		if( $news['news_seq'] > 0 )
		{
			$category_map_news_manager->setFields(array('category_code'));
			$category_map_newss	= $category_map_news_manager->loadByNewsSeq( $news['news_seq'] );
		}

		/*
		 * set area data
		 */
		$areas			= $area_manager->loadAll();

		/*
		 * set assign data
		 */
		$assign_data['news']				= $news;
		$assign_data['categorys']			= $categorys;
		$assign_data['areas']				= $areas;
		$assign_data['category_map_newss']	= $category_map_newss;

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
		$news_manager			= Go::manager('News');

		/*
		 * post data
		 */
		$news_manager->area_code	= $news_manager->validation('area_code',	initPost('area_code'));
		$news_manager->news_title	= $news_manager->validation('news_title',	initPost('news_title'));
		$news_manager->news_from	= $news_manager->validation('news_from',	initPost('news_from'));
		$news_manager->news_content	= $news_manager->validation('news_content',	initPost('news_content'));
		$category_codes				= explode('_',(string)initPost('category_code_text'));

		/*
		 * process
		 */
		$assign_data['error']		= self::newNews( $news_manager, $category_codes ) !== TRUE;

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
		$news_manager			= Go::manager('News');

		/*
		 * post data
		 */
		$news_manager->news_seq		= $news_manager->validation('news_seq',		initPost('news_seq'));
		$news_manager->area_code	= $news_manager->validation('area_code',	initPost('area_code'));
		$news_manager->news_title	= $news_manager->validation('news_title',	initPost('news_title'));
		$news_manager->news_from	= $news_manager->validation('news_from',	initPost('news_from'));
		$news_manager->news_content	= $news_manager->validation('news_content',	initPost('news_content'));
		$category_codes				= explode('_',(string)initPost('category_code_text'));

		/*
		 * process
		 */
		$assign_data['error']		= self::updateNews( $news_manager, $category_codes ) !== TRUE;

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
		$news_manager	= Go::manager('News');

		/*
		 * post data
		 */
		$news_seqs		= $news_manager->validation( 'news_seq',	(array) initPost('news_seq'), TRUE );

		/*
		 * process
		 */
		$assign_data['error']	= self::deleteNews( $news_seqs ) !== TRUE;

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
		$news_title	= initGet('news_title');

		/*
		 * make where value
		 */
		if(strlen((string) $news_title ) != 0 ) $where[]	= '`news_title` LIKE ' . Database::escapeString( $news_title . '%' );

		/*
		 * return
		 */
		return $where;
	}
}
