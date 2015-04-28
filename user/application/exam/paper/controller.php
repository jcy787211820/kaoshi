<?php
includeTraits('Login');
includeTraits('Permission');
use \core\Database AS Database,
	\core\Controller AS Controller,
	\core\Config AS Config,
	\core\MsgException AS MsgException,
	\core\Go AS Go;
class Paper extends Controller
{
	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->frontCheckLogin();
		$this->checkPermission();
	}

	public function index()
	{
		/*
		 * var data
		 */
		$assign_data	= array();

		/*
		 * include class
		 */
		$test_manager		= Go::manager('Test');
		$category_manager	= Go::manager('Category');

		/*
		 * load data
		 */
		$test_types		= $test_manager->getTestTypes('impersonal');

		$category_manager->fields	= array(
				'category_code',
				'category_name',
		);
		$category_manager->where	= array(
				"category_use_flag = 'T'",
				"category_delete_flag = 'F'",
				"category_exercise_flag = 'T'",
		);
		$categorys					= $category_manager->select();

		/*
		 * view
		 */
		$assign_data['test_types']	= $test_types;
		$assign_data['categorys']	= $categorys;

		parent::view( $assign_data );
	}

	/**
	 * get list data
	 */
	public function ajaxIndex()
	{
		/*
		 * var data
		 */
		$assign_data = array('error'=>TRUE);

		/*
		 * load class
		 */
		$test_manager				= Go::manager('Test');
		$category_map_test_manager	= Go::manager('CategoryMapTest');

		/*
		 * post data
		 */
		$page					= initGet('page');
		$rows					= initGet('rows');
		$test_type				= initGet('test_type');
		$start_test_insert_date	= initGet('start_test_insert_date');
		$end_test_insert_date	= initGet('end_test_insert_date');
		$test_check_flag		= initGet('test_check_flag');
		$start_test_price		= initGet('start_test_price');
		$end_test_price			= initGet('end_test_price');
		$category_codes			= initGet('category_code');
		$test_question			= initGet('test_question');

		/*
		 * search page, search rows
		 */
		$page					= $page > 0 ? $page : 1;
		$rows					= $rows > 0 ? $rows : 15;


		/*
		 * search category_code
		 */
		$search_category_code	= NULL;
		if(!empty( $category_codes ) && is_array( $category_codes ))
		{
			sort( $category_codes, SORT_STRING );
			$search_category_code	= end( $category_codes );
			if( $search_category_code === '' ) $search_category_code = NULL;
		}

		/*
		 * setting page
		 */
		$test_manager->vdtPage( $page );
		$test_manager->vdtRows( $rows );
		$test_manager->setPage( $page, $rows );

		/*
		 * make fields
		 */
		$test_manager->setFields(array(
				'`test_seq`',
				'`test_type`',
				'LEFT(`test_question`,20) AS `test_question_vw`',
				'`test_price`',
				'IF( `test_check_flag` = "T", "已审核", "未审核") AS `test_check_flag_vw`',
				'FROM_UNIXTIME(`test_insert_time`) AS test_insert_datetime_vw',
		));
		/*
		 * make where
		 */
		$where		= array();
		$where[]	= '`user_id` = '. Database::escapeString( $_SESSION['user_id'] );
		if(array_key_exists( $test_type, $test_manager->getTestTypes('impersonal')))
		{
			$where[]	= '`test_type` = ' . Database::escapeString( $test_type );
		}
		if($test_manager->vdtDate( $start_test_insert_date ) == TRUE )
		{
			$where[]	= '`test_insert_time` >= ' . Database::escapeString(strtotime( $start_test_insert_date ));
		}
		if($test_manager->vdtDate( $end_test_insert_date ) == TRUE )
		{
			$where[]	= '`test_insert_time` <= ' . Database::escapeString(strtotime( $end_test_insert_date.'235959' ));
		}
		if( $test_check_flag == 'T' || $test_check_flag == 'F' )
		{
			$where[]	= '`test_check_flag` = ' . Database::escapeString( $test_check_flag );
		}
		if(strlen( $start_test_price ) > 0 )
		{
			$where[]	= '`test_price` >= ' . Database::escapeString( $start_test_price );
		}
		if(strlen( $end_test_price ))
		{
			$where[]	= '`test_price` <= ' . Database::escapeString( $end_test_price );
		}
		if(!is_null( $search_category_code ))
		{
			$where[]	= 'EXISTS(
				SELECT 1 FROM `' . $category_map_test_manager->table . '` AS `tmp`
				WHERE `tmp`.`category_code` LIKE ' . Database::escapeString( $search_category_code.'%' ) . '
				AND `tmp`.`test_seq` = `' . $test_manager->table . '`.`test_seq`
			)';
		}
		if(strlen( $test_question ) > 0 )
		{
			$where[]	= '`test_question` LIKE ' . Database::escapeString( '%'.$test_question.'%' );
		}
		$test_manager->setWhere( $where );

		/*
		 * order
		 */
		$test_manager->setOrder(array('`test_insert_time` DESC','`test_seq` ASC'));

		/*
		 * get lists
		 */
		$lists	= $test_manager->select(MYSQL_ASSOC);

		/*
		 * format
		 */
		foreach( $lists AS $key => $list )
		{
			$lists[$key]['test_seq_vw']		= core_encode( $list['test_seq'] );
			$lists[$key]['test_type_vw']	= $test_manager->getTestTypeValue( $list['test_type'] );
			unset( $lists[$key]['test_type'] );
		}

		/*
		 * get total
		 */
		$total	= $test_manager->preTotal();

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
	 * request form
	 */
	public function form( $encode_seq = '' )
	{
		/*
		 * var data
		 */
		$assign_data			= array();
		$category_map_codes		= array();
		$test_paper_seq			= 0;

		if( $encode_seq !== '' )
		{
// 			$test_seq				= core_decode( $encode_seq );
// 			if(!ctype_digit( $test_seq ) || $test_seq == 0) throw new MsgException('非法操作.');
		}

		/*
		 * include class
		 */
		$test_paper_manager					= Go::manager('TestPaper');
		$category_manager					= Go::manager('Category');
		$category_map_test_paper_manager	= Go::manager('CategoryMapTest');

		/*
		 * load data
		 */
		$test_paper					= $test_paper_manager->fields;

		$category_manager->fields	= array(
			'`category_code`',
			'`category_name`',
		);
		$category_manager->where	= array(
			"`category_use_flag` = 'T'",
			"`category_delete_flag` = 'F'",
			"`category_exercise_flag` = 'T'",
		);
		$categorys					= $category_manager->select();

		// get test data
		if( $test_paper_seq > 0 )
		{
			$test_paper_manager->setFields(array(
				'`test_paper_seq`',
				'`test_paper_name`',
				'`test_paper_description`',
				'`test_paper_point`',
				'`test_paper_price`',
				'`test_paper_answer_price`',
				'`test_paper_analysis_price`',
				'`test_paper_timeout`',
				'`test_paper_insert_time`',
				'`test_paper_edit_time`',
			));
			$test_paper_manager->setWhere(array(
				'`user_id` = '. Database::escapeString( $_SESSION['user_id'] ),
				'`test_paper_seq` = '. Database::escapeString( $test_paper_seq ),
			));
			$test_paper	= $test_paper_manager->select( MYSQL_ROW_ASSOC );
			if(empty( $test_paper )) throw new MsgException('非法操作.');

			$category_map_test_paper_manager->setFields(array(
				'`category_code`'
			));
			$category_map_test_paper_manager->setWhere(array(
				'`test_paper_seq` = '. Database::escapeString( $test_paper_seq ),
			));
			$category_map_codes		= array_column($category_map_test_paper_manager->select(), 'category_code' );
		}

		/*
		 * view
		 */
		$assign_data['test_paper']			= $test_paper;
		$assign_data['categorys']			= $categorys;
		$assign_data['category_map_codes']	= $category_map_codes;
		$assign_data['encode_seq']			= $encode_seq;

		parent::view( $assign_data );
	}

	/**
	 * paper file
	 */
	public function file( $encode_seq = '' )
	{
		try
		{
			/*
			 * load class
			 */
			$file_manager				= Go::manager('File');
			$test_paper_file_manager	= Go::manager('TestPaperFile');
			$test_paper_manager			= Go::manager('TestPaper');

			/*
			 * post data
			 */
			$test_paper_seq			= core_decode( $encode_seq );
			$file_uri				= empty( $_FILES['test_paper_file'] ) ? NULL : $this->_uploadTestPaperFile();
			$file_title				= initPost('file_title');
			$file_description		= initPost('file_description');

			/*
			 * check params
			 */
			$this->_issetTestPaper( $test_paper_seq );
			$file_manager->validationFileTitle( $file_title );
			$file_manager->validationFileDescription( $file_description );

			/*
			 * find original file url by this test paper seq
			 */
			$test_paper_file_manager->setFields(array(
					'`file_seq`',
			));
			$test_paper_file_manager->setWhere(array(
					'`test_paper_seq` = '. Database::escapeString( $test_paper_seq ),
			));
			$file_seq	= $test_paper_file_manager->select( MYSQL_ONE );

			if( $file_seq > 0 )
			{
				$file_manager->setFields(array(
						'`file_uri`',
				));
				$file_manager->setWhere(array(
						'`file_seq` = '. Database::escapeString( $file_seq ),
				));
				$original_file_uri	= $test_paper_file_manager->select( MYSQL_ONE );
				$file_uri			= empty( $file_uri ) ? $original_file_uri : $file_uri;
			}

			/*
			 * database process
			 */
			try
			{
				/********************/
				Database::transtart();
				/********************/
				if( $file_seq > 0 )
				{
					$file_manager->setFields(array(
							'file_title'			=> $file_title,
							'file_description'		=> $file_description,
							'file_uri'				=> $file_uri,
					));
					$file_manager->setWhere(array(
							'`file_seq` = '. Database::escapeString( $file_seq ),
					));
					if(!$file_manager->update()) throw new MsgException('系统错误：附件修改异常,请重试.');

					if( $file_uri != $original_file_uri )
					{
						$original_file_path	= str_replace( Config::base('UPLOAD_BASE_URI'), Config::base('UPLOAD_PATH'), $file_uri );
						@unlink( $original_file_path );
					}
				}
				else
				{
					if(empty( $file_uri ))	throw new MsgException('请选择需要上传的文件.');
					$file_manager->setFields(array(
							'file_title'			=> $file_title,
							'file_description'		=> $file_description,
							'file_uri'				=> $file_uri,
					));
					if(!$file_manager->insert()) throw new MsgException('系统错误：附件添加异常,请重试.');
					$file_seq	= $file_manager->file_seq;

					$test_paper_file_manager->setFields(array(
							'test_paper_seq'	=> $test_paper_seq,
							'file_seq'			=> $file_seq,
					));
					if(!$test_paper_file_manager->insert()) throw new MsgException('系统错误：附件添加异常,请重试.');
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
			echo "<script type='text/javascript'>parent.jQuery.msgbox({'message':'成功.'});</script>";
		}
		catch(MsgException $e)
		{
			echo "<script type='text/javascript'>parent.jQuery.msgbox({'message':'{$e->getMessage()}'});</script>";
		}
		catch(Exception $e)
		{
			echo "<script type='text/javascript'>parent.jQuery.msgbox({'message':'系统错误.'});</script>";
		}
	}

	/**
	 * request
	 */
	public function insert()
	{
		/*
		 * var data
		 */
		$assign_data			= array('error' => TRUE);
		$test_paper_edit_time	= REQUEST_TIME;
		$test_paper_seq			= 0;

		/*
		 * load class
		 */
		$test_paper_manager					= Go::manager('TestPaper');
		$category_manager					= Go::manager('Category');
		$category_map_test_paper_manager	= Go::manager('CategoryMapTestPaper');

		/*
		 * post data
		 */
		$category_codes				= initPost('category_code');
		$test_paper_name			= initPost('test_paper_name');
		$test_paper_description		= initPost('test_paper_description');
		$test_paper_timeout			= initPost('test_paper_timeout');
		$test_paper_price			= initPost('test_paper_price');
		$test_paper_answer_price	= initPost('test_paper_answer_price');
		$test_paper_analysis_price	= initPost('test_paper_analysis_price');

		/*
		 * filter post data
		 */
		$category_codes			= $category_manager->onlyLastChild( $category_codes );
		$test_paper_manager->validationTestPaperName( $test_paper_name );
		$test_paper_manager->validationTestPaperDescription( $test_paper_description );
		$test_paper_manager->validationTestPaperTimeout( $test_paper_timeout );
		$test_paper_manager->validationTestPaperPrice( $test_paper_price );
		$test_paper_manager->validationTestPaperAnswerPrice( $test_paper_answer_price );
		$test_paper_manager->validationTestPaperAnalysisPrice( $test_paper_analysis_price );

		if(empty( $category_codes )) throw new MsgException('至少需要选择一个展示的分类.');

		/*
		 * database process
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			// st_test insert
			$test_paper_manager->fields		= array(
				'user_id'					=> $_SESSION['user_id'],
				'test_paper_name'			=> $test_paper_name,
				'test_paper_description'	=> $test_paper_description,
				'test_paper_point'			=> '0',
				'test_paper_price'			=> $test_paper_price,
				'test_paper_answer_price'	=> $test_paper_answer_price,
				'test_paper_analysis_price'	=> $test_paper_analysis_price,
				'test_paper_timeout'		=> $test_paper_timeout,
				'test_paper_check_flag'		=> 'F',
				'test_paper_insert_time'	=> $test_paper_edit_time,
				'test_paper_insert_id'		=> $_SESSION['user_id'],
				'test_paper_edit_time'		=> $test_paper_edit_time,
				'test_paper_edit_id'		=> $_SESSION['user_id'],
				'test_paper_delete_flag'	=> 'F',
			);
			if(!$test_paper_manager->insert()) throw new MsgException('系统错误：试卷添加异常,请重试.');
			$test_paper_seq	= $test_paper_manager->test_paper_seq;

			// st_category_map_test insert
			$category_map_test_paper_manager->fields		= array();
			foreach( $category_codes AS $category_code )
			{
				$category_map_test_paper_manager->fields[]	= array(
					'category_code'		=> $category_code,
					'test_paper_seq'	=> $test_paper_seq,
				);
			}
			if(!$category_map_test_paper_manager->insert()) throw new MsgException('系统错误：试卷展示分类添加异常,请重试.');

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
		 * assign data
		 */
		$assign_data['encode_seq']					= core_encode( $test_paper_seq );
		$assign_data['test_paper_edit_datetime_vw']	= date('Y-m-d H:i:s', $test_paper_edit_time);
		$assign_data['error']						= FALSE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	/**
	 * request
	 */
	public function update( $encode_seq = '' )
	{
		/*
		 * var data
		*/
		$assign_data		= array('error' => TRUE );
		$test_edit_time		= REQUEST_TIME;

		/*
		 * load class
		 */
		$test_manager				= Go::manager('Test');
		$category_manager			= Go::manager('Category');
		$category_map_test_manager	= Go::manager('CategoryMapTest');

		/*
		 * post data
		 */
		$test_seq				= core_decode( $encode_seq );
		$category_codes			= initPost('category_code');
		$test_type				= initPost('test_type');
		$test_question			= initPost('test_question');
		$test_answer_content	= initPost('test_answer_content');
		$test_real_answer		= initPost('test_real_answer');
		$test_analysis			= initPost('test_analysis');
		$test_timeout			= initPost('test_timeout');
		$test_price				= initPost('test_price');
		$test_answer_price		= initPost('test_answer_price');
		$test_analysis_price	= initPost('test_analysis_price');

		/*
		 * select original data
		 */
		$test_manager->setFields(array(
			'`test_type`',
			'`test_question`',
			'`test_answer_json`',
			'`test_timeout`',
			'`test_check_flag`',
		));
		$test_manager->setWhere(array(
			'`user_id` = '. Database::escapeString( $_SESSION['user_id'] ),
			'`test_seq` = '. Database::escapeString( $test_seq ),
		));
		$test	= $test_manager->select( MYSQL_ROW_ASSOC );
		if(empty( $test )) throw new MsgException('非法操作.');

		/*
		 * filter post data
		 */
		$category_codes			= $category_manager->onlyLastChild( $category_codes );
		if( $test['test_check_flag'] == 'T' )
		{
			$test_type				= $test['test_type'];
			$test_question			= $test['test_question'];
			$test_answer_json		= $test['test_answer_json'];
			$test_timeout			= $test['test_timeout'];
		}
		else
		{
			$test_answer_json		= $test_manager->makeTestAnswerJson( $test_type, $test_answer_content, $test_real_answer );
		}

		/*
		 * check params
		 */
		$test_manager->validationTestType( $test_type, 'impersonal' );
		$test_manager->validationTestQuestion( $test_question );
		$test_manager->validationTestAnswerJson( $test_answer_json );
		$test_manager->validationTestAnalysis( $test_analysis );
		$test_manager->validationTestTimeout( $test_timeout );
		$test_manager->validationTestPrice( $test_price );
		$test_manager->validationTestAnswerPrice( $test_answer_price );
		$test_manager->validationTestAnalysisPrice( $test_analysis_price );

		if(empty( $category_codes )) throw new MsgException('至少需要选择一个展示的分类.');

		/*
		 * database process
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			// st_test update
			$test_manager->fields		= array(
				'test_type'				=> "IF(`test_check_flag`='T', `test_type`, " . Database::escapeString( $test_type ) .")",
				'test_question'			=> "IF(`test_check_flag`='T', `test_question`, " . Database::escapeString( $test_question ) .")",
				'test_answer_json'		=> "IF(`test_check_flag`='T', `test_answer_json`, " . Database::escapeString( $test_answer_json ) .")",
				'test_timeout'			=> "IF(`test_check_flag`='T', `test_timeout`, " . Database::escapeString( $test_timeout ) .")",
				'test_analysis'			=> $test_analysis,
				'test_price'			=> $test_price,
				'test_answer_price'		=> $test_answer_price,
				'test_analysis_price'	=> $test_analysis_price,
				'test_edit_time'		=> $test_edit_time,
				'test_edit_id'			=> $_SESSION['user_id'],
			);
			$test_manager->where		= array(
				'`user_id` = '. Database::escapeString( $_SESSION['user_id'] ),
				'`test_seq` = '. Database::escapeString( $test_seq ),
			);
			$test_manager->unescapes	= array(
				'test_type',
				'test_question',
				'test_answer_json',
				'test_timeout',
			);
			if(!$test_manager->update()) throw new MsgException('系统错误：试题添加异常,请重试.');

			// st_category_map_test delete
			$category_map_test_manager->where	= array(
				'`test_seq` = '. Database::escapeString( $test_seq ),
				'`category_code` NOT IN(' . implode( ',' , array_map(array('\core\Database','escapeString'), $category_codes )) . ')',
			);
			if(!$category_map_test_manager->delete())	throw new MsgException('系统错误：试题展示分类删除异常,请重试.');

			// st_category_map_test insert
			$category_map_test_manager->fields		= array();
			foreach( $category_codes AS $category_code )
			{
				$category_map_test_manager->fields[]	= array(
					'category_code'						=> $category_code,
					'test_seq'							=> $test_seq,
				);
			}
			if(!$category_map_test_manager->insert(array('IGNORE'=>'IGNORE')))	throw new MsgException('系统错误：试题展示分类添加异常,请重试.');

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
		 * assign data
		 */
		$assign_data['test_edit_datetime_vw']	= date('Y-m-d H:i:s', $test_edit_time);
		$assign_data['error']					= FALSE;

		/*
		 * result
		 */
		echo json_encode( $assign_data );
	}

	public function unitList( $encode_seq )
	{
		/*
		 * var data
		 */
		$assign_data		= array('error'=>TRUE);
		$test_paper_seq		= core_decode($encode_seq);

		/*
		 * load class
		 */
		$test_paper_unit_manager	= Go::manager('TestPaperUnit');

		$test_paper_unit_manager->setFields(array(
			'`parent_test_paper_unit_seq`',
			'`test_paper_unit_seq`',
			'`test_paper_unit_title`',
			'`test_paper_unit_point`',
			'`test_paper_unit_description`',
		));

		$test_paper_unit_manager->setWhere(array(
			"`test_paper_seq` = '{$test_paper_seq}'",
			"`test_paper_unit_delete_flag` = 'F'",
		));

		/*
		 * order
		 */
		$test_paper_unit_manager->setOrder(array('`test_paper_unit_sort` ASC'));

		/*
		 * get lists
		 */
		function funMakeTree( &$lists, &$tree, $parent_seq = 0 )
		{
			foreach( $lists AS $key => $list )
			{
				if( $parent_seq == $list['parent_test_paper_unit_seq'] )
				{
					$test_paper_unit_seq		= $list['test_paper_unit_seq'];
					$tree[$test_paper_unit_seq]	= $list;
					unset( $lists[$key] );
					$tree[$test_paper_unit_seq]['children']	= array();
					funMakeTree( $lists, $tree[$test_paper_unit_seq]['children'], $test_paper_unit_seq );
				}
			}
		}
		$lists		= array();
		$tmp_lists	= $test_paper_unit_manager->select(MYSQL_ASSOC);
		funMakeTree( $tmp_lists, $lists );


		/*
		 * assign data
		 */
		$assign_data['lists']	= $lists;
		$assign_data['error']	= FALSE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	public function unitInsert()
	{
		/*
		 * var data
		 */
		$assign_data				= array('error' => TRUE);
		$test_paper_unit_edit_time	= REQUEST_TIME;

		/*
		 * load class
		 */
		$test_paper_unit_manager	= Go::manager('TestPaperUnit');

		/*
		 * post data
		 */
		$test_paper_seq				= core_decode(initPost('encode_seq'));
		$parent_test_paper_unit_seq	= initPost('parent_test_paper_unit_seq');
		$test_paper_unit_title		= initPost('test_paper_unit_title');
		$test_paper_unit_description= initPost('test_paper_unit_description');

		/*
		 * filter post data
		 */
		$this->_issetTestPaper( $test_paper_seq );
		$test_paper_unit_manager->validationParentTestPaperUnitSeq( $parent_test_paper_unit_seq );
		$test_paper_unit_manager->validationTestPaperUnitTitle( $test_paper_unit_title );
		$test_paper_unit_manager->validationTestPaperUnitDescription( $test_paper_unit_description );

		// insert
		$test_paper_unit_manager->setFields(array(
			'test_paper_seq'				=> $test_paper_seq,
			'parent_test_paper_unit_seq'	=> $parent_test_paper_unit_seq,
			'test_paper_unit_sort'			=> "(SELECT COUNT(*)
												FROM {$test_paper_unit_manager->database}.{$test_paper_unit_manager->table} AS `tmp`
												WHERE `tmp`.`test_paper_seq` = '{$test_paper_seq}'
												AND `tmp`.`parent_test_paper_unit_seq` = '{$parent_test_paper_unit_seq}')",
			'test_paper_unit_point'			=> '0',
			'test_paper_unit_title'			=> $test_paper_unit_title,
			'test_paper_unit_description'	=> $test_paper_unit_description,
			'test_paper_unit_edit_time'		=> $test_paper_unit_edit_time,
			'test_paper_unit_edit_id'		=> $_SESSION['user_id'],
			'test_paper_unit_delete_flag'	=> 'F',
		));
		$test_paper_unit_manager->setUnescapes(array('test_paper_unit_sort'));
		if(!$test_paper_unit_manager->insert()) throw new MsgException('系统错误：试卷小标题添加异常,请重试.');

		/*
		 * assign data
		 */
		$assign_data['error']						= FALSE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	private function _issetTestPaper( $test_paper_seq )
	{
		if(empty( $test_paper_seq )) throw new MsgException('请先添加试题基本信息后，再上传附件.');

		$test_paper_manager	= Go::manager('TestPaper');
		$test_paper_manager->setFields(array(
				'1',
		));
		$test_paper_manager->setWhere(array(
				'`user_id` = '. Database::escapeString( $_SESSION['user_id'] ),
				'`test_paper_seq` = '. Database::escapeString( $test_paper_seq ),
				"`test_paper_delete_flag` = 'F'",
		));
		if($test_paper_manager->select( MYSQL_ONE ) != '1' ) throw new MsgException('非法操作.');

	}
	/**
	 * upload file
	 */
	private function _uploadTestPaperFile()
	{
		$upload_plugin	= Go::plugin('Upload');
		$upload_plugin->setProperty('upload_field','test_paper_file');
		$upload_plugin->setProperty('upload_dir', Config::base('UPLOAD_PATH') . DIRECTORY_SEPARATOR . 'test_paper_file');
		$upload_plugin->setProperty('upload_base_uri', Config::base('UPLOAD_BASE_URI') . DIRECTORY_SEPARATOR . 'test_paper_file');
		$upload_plugin->setProperty('upload_limit_size', 10482760);	// 10M
		$upload_infos	= $upload_plugin->doAction();
		if( $upload_infos['error'] == TRUE ) throw new MsgException( $upload_infos['message'] );
		return $upload_infos['data']['upload_uri'];
	}
}
