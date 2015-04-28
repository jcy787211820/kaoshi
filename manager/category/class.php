<?php
/**
 * category manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;

class Category extends Manager
{
	public	$category_seq					= 0,	// int(10) UN PK AI
			$category_code					= '',	// varchar(30)
			$category_name					= '',	// varchar(45)
			$category_description			= '',	// varchar(200)
			$category_use_flag				= 'F',	// enum('T','F')
			$category_study_data_flag		= 'F',	// enum('T','F')
			$category_news_flag				= 'F',	// enum('T','F')
			$category_exercise_flag			= 'F',	// enum('T','F')
			$category_edit_time				= 0,	// int(10) UN
			$category_edit_id				= '',	// varchar(45)
			$category_delete_flag			= 'F';	// enum('T','F')

	public
		$fields								= array(
				'category_seq'				=> 0,		// int(10) UN PK AI
				'category_code'				=> '',		// varchar(30)
				'category_name'				=> '',		// varchar(45)
				'category_description'		=> '',		// varchar(200)
				'category_use_flag'			=> 'F',		// enum('T','F')
				'category_study_data_flag'	=> 'F',		// enum('T','F')
				'category_news_flag'		=> 'F',		// enum('T','F')
				'category_exercise_flag'	=> 'F',		// enum('T','F')
				'category_edit_time'		=> 0,		// int(10) UN
				'category_edit_id'			=> '',		// varchar(45)
				'category_delete_flag'		=> 'F',		// enum('T','F')
		);


	public function __construct()
	{
		parent::__construct(array(
			'database'		=> Config::database('default')['DATABASE'],
			'table'			=> Config::database('default')['TABLE_PERFIX'] . 'category',
			'primary_key'	=> 'category_seq',
			'create_log'	=> TRUE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $category_seq
	 * @return single array
	 */
	public function loadBySeq( $category_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` = " . Database::escapeString( $category_seq ),
				'`category_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load row data by Category Code
	 * @param (int) $category_code
	 * @return single array
	 */
	public function loadByCode( $category_code )
	{
		$this->setWhere(array(
				'`category_code` = ' . Database::escapeString( $category_code ),
				'`category_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load used All
	 * @return array
	 */
	public function loadUsedAll( $kind_flag = NULL )
	{
		/*
		 * set where
		 */
		$this->setFields(array(
			'*'
		));
		$this->setWhere(array(
				'`category_use_flag` = "T"',
				'`category_delete_flag` = "F"',
		));
		switch( $kind_flag )
		{
			case 'category_study_data_flag':
			case 'category_news_flag':
			case 'category_exercise_flag':
				$this->where[]	= "`{$kind_flag}` = 'T'";
				break;
			default:
				if(!is_null( $kind_flag )) throw new CException('kind type error.');

		}

		/*
		 * load
		*/
		return $this->select();
	}


	/**
	 * load children data
	 * @param (string) $category_parent_code
	 * @return double array
	 */
	public function loadByParentCode( $category_parent_code, $where = array())
	{
		$lists	= array();
		$this->setFields(array(
				'*'
		));
		$this->setWhere(array(
				'`category_code` LIKE ' . Database::escapeString( $category_parent_code . '__' ),
				'`category_delete_flag` = "F"',
		));
		$this->where	= array_merge( $this->where, $where );

		$this->setOrder(array('`category_seq` ASC'));
		return $this->select( MYSQL_ASSOC );
	}

	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert( $category_parent_code = NULL )
	{
		$this->category_code	= "(
			SELECT IFNULL(LPAD(CONV(CONV(MAX(`category_code`),36,10)+1,10,36)," . (strlen((string) $category_parent_code ) + 2) . ",'0'),'{$category_parent_code}00')
			FROM `{$this->database}`.`{$this->table}` AS `tmp`
			WHERE `category_code` LIKE " . Database::escapeString( $category_parent_code . '__' ) . "
		)";
		$this->setUnescapes(array('category_code'));

		$this->setFields(array(
				'category_code'					=> $this->category_code,
				'category_name'					=> $this->category_name,
				'category_description'			=> $this->category_description,
				'category_use_flag'				=> $this->category_use_flag,
				'category_study_data_flag'	=> $this->category_study_data_flag,
				'category_news_flag'			=> $this->category_news_flag,
				'category_exercise_flag'		=> $this->category_exercise_flag,
				'category_edit_time'			=> $this->editTime('category_edit_time'),
				'category_edit_id'				=> $this->editId('category_edit_id'),
		));
		return parent::insert();
	}

	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function update()
	{
		//set log keys
		$this->log_keys	= array( $this->category_seq );

		$this->setFields(array(
				'category_name'					=> $this->category_name,
				'category_description'			=> $this->category_description,
				'category_use_flag'				=> $this->category_use_flag,
				'category_study_data_flag'	=> $this->category_study_data_flag,
				'category_news_flag'			=> $this->category_news_flag,
				'category_exercise_flag'		=> $this->category_exercise_flag,
				'category_edit_time'			=> $this->editTime('category_edit_time'),
				'category_edit_id'				=> $this->editId('category_edit_id'),
		));
		$this->setWhere(array(
			'`category_seq`=' . Database::escapeString( $this->category_seq ),
		));
		return parent::update();
	}
	/**
	 * delete multi row
	 * @param (array) $category_seqs
	 * @return boolean
	 */
	public function delete()
	{
		//set log keys
		$this->log_keys	= array( $this->category_seq );

		$this->setFields(array(
				'category_delete_flag'	=> 'T',
				'category_edit_time'	=> $this->editTime('category_edit_time'),
				'category_edit_id'		=> $this->editId('category_edit_id'),
		));
		$this->setWhere(array(
				"`category_seq` = " . Database::escapeString( $this->category_seq )
		));
		return parent::update();
	}
// 	/**
// 	 * set category_use_flag = 'T'
// 	 * @param (array) $category_seqs
// 	 * @return boolean
// 	 */
// 	public function used(array $category_seqs )
// 	{
// 		$this->setFields(array(
// 				'category_use_flag'		=> 'T',
// 				'category_edit_time'	=> $this->editTime('category_edit_time'),
// 				'category_edit_id'		=> $this->editId('category_edit_id'),
// 		));
// 		$this->setWhere(array(
// 				"`category_seqs` in('" . implode( "','", $category_seqs ) . "')"
// 		));
// 		return parent::update();
// 	}
// 	/**
// 	 * set category_use_flag = 'F'
// 	 * @param (array) $category_seqs
// 	 * @return boolean
// 	 */
// 	public function unused(array $category_seqs )
// 	{
// 		$this->setFields(array(
// 				'category_use_flag'		=> 'F',
// 				'category_edit_time'	=> $this->editTime('category_edit_time'),
// 				'category_edit_id'		=> $this->editId('category_edit_id'),
// 		));
// 		$this->setWhere(array(
// 				"`category_seqs` in('" . implode( "','", $category_seqs ) . "')"
// 		));
// 		return parent::update();
// 	}

	/**
	 * fileds validation
	 */
	public function validation( $key, $value, $is_multi = FALSE )
	{
		if( $is_multi == TRUE )
		{
			foreach( $value AS $tmp )
			{
				$this->validation( $key, $tmp );
			}
		}
		else
		{
			switch( $key )
			{
				case 'category_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Category seq error.');
					break;
				case 'category_parent_code':
					if(!empty( $value )) self::validation('category_code', $value );
					break;
				case 'category_code':
					if( self::vdtStr( $value, 1, 45 ) == FALSE || self::vdtReg( $value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('Category Code error.');
					break;
				case 'category_name':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Category name error.');
					break;
				case 'category_description':
					if( self::vdtStr( $value, 0, 200 ) == FALSE ) throw new CException('Category description error.');
					break;
				case 'category_use_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Category use flag error.');
					break;
				case 'category_study_data_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Category learn material flag error.');
					break;
				case 'category_news_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Category news flag error.');
					break;
				case 'category_exercise_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Category exercise flag error.');
					break;
				case 'category_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Category edit time error.');
					break;
				case 'category_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Category edit id error.');
					break;
				case 'category_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Category delete flag error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) self::$method( $value );
					else throw new CException('Param error.');
			}
		}
		return $value;
	}

	/**
	 * data format
	 * @param (array) $data
	 */
	public function format( &$data )
	{
		if(isset( $data['category_edit_time'] ) == TRUE )
		{
			$data['category_edit_time_vm']	= $data['category_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['category_edit_time'] ) : '';
		}

		return $data;
	}

	public function onlyLastChild( $category_codes )
	{
		$delete_codes	= array();

		sort( $category_codes, SORT_STRING );
		while(($current_code = current( $category_codes )) !== FALSE)
		{
			$next_code		= next( $category_codes );
			if(		$current_code === substr( $next_code, 0, -2 )
				||	$current_code === ''
			)	$delete_codes[]	= $current_code;
		}
		$return_codes	= array_diff( $category_codes, $delete_codes );
		if(!array_walk( $return_codes, array($this,'validationCategoryCode'))) throw new CException('系统错误:php function error[array_walk].');

		return $return_codes;
	}

	public function validationCategoryCode( $category_code )
	{
		if(		self::vdtStr( $category_code, 1, 45 ) == FALSE
			||	self::vdtReg( $category_code, '@([A-Z0-9]{2})+@') == FALSE
		) throw new MsgException('Category Code error.');
	}
}