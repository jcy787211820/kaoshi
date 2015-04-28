<?php
/**
 * news manager
 */
namespace manager;
use \core\Go AS Go,
	\core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;

class News extends Manager
{
	public	$news_seq			= 0,	// int(10) UN PK AI
			$area_code			= '',	// varchar(30)
			$news_from			= '',	// varchar(200)
			$news_title			= '',	// varchar(45)
			$news_content		= '',	// text
			$news_read_count	= 0,	// decimal(12,0) UN
			$news_edit_time		= 0,	// int(10) UN
			$news_edit_id		= '',	// varchar(45)
			$news_delete_flag	= 'F';	// enum('T','F')


	public function __construct()
	{
		parent::__construct(array(
			'database'		=> Config::database('default')['DATABASE'],
			'table'			=> Config::database('default')['TABLE_PERFIX'] . 'news',
			'primary_key'	=> 'news_seq',
			'create_log'	=> TRUE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $news_seq
	 * @return single array
	 */
	public function loadBySeq( $news_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` = " . Database::escapeString( $news_seq ),
				'`news_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		/*
		 * load data
		 */
		$where[]	= '`news_delete_flag` = "F"';
		$this->setFields(array(
				'`news_seq`',
				'`area_code`',
				'`news_from`',
				'`news_title`',
				'`news_read_count`',
				'`news_edit_time`',
				'`news_edit_id`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`news_edit_id` DESC'));
		$lists	= $this->select();
		$lists	= array_map(array($this,'format'), $lists);

		/*
		 * mapping area name
		 */
		$area_manager	= Go::Manager('Area');
		$lists			= $area_manager->mappingName( $lists );

		/*
		 * return
		 */
		return $lists;
	}

	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert($option=array())
	{
		$this->setFields(array(
				'area_code'			=> $this->area_code,
				'news_from'			=> $this->news_from,
				'news_title'		=> $this->news_title,
				'news_content'		=> $this->news_content,
				'news_edit_time'	=> $this->editTime('news_edit_time'),
				'news_edit_id'		=> $this->editId('news_edit_id'),
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
		$this->log_keys	= array( $this->news_seq );

		$this->setFields(array(
				'area_code'			=> $this->area_code,
				'news_from'			=> $this->news_from,
				'news_title'		=> $this->news_title,
				'news_content'		=> $this->news_content,
				'news_edit_time'	=> $this->editTime('news_edit_time'),
				'news_edit_id'		=> $this->editId('news_edit_id'),
		));
		$this->setWhere(array(
				'`news_seq`=' . Database::escapeString( $this->news_seq ),
		));
		return parent::update();
	}

	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function read()
	{
		//set log keys
		$this->log_keys	= array( $this->news_seq );

		$this->news_read_count	= "`news_read_count` + 1";
		$this->setUnescapes(array('news_read_count'));

		$this->setFields(array(
				'news_read_count'		=> $this->news_read_count,
		));
		$this->setWhere(array(
				'`news_seq`=' . Database::escapeString( $this->news_seq ),
		));
		return parent::update();
	}

	/**
	 * delete row
	 * @param (array) $news_seqs
	 * @return boolean
	 */
	public function delete(array $news_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $news_seqs;

		$this->setFields(array(
				'news_delete_flag'	=> 'T',
				'news_edit_time'		=> $this->editTime('news_edit_time'),
				'news_edit_id'		=> $this->editId('news_edit_id'),
		));
		$this->setWhere(array(
				"`news_seq` IN(" . implode( ",", array_map(array('\core\Database','escapeString'), $news_seqs )) . ")"
		));
		return parent::update();
	}

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
				case 'news_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('News seq error.');
					break;
				case 'area_code':
					if( self::vdtStr( $value, 0, 30 ) == FALSE || self::vdtReg( $value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('Area code error.');
					break;
				case 'news_from':
					if( self::vdtStr( $value, 0, 200 ) == FALSE ) throw new CException('News from error.');
					break;
				case 'news_title':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('News name error.');
					break;
				case 'news_content':
					if( self::vdtStr( $value, 1, 65535 ) == FALSE ) throw new CException('News content error.');
					break;
				case 'news_read_count':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new CException('News read count error.');
					break;
				case 'news_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('News edit time error.');
					break;
				case 'news_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('News edit id error.');
					break;
				case 'news_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('News delete flag error.');
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
		if(isset( $data['news_edit_time'] ) == TRUE )
		{
			$data['news_edit_time_vm']	= $data['news_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['news_edit_time'] ) : '';
		}

		return $data;
	}
}