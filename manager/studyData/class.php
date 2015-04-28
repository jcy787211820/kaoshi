<?php
/**
 * study data manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;

class StudyData extends Manager
{
	public	$study_data_seq				= 0,	// int(10) UN PK AI
			$study_data_from			= '',	// varchar(200)
			$study_data_title			= '',	// varchar(45)
			$study_data_content			= '',	// text
			$study_data_read_count		= 0,	// decimal(12,0) UN
			$study_data_edit_time		= 0,	// int(10) UN
			$study_data_edit_id			= '',	// varchar(45)
			$study_data_delete_flag		= 'F';	// enum('T','F')


	public function __construct()
	{
		parent::__construct(array(
			'database'		=> Config::database('default')['DATABASE'],
			'table'			=> Config::database('default')['TABLE_PERFIX'] . 'study_data',
			'primary_key'	=> 'study_data_seq',
			'create_log'	=> TRUE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $study_data_seq
	 * @return single array
	 */
	public function loadBySeq( $study_data_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` = " . Database::escapeString( $study_data_seq ),
				'`study_data_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$where[]	= '`study_data_delete_flag` = "F"';
		$this->setFields(array(
				'`study_data_seq`',
				'`study_data_from`',
				'`study_data_title`',
				'`study_data_read_count`',
				'`study_data_edit_time`',
				'`study_data_edit_id`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`study_data_edit_id` DESC'));
		$lists	= $this->select();
		$lists	= array_map(array($this,'format'), $lists);
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
				'study_data_from'		=> $this->study_data_from,
				'study_data_title'		=> $this->study_data_title,
				'study_data_content'	=> $this->study_data_content,
				'study_data_edit_time'	=> $this->editTime('study_data_edit_time'),
				'study_data_edit_id'	=> $this->editId('study_data_edit_id'),
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
		$this->log_keys	= array( $this->study_data_seq );

		$this->setFields(array(
				'study_data_from'		=> $this->study_data_from,
				'study_data_title'		=> $this->study_data_title,
				'study_data_content'	=> $this->study_data_content,
				'study_data_edit_time'	=> $this->editTime('study_data_edit_time'),
				'study_data_edit_id'	=> $this->editId('study_data_edit_id'),
		));
		$this->setWhere(array(
				'`study_data_seq`=' . Database::escapeString( $this->study_data_seq ),
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
		$this->log_keys	= array( $this->study_data_seq );

		$this->study_data_read_count	= "`study_data_read_count` + 1";
		$this->setUnescapes(array('study_data_read_count'));

		$this->setFields(array(
				'study_data_read_count'		=> $this->study_data_read_count,
		));
		$this->setWhere(array(
				'`study_data_seq`=' . Database::escapeString( $this->study_data_seq ),
		));
		return parent::update();
	}

	/**
	 * delete row
	 * @param (array) $study_data_seqs
	 * @return boolean
	 */
	public function delete(array $study_data_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $study_data_seqs;

		$this->setFields(array(
				'study_data_delete_flag'	=> 'T',
				'study_data_edit_time'		=> $this->editTime('study_data_edit_time'),
				'study_data_edit_id'		=> $this->editId('study_data_edit_id'),
		));
		$this->setWhere(array(
				"`study_data_seq` IN(" . implode( ",", array_map(array('\core\Database','escapeString'), $study_data_seqs )) . ")"
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
				case 'study_data_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Study data seq error.');
					break;
				case 'study_data_from':
					if( self::vdtStr( $value, 0, 200 ) == FALSE ) throw new CException('Study data from error.');
					break;
				case 'study_data_title':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Study_data name error.');
					break;
				case 'study_data_content':
					if( self::vdtStr( $value, 1, 65535 ) == FALSE ) throw new CException('Study data content error.');
					break;
				case 'study_data_read_count':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new CException('Study data read count error.');
					break;
				case 'study_data_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Study data edit time error.');
					break;
				case 'study_data_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Study data edit id error.');
					break;
				case 'study_data_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Study data delete flag error.');
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
		if(isset( $data['study_data_edit_time'] ) == TRUE )
		{
			$data['study_data_edit_time_vm']	= $data['study_data_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['study_data_edit_time'] ) : '';
		}

		return $data;
	}
}