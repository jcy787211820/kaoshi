<?php
/**
 * user level manager
 */
namespace manager;
use	\core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;
class UserLevel extends Manager
{
	const
		LEVEL_TYPE_ANS	= 1,
		LEVEL_TYPE_ASK	= 2;
	public	$user_level_seq			= 0,	// int(10) UN PK AI
			$user_level_name		= '',	// varchar(45)
			$user_level_min_empiric	= 0,	// decimal(12,0) UN
			$user_level_type		= 1,	// tinyint(1) UN
			$user_level_description	= '',	// varchar(200)
			$user_level_use_flag	= 'F',	// enum('T','F')
			$user_level_edit_time	= 0,	// int(10) UN
			$user_level_edit_id		= '',	// varchar(45)
			$user_level_delete_flag	= 'F';	// enum('T','F')

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'user_level',
				'primary_key'	=> 'user_level_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $user_level_seq
	 * @return single array
	 */
	public function loadBySeq( $user_level_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` =" . Database::escapeString( $user_level_seq ),
				'`user_level_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}
	/**
	 * load rdata by user level seqs
	 * @param (array) $user_level_seqs
	 * @return multi array
	 */
	public function loadBySeqs(array $user_level_seqs )
	{
		if(empty( $user_level_seqs )) return array();
		foreach( $user_level_seqs AS &$user_level_seq)
		{
			$user_level_seq	= Database::escapeString( $user_level_seq );
		}
		$this->setWhere(array(
				"`{$this->primary_key}` IN(" . implode(',', $user_level_seqs ) . ")",
		));
		return $this->select();
	}

	public function getAnsUserLevelSeq( $user_empiric_a )
	{
		$this->setFields(array(
				"MAX(`user_level_seq`) AS user_level_seq"
		));

		$this->setWhere(array(
				"`user_level_type` = " . Database::escapeString( self::LEVEL_TYPE_ANS ),
				"`user_level_min_empiric` <= " . Database::escapeString( $user_empiric_a ),
				"`user_level_use_flag` = 'T'",
				"`user_level_delete_flag` = 'F'",
		));
		return $this->select( MYSQL_ONE );
	}
	public function getAskUserLevelSeq( $user_empiric_b )
	{
		$this->setFields(array(
				"MAX(`user_level_seq`) AS user_level_seq"
		));

		$this->setWhere(array(
				"`user_level_type` = " . Database::escapeString( self::LEVEL_TYPE_ASK ),
				"`user_level_min_empiric` <= " . Database::escapeString( $user_empiric_b ),
				"`user_level_use_flag` = 'T'",
				"`user_level_delete_flag` = 'F'",
		));
		return $this->select( MYSQL_ONE );
	}
	/**
	 * load data by user group seqs
	 * @param (array) $user_group_seqs
	 * @return multi array
	 */
	public function loadNames(array $user_level_seqs )
	{
		/*
		 * var data
		*/
		$result	= array();

		/*
		 * set fileds
		*/
		$this->setFields(array(
				'user_level_seq',
				'user_level_name',
		));

		/*
		 * set return data
		*/
		$data	= $this->loadBySeqs( $user_level_seqs );
		foreach( $data AS $value )
		{
			$user_level_seq				= $value['user_level_seq'];
			$result[$user_level_seq]	= $value['user_level_name'];
		}

		/*
		 * return
		*/
		return $result;
	}
	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$where[]	= '`user_level_delete_flag` = "F"';
		$this->setFields(array(
				'`user_level_seq`',
				'`user_level_name`',
				'`user_level_min_empiric`',
				'`user_level_type`',
				'`user_level_description`',
				'`user_level_use_flag`',
				'`user_level_edit_time`',
				'`user_level_edit_id`',
				'FROM_UNIXTIME(`user_level_edit_time`,"%Y-%m-%d %H:%i:%s") AS `user_level_edit_date_vm`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`user_level_seq` DESC'));
		$lists	= $this->select();
		$lists	= array_map(array($this,'format'), $lists);
		return $lists;
	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert( $option = array())
	{
		$this->setFields(array(
				'user_level_name'			=> $this->user_level_name,
				'user_level_min_empiric'	=> $this->user_level_min_empiric,
				'user_level_type'			=> $this->user_level_type,
				'user_level_description'	=> $this->user_level_description,
				'user_level_use_flag'		=> $this->user_level_use_flag,
				'user_level_edit_time'		=> $this->editTime('user_level_edit_time'),
				'user_level_edit_id'		=> $this->editId('user_level_edit_id'),
		));
		return parent::insert( $option = array() );
	}
	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function update()
	{
		//set log keys
		$this->log_keys	= array( $this->user_level_seq );

		$this->setFields(array(
				'user_level_name'			=> $this->user_level_name,
				'user_level_min_empiric'	=> $this->user_level_min_empiric,
				'user_level_type'			=> $this->user_level_type,
				'user_level_description'	=> $this->user_level_description,
				'user_level_use_flag'		=> $this->user_level_use_flag,
				'user_level_edit_time'		=> $this->editTime('user_level_edit_time'),
				'user_level_edit_id'		=> $this->editId('user_level_edit_id'),
		));
		$this->setWhere(array(
				'`user_level_seq`=' . Database::escapeString( $this->user_level_seq ),
		));
		return parent::update();
	}

	/**
	 * delete multi row
	 * @param (array) $user_level_seqs
	 * @return boolean
	 */
	public function delete(array $user_level_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $user_level_seqs;

		$this->setFields(array(
				'user_level_delete_flag'	=> 'T',
				'user_level_edit_time'		=> $this->editTime('user_level_edit_time'),
				'user_level_edit_id'		=> $this->editId('user_level_edit_id'),
		));
		$this->setWhere(array(
				"`user_level_seq` in('" . implode( "','", $user_level_seqs ) . "')"
		));
		return parent::update();
	}
	/**
	 * set user_level_use_flag = 'T'
	 * @param (array) $user_level_seqs
	 * @return boolean
	 */
	public function used(array $user_level_seqs )
	{
		//set log keys
		$this->log_keys	= $user_level_seqs;

		$this->setFields(array(
				'user_level_use_flag'	=> 'T',
				'user_level_edit_time'	=> $this->editTime('user_level_edit_time'),
				'user_level_edit_id'	=> $this->editId('user_level_edit_id'),
		));
		$this->setWhere(array(
				"`user_level_seq` in('" . implode( "','", $user_level_seqs ) . "')"
		));
		return parent::update();
	}
	/**
	 * set user_level_use_flag = 'F'
	 * @param (array) $user_level_seqs
	 * @return boolean
	 */
	public function unused(array $user_level_seqs )
	{
		//set log keys
		$this->log_keys	= $user_level_seqs;

		$this->setFields(array(
				'user_level_use_flag'	=> 'F',
				'user_level_edit_time'	=> $this->editTime('user_level_edit_time'),
				'user_level_edit_id'	=> $this->editId('user_level_edit_id'),
		));
		$this->setWhere(array(
				"`user_level_seq` in('" . implode( "','", $user_level_seqs ) . "')"
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
				case 'user_level_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('User level seq error.');
					break;
				case 'user_level_name':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('User level name error.');
					break;
				case 'user_level_min_empiric':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new CException('User level min empiric error.');
					break;
				case 'user_level_type':
					if(isset( self::getUserLevelTypes()[$value] ) == FALSE ) throw new CException('User level type error.');
					if( self::vdtInt( $value, 1, 255 ) == FALSE ) throw new CException('User level type error.');
					break;
				case 'user_level_description':
					if( self::vdtStr( $value, 1, 200 ) == FALSE ) throw new CException('User level description error.');
					break;
				case 'user_level_use_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('User level use flag error.');
					break;
				case 'user_level_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('User level edit time error.');
					break;
				case 'user_level_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('User level edit id error.');
					break;
				case 'user_level_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('User level delete flag error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) $this->$method( $value );
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
		if(isset( $data['user_level_type'] ))
		{
			$user_level_type			= $data['user_level_type'];
			$data['user_level_type_vm']	= empty( $this->getUserLevelTypes()[$user_level_type] ) ? '' : $this->getUserLevelTypes()[$user_level_type];
		}
		return $data;
	}
	/**
	 * get user level types
	 * @return array
	 */
	public function getUserLevelTypes()
	{
		return array(
				self::LEVEL_TYPE_ANS	=> '答题者',
				self::LEVEL_TYPE_ASK	=> '出题者',
		);
	}
}
