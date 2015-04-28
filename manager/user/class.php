<?php
/**
 * user manager
 */
namespace manager;
use \core\MsgException AS MsgException,
	\core\Database AS Database,
	\core\Config AS Config;
class User extends Manager
{
	public	$user_seq				= 0,	// int(10) UN PK AI
			$user_id				= '',	// varchar(45)
			$user_password			= '',	// char(40)
			$user_point				= 0,	// decimal(12,0) UN
			$user_cash				= 0,	// decimal(12,0) UN
			$user_empiric_a			= 0,	// decimal(12,0) UN
			$user_empiric_b			= 0,	// decimal(12,0) UN
			$user_friend_count		= 0,	// decimal(12,0) UN
			$user_blacklist_count	= 0,	// decimal(12,0) UN
			$user_insert_time		= 0,	// int(10) UN
			$user_login_time		= 0,	// int(10) UN
			$user_delete_flag		= 'F',	// enum('T','F')
			$user_active_flag		= 'F',	// enum('T','F')
			$user_admin_add_flag	= 'F',	// enum('T','F')
			$user_edit_time			= 0,	// int(10) UN
			$user_edit_id			= '';	// varchar(45)

	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'user',
				'primary_key'	=> 'user_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $user_seq
	 * @return single array
	 */
	public function loadBySeq( $user_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}`=" . Database::escapeString( $user_seq ),
				'`user_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}
	/**
	 * load data by user seqs
	 * @param (array) $user_seqs
	 * @return multi array
	 */
	public function loadBySeqs(array $user_seqs )
	{
		if(empty( $user_seqs )) return array();
		foreach( $user_seqs AS &$user_seq)
		{
			$user_seq	= Database::escapeString( $user_seq );
		}
		$this->setWhere(array(
				"`{$this->primary_key}` IN(" . implode(',', $user_seqs ) . ")",
				));
		return $this->select();
	}

	/**
	 * @param (string) $user_id
	 * @return (boolean)
	 */
	public function isExist( $user_id )
	{
		$this->setFields(array(
				'1',
		));

		$this->setWhere(array(
				"`user_id`=" . Database::escapeString( $user_id ),
		));
		$data	= $this->select( MYSQL_ROW_ASSOC );
		return !empty( $data );
	}


	/**
	 * load row data by user id
	 * @param (string) $user_id
	 * @return single array
	 */
	public function loadByUsrId( $user_id )
	{
		$this->setWhere(array(
				"`user_id`=" . Database::escapeString( $user_id ),
				'`user_delete_flag` = "F"',
				));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$where[]	= '`user_delete_flag` = "F"';
		$this->setFields(array(
				'`user_seq`',
				'`user_id`',
				'`user_point`',
				'`user_cash`',
				'`user_empiric_a`',
				'`user_empiric_b`',
				'`user_friend_count`',
				'`user_blacklist_count`',
				'`user_active_flag`',
				'`user_insert_time`',
				'`user_login_time`',
				'`user_admin_add_flag`',
				'`user_edit_time`',
				'`user_edit_id`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`user_seq` DESC'));
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
				'user_id'				=> $this->user_id,
				'user_password'			=> $this->user_password,
				'user_point'			=> $this->user_point,
				'user_cash'				=> $this->user_cash,
				'user_empiric_a'		=> $this->user_empiric_a,
				'user_empiric_b'		=> $this->user_empiric_b,
				'user_friend_count'		=> $this->user_friend_count,
				'user_blacklist_count'	=> $this->user_blacklist_count,
				'user_insert_time'		=> $this->editTime('user_insert_time'),
				'user_login_time'		=> $this->user_login_time,
				'user_delete_flag'		=> $this->user_delete_flag,
				'user_active_flag'		=> $this->user_active_flag,
				'user_admin_add_flag'	=> $this->user_admin_add_flag,
				'user_edit_time'		=> $this->editTime('user_edit_time'),
				'user_edit_id'			=> $this->editTime('user_edit_id'),
		));
		return parent::insert($option);
	}

	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function update()
	{
		//set log keys
		$this->log_keys	= array( $this->user_seq );

		$this->setFields(array(
				'user_active_flag'		=> $this->user_active_flag,
				'user_edit_time'		=> $this->editTime('user_edit_time'),
				'user_edit_id'			=> $this->editId('user_edit_id'),
		));
		$this->setWhere(array(
				'`user_seq`=' . Database::escapeString( $this->user_seq ),
		));
		return parent::update();
	}

	public function updateLoginTime()
	{
		//set log keys
		$this->log_keys				= array( $this->user_seq );

		$this->setFields(array(
				'user_login_time'		=> $this->editTime('user_login_time'),
				'user_edit_time'		=> $this->editTime('user_edit_time'),
				'user_edit_id'			=> $this->editId('user_edit_id'),
		));
		$this->setWhere(array(
				'`user_seq`=' . Database::escapeString( $this->user_seq ),
		));
		return parent::update();
	}

	/**
	 * delete multi row
	 * @param (array) $user_group_seqs
	 * @return boolean
	 */
	public function delete(array $user_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $user_seqs;

		$this->setFields(array(
				'user_delete_flag'	=> 'T',
				'user_edit_time'	=> $this->editTime('user_edit_time'),
				'user_edit_id'		=> $this->editId('user_edit_id'),
		));
		$this->setWhere(array(
				"`user_seq` in('" . implode( "','", $user_seqs ) . "')"
		));
		return parent::update();
	}

	/**
	 * set user_group_use_flag = 'T'
	 * @param (array) $user_group_seqs
	 * @return boolean
	 */
	public function used(array $user_seqs )
	{
		//set log keys
		$this->log_keys	= $user_seqs;

		$this->setFields(array(
				'user_active_flag'	=> 'T',
				'user_edit_time'	=> $this->editTime('user_edit_time'),
				'user_edit_id'		=> $this->editId('user_edit_id'),
		));
		$this->setWhere(array(
				"`user_seq` in('" . implode( "','", $user_seqs ) . "')"
		));
		return parent::update();
	}
	/**
	 * set user_group_use_flag = 'F'
	 * @param (array) $user_group_seqs
	 * @return boolean
	 */
	public function unused(array $user_seqs )
	{
		//set log keys
		$this->log_keys	= $user_seqs;

		$this->setFields(array(
				'user_active_flag'	=> 'F',
				'user_edit_time'	=> $this->editTime('user_edit_time'),
				'user_edit_id'		=> $this->editId('user_edit_id'),
		));
		$this->setWhere(array(
				"`user_seq` in('" . implode( "','", $user_seqs ) . "')"
		));
		return parent::update();
	}

	/**
	 * fileds validation
	 */
	public function validation( $key, $value )
	{
		if(is_array( $value ))
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
				case 'user_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new MsgException('User seq error.');
					break;
				case 'user_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new MsgException('用户ID错误.【必须是1到45个字符!】');
					if( self::vdtReg( $value, '@^\w+$@i') == FALSE ) throw new MsgException('用户ID错误.【用户名只能有字母数字或者下划线组成!】');
					break;
				case 'user_password':
					if( self::vdtStr( $value, 6, 100 ) == FALSE ) throw new MsgException('用户密码错误.【必须是6到100个字符!】.');
					break;
				case 'user_point':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User point error.');
					break;
				case 'user_cash':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User cash error.');
					break;
				case 'user_empiric_a':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User empiric a error.');
					break;
				case 'user_empiric_b':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User empiric b error.');
					break;
				case 'user_friend_count':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User friend count error.');
					break;
				case 'user_blacklist_count':
					if( self::vdtDec( $value, 12, 0, TRUE ) == FALSE ) throw new MsgException('User blacklist count error.');
					break;
				case 'user_insert_time':
					if( self::vdtInt( $value, 0, 4294967295 ) == FALSE ) throw new MsgException('User insert time error.');
					break;
				case 'user_login_time':
					if( self::vdtInt( $value, 0, 4294967295 ) == FALSE ) throw new MsgException('User login time error.');
					break;
				case 'user_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new MsgException('User delete flag error.');
					break;
				case 'user_active_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new MsgException('User active flag error.');
					break;
				case 'user_admin_add_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new MsgException('User active flag error.');
					break;
				case 'user_edit_time':
					if( self::vdtInt( $value, 0, 4294967295 ) == FALSE ) throw new MsgException('User edit time error.');
					break;
				case 'user_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new MsgException('User edit id error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) $this->$method( $value );
					else throw new MsgException('Param error.');
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
		$data['user_insert_time_vm']	= empty( $data['user_insert_time'] ) ? '-' : date( 'Y-m-d H:i:s', $data['user_insert_time'] );
		$data['user_login_time_vm']		= empty( $data['user_login_time'] ) ? '-' : date( 'Y-m-d H:i:s', $data['user_login_time'] );
		$data['user_edit_time_vm']		= empty( $data['user_edit_time'] ) ? '-' : date( 'Y-m-d H:i:s', $data['user_edit_time'] );
		return $data;
	}
	/**
	 * Encryption after return
	 * @param (string) $password
	 */
	public function encryptsPassword( $password )
	{
		if(self::vdtStr( $password, 6,  40 ) == FALSE ) throw new MsgException('User password Length error.');
		return sha1(md5( $password ) . strlen( $password ));
	}
}