<?php
/**
 * Permission manager
 */
namespace manager;
use	\core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;
class Permission extends Manager
{
	const
		PERMISSION_BASE_USER_GROUP	= 'base_user_group',
		PERMISSION_BASE_USER_LEVEL	= 'base_user_level',
		PERMISSION_BASE_USER		= 'base_user',
		PERMISSION_BASE_IP			= 'base_ip';
	public	$permission_seq			= 0,	// int(10) UN PK AI
			$permission_name		= '',	// varchar(45)
			$permission_action		= '',	// varchar(200)
			$permission_data		= '',	// varchar(2000)
			$permission_edit_time	= 0,	// int(10) UN
			$permission_edit_id		= '';	// varchar(45)

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'permission',
				'primary_key'	=> 'permission_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $permission_seq
	 * @return single array
	 */
	public function loadBySeq( $permission_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` =" . Database::escapeString( $permission_seq ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}
	/**
	 * load multi array data by permission action
	 * @param (array) $permission_actions
	 * @return multi array
	 */
	public function loadByActions( $permission_actions )
	{
		/*
		 * escape param
		 */
		foreach( $permission_actions AS $key => $permission_action )
		{
			$permission_actions[$key]	= Database::escapeString( $permission_action );
		}
		/*
		 * where
		 */
		$this->setWhere(array(
				"`permission_action` IN(" . implode(',', $permission_actions ) . ")",
		));
		/*
		 * load data
		 */
		return $this->select( MYSQL_ASSOC );
	}
	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$this->setFields(array(
				'`permission_seq`',
				'`permission_name`',
				'`permission_action`',
				'`permission_data`',
				'`permission_edit_time`',
				'`permission_edit_id`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`permission_seq` DESC'));
		$lists	= $this->select();
		$lists	= array_map(array($this,'format'), $lists);
		return $lists;
	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert( $option = array() )
	{
		$this->permission_data		= json_encode( $this->permission_data );
		$this->permission_action	= rtrim( $this->permission_action, DIRECTORY_SEPARATOR );

		$this->setFields(array(
			'permission_name'		=> $this->permission_name,
			'permission_action'		=> $this->permission_action,
			'permission_data'		=> $this->permission_data,
			'permission_edit_time'	=> $this->editTime('permission_edit_time'),
			'permission_edit_id'	=> $this->editId('permission_edit_id'),
		));
		return parent::insert( $option );
	}
	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function update()
	{
		//set log keys
		$this->log_keys	= array( $this->permission_seq );

		$this->permission_data		= json_encode( $this->permission_data );
		$this->permission_action	= rtrim( $this->permission_action, DIRECTORY_SEPARATOR );

		$this->setFields(array(
			'permission_name'		=> $this->permission_name,
			'permission_action'		=> $this->permission_action,
			'permission_data'		=> $this->permission_data,
			'permission_edit_time'	=> $this->editTime('permission_edit_time'),
			'permission_edit_id'	=> $this->editId('permission_edit_id'),
		));
		$this->setWhere(array(
				'`permission_seq`=' . Database::escapeString( $this->permission_seq ),
		));
		return parent::update();
	}

	/**
	 * delete multi row
	 * @param (array) $user_level_seqs
	 * @return boolean
	 */
	public function delete(array $permission_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $permission_seqs;

		$this->setWhere(array(
				"`permission_seq` in('" . implode( "','", $permission_seqs ) . "')"
		));
		return parent::delete();
	}

	/**
	 * fileds validation
	 */
	public function validation( $key, $value, $is_multi	= FALSE )
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
				case 'permission_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Permission seq error.');
					break;
				case 'permission_name':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Permission name error.');
					break;
				case 'permission_action':
					if( self::vdtStr( $value, 1, 200 ) == FALSE ) throw new CException('Permission action error.');
					break;
				case 'permission_data':
					$tmp_value	= (array) json_decode( $value );
					foreach( array_keys( $tmp_value ) AS $permission_data_key )
					{
						if(!isset( $this->getPermissionDataKeys()[$permission_data_key] )) throw new CException('Permission data error.');
					}
					if( self::vdtStr( $value , 1, 2000 ) == FALSE ) throw new CException('Permission data error.');
					break;
				case 'permission_data_vm':
					foreach( array_keys( $value ) AS $permission_data_key )
					{
						if(!isset( $this->getPermissionDataKeys()[$permission_data_key] )) throw new CException('Permission data vm error.');
					}
					if( self::vdtStr( json_encode( $value ), 1, 2000 ) == FALSE ) throw new CException('Permission data vm error.');
					break;
				case 'permission_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Permission edit time error.');
					break;
				case 'permission_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Permission edit id error.');
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
		if(isset( $data['permission_data'] ))
		{
			$data['permission_data_vm']	= (array) json_decode( $data['permission_data'], TRUE );
			$permission_data_keys	= $this->getPermissionDataKeys();
			foreach( $data['permission_data_vm'] AS $type => $permission_data_vm )
			{
				if(!array_key_exists( $type, $permission_data_keys )) throw new CException('SYSTEM ERROR[permission_data].');
				$data['permission_data_vm'][$type]['is_out_flag']	= empty( $permission_data_vm['is_out_flag'] ) ? 'F' : 'T';
				$data['permission_data_vm'][$type]['data']			= empty( $permission_data_vm['data'] ) ? array() : $permission_data_vm['data'];
			}
		}
		if(isset( $data['permission_edit_time'] )) $data['permission_edit_time_vm']	= $data['permission_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['permission_edit_time'] ) : '';
		return $data;
	}
	/**
	 * get user level types
	 * @return array
	 */
	public function getPermissionDataKeys()
	{
		return array(
				self::PERMISSION_BASE_USER_GROUP	=> '用户组',
				self::PERMISSION_BASE_USER_LEVEL	=> '用户等级',
				self::PERMISSION_BASE_USER			=> '用户',
				self::PERMISSION_BASE_IP			=> 'IP',
		);
	}
}
