<?php
/**
 * user group manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;

class UserGroup extends Manager
{
	public	$user_group_seq			= 0,	//	tinyint(3) UN PK AI
			$user_group_code		= '',	//	varchar(30)
			$user_group_name		= '',	//	varchar(45)
			$user_group_description	= '',	//	varchar(200)
			$user_group_use_flag	= 'F',	//	enum('T','F')
			$user_group_delete_flag	= 'F',	//	enum('T','F')
			$user_group_edit_time	= '0',	//	int(10) UN
			$user_group_edit_id		= '';	//	varchar(45)



	public function __construct()
	{
		parent::__construct(array(
			'database'		=> Config::database('default')['DATABASE'],
			'table'			=> Config::database('default')['TABLE_PERFIX'] . 'user_group',
			'primary_key'	=> 'user_group_seq',
			'create_log'	=> TRUE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $user_group_seq
	 * @return single array
	 */
	public function loadBySeq( $user_group_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}`=" . Database::escapeString( $user_group_seq ),
				'`user_group_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load row data by User Group Code
	 * @param (int) $user_group_code
	 * @return single array
	 */
	public function loadByCode( $user_group_code )
	{
		$this->setWhere(array(
				'`user_group_code` = ' . Database::escapeString( $user_group_code ),
				'`user_group_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load children data
	 * @param (string) $user_group_parent_code
	 * @return double array
	 */
	public function loadByParentCode( $user_group_parent_code )
	{
		$lists	= array();
		$this->setWhere(array(
				'`user_group_code` LIKE ' . Database::escapeString( $user_group_parent_code . '__' ),
				'`user_group_delete_flag` = "F"',
		));

		$this->setOrder(array('`user_group_seq` ASC'));
		return $this->select( MYSQL_ASSOC );
	}


	/**
	 * load data by user group codes
	 * @param (array) $user_group_codes
	 * @return multi array
	 */
	public function loadByCodes(array $user_group_codes )
	{
		if(empty( $user_group_codes )) return array();

		$this->setWhere(array(
				"`user_group_code` IN(" . implode(',', array_map(array('\core\Database','escapeString'), $user_group_codes )) . ")",
		));
		return $this->select();
	}

	/**
	 * load data by user group seqs
	 * @param (array) $user_group_seqs
	 * @return multi array
	 */
	public function loadNameByCodes(array $user_group_codes )
	{
		/*
		 * var data
		 */
		$result	= array();

		/*
		 * set fileds
		 */
		$this->setFields(array(
				'user_group_code',
				'user_group_name',
		));

		/*
		 * set return data
		 */
		$data	= $this->loadByCodes( $user_group_codes );
		foreach( $data AS $value )
		{
			$user_group_code			= $value['user_group_code'];
			$result[$user_group_code]	= $value['user_group_name'];
		}

		/*
		 * return
		 */
		return $result;
	}

	/**
	 * load Used
	 * @return array
	 */
	public function loadUsed()
	{
		/*
		 * set where
		*/
		$this->setWhere(array(
				'`user_group_use_flag` = "T"',
				'`user_group_delete_flag` = "F"',
		));
		/*
		 * load
		*/
		return $this->select();
	}
// 	/**
// 	 * load Used tree
// 	 * @return multi array
// 	 */
// 	public function loadUsedTree()
// 	{
// 		/*
// 		 * set where
// 		*/
// 		$this->setWhere(array(
// 				'`user_group_use_flag` = "T"',
// 				'`user_group_delete_flag` = "F"',
// 		));
// 		/*
// 		 * load
// 		*/
// 		return $this->loadTree();
// 	}
// 	/**
// 	 * load Tree
// 	 * @return multi array
// 	 */
// 	public function loadTree()
// 	{
// 		/*
// 		 * select
// 		*/
// 		$lists	= array();
// 		$lists	= $this->select();
// 		foreach( $lists AS $key => $list )
// 		{
// 			$this->format( $list );
// 			$lists[$key]	= $list;
// 		}

// 		/*
// 		 * sort
// 		*/
// 		$sort_user_group_parent_seqs	= array();
// 		foreach($lists AS $key => $list )
// 		{
// 			$sort_user_group_parent_seqs[$key]	= $list['user_group_parent_seq'];
// 		}
// 		array_multisort( $sort_user_group_parent_seqs, SORT_ASC, $lists );

// 		/*
// 		 * make tree
// 		*/
// 		$tree	= array();
// 		$this->makeTree( $lists, $tree );
// 		return $tree;
// 	}

// 	/**
// 	 * make tree
// 	 * @param (array) $user_groups
// 	 * @param (array) $tree
// 	 * @param (int) $parent_seq
// 	 */
// 	public function makeTree( &$user_groups, &$tree, $parent_seq = 0 )
// 	{
// 		foreach( $user_groups AS $key => $value )
// 		{
// 			if( $parent_seq == $value['user_group_parent_seq'] )
// 			{
// 				$user_group_seq			= $value['user_group_seq'];
// 				$tree[$user_group_seq]	= $value;
// 				unset( $user_groups[$key] );
// 				$tree[$user_group_seq]['children']	= array();
// 				$this->makeTree( $user_groups, $tree[$user_group_seq]['children'], $user_group_seq );
// 			}
// 		}
// 	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert( $user_group_parent_code = NULL )
	{
		$this->user_group_code	= "(
			SELECT IFNULL(LPAD(CONV(CONV(MAX(`user_group_code`),36,10)+1,10,36)," . (strlen((string) $user_group_parent_code ) + 2) . ",'0'),'{$user_group_parent_code}00')
			FROM `{$this->database}`.`{$this->table}` AS `tmp`
			WHERE `user_group_code` LIKE " . Database::escapeString( $user_group_parent_code . '__' ) . "
		)";
		$this->setUnescapes(array('user_group_code'));

		$this->setFields(array(
				'user_group_code'		=> $this->user_group_code,
				'user_group_name'		=> $this->user_group_name,
				'user_group_description'=> $this->user_group_description,
				'user_group_use_flag'	=> $this->user_group_use_flag,
				'user_group_edit_time'	=> $this->editTime('user_group_edit_time'),
				'user_group_edit_id'	=> $this->editId('user_group_edit_id'),
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
		$this->log_keys	= array( $this->user_group_seq );

		$this->setFields(array(
				'user_group_name'		=> $this->user_group_name,
				'user_group_description'=> $this->user_group_description,
				'user_group_use_flag'	=> $this->user_group_use_flag,
				'user_group_edit_time'	=> $this->editTime('user_group_edit_time'),
				'user_group_edit_id'	=> $this->editId('user_group_edit_id'),
		));
		$this->setWhere(array(
				'user_group_seq'		=> '`user_group_seq`=' . Database::escapeString( $this->user_group_seq ),
		));

		return parent::update();
	}
	/**
	 * delete multi row
	 * @param (array) $user_group_seqs
	 * @return boolean
	 */
	public function delete(array $user_group_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $user_group_seqs;

		$this->setFields(array(
				'user_group_delete_flag'	=> 'T',
				'user_group_edit_time'		=> $this->editTime('user_group_edit_time'),
				'user_group_edit_id'		=> $this->editId('user_group_edit_id'),
		));
		$this->setWhere(array(
				"`user_group_seq` in('" . implode( "','", $user_group_seqs ) . "')"
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
				case 'user_group_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('User group seq error.');
					break;
				case 'user_group_parent_code':
					if(!empty( $value )) self::validation('user_group_code', $value );
					break;
				case 'user_group_code':
					if( self::vdtStr( $value, 1, 30 ) == FALSE || self::vdtReg( $value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('User Group Code error.');
					break;
				case 'user_group_description':
					if( self::vdtStr( $value, 0, 200 ) == FALSE ) throw new CException('User group description error.');
					break;
				case 'user_group_name':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('User group name error.');
					break;
				case 'user_group_use_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('User group use flag error.');
					break;
				case 'user_group_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('User group delete flag error.');
					break;
				case 'user_group_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('User group edit time error.');
					break;
				case 'user_group_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('User group edit id error.');
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
		if(isset( $data['user_group_edit_time'] ) == TRUE )
		{
			$data['user_group_edit_time_vm']	= $data['user_group_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['user_group_edit_time'] ) : '';
		}

		return $data;
	}
}