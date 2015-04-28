<?php
/**
 * user info manager
 */
namespace manager;
use	\core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;
class UserInfo extends Manager
{
	const
		USER_INFO_TYPE_GROUP	= 1,
		USER_INFO_TYPE_NAME		= 2,
		USER_INFO_TYPE_NICK		= 3,
		USER_INFO_TYPE_EMAIL	= 4,
		USER_INFO_TYPE_PHONE	= 5,
		USER_INFO_TYPE_QQ		= 6,
		USER_INFO_TYPE_SEX		= 7,
		USER_INFO_TYPE_AREA		= 8,
		USER_INFO_TYPE_BIRTHDAY	= 9;


	public	$user_info_seq		= 0,	// int(10) UN PK AI
			$user_id			= 0,	// int(10) UN
			$user_info_type		= 0,	// tinyint(3) UN
			$user_info_value	= '';	// varchar(200)
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=>  Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'user_info',
				'primary_key'	=> 'user_info_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $user_info_seq
	 * @return single array
	 */
	public function loadBySeq( $user_info_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}`=" . Database::escapeString( $user_info_seq ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}
	/**
	 * load multi rows data by user id
	 * @param (int) $user_info_seq
	 * @return single array
	 */
	public function loadByUserId( $user_id )
	{
		$this->setWhere(array(
				"`user_id`=" . Database::escapeString( $user_id ),
		));
		return $this->select( MYSQL_ASSOC );
	}

	public function getUserGroupCodes( $user_id, $user_info_values = array())
	{
		$this->setFields(array(
				"`user_info_value`",
		));

		$this->setWhere(array(
				"`user_id`=" . Database::escapeString( $user_id ),
				"`user_info_type`=" . Database::escapeString( self::USER_INFO_TYPE_GROUP ),
		));

		if(!empty( $user_info_values ))
		{
			$user_group_where	= array();
			$user_info_values	= (array) $user_info_values;
			foreach( $user_info_values AS $user_info_value )
			{
				$user_group_where[]	= "`user_info_value` LIKE " . Database::escapeString( "{$user_info_value}%" );
			}
			$this->where[]	= '(' . implode(' OR ', $user_group_where ) .')';
		}


		$data	= $this->select( MYSQL_ASSOC );
		$user_group_seqs	= array();
		foreach( $data AS $value )
		{
			$user_group_seq						= $value['user_info_value'];
			$user_group_seqs[$user_group_seq]	= $user_group_seq;
		}

		return $user_group_seqs;
	}

	/**
	 * insert multi rows data
	 * @param (array) $data
	 * @return boolean
	 */
	public function inserts( $data )
	{
		/*
		 * init var is need trans action
		 */
		$need_trans_action	= !Database::getTransActionStatus();

		try
		{
			/*
			 * db transtart
			 */
			if( $need_trans_action == TRUE )
			{
				/********************/
				Database::transtart();
				/********************/
			}

			/*
			 * action
			 */
			$insert_rows	= 0;
			foreach( $data AS $fields )
			{
				$this->setFields( $fields );
				if(parent::insert() == TRUE) $insert_rows ++;
				else break;
			}

			if( $need_trans_action == TRUE )
			{
				/*****************/
				Database::commit();
				/*****************/
			}
		}
		catch(Exception $e)
		{
			if( $need_trans_action == TRUE )
			{
				/*******************/
				Database::rollback();
				/*******************/
			}
			throw $e;
		}

		if( $insert_rows == count( $data )) return TRUE;
		else return FALSE;
	}

	/**
	 * delete multi row
	 * @param (array) $user_level_seqs
	 * @return boolean
	 */
	public function delete(array $user_info_seqs = NULL )
	{
		//set log keys
		$this->log_keys	= $user_info_seqs;

		$this->setWhere(array(
				"`user_info_seq` in('" . implode( "','", $user_info_seqs ) . "')"
		));
		return parent::delete();
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
				case 'user_info_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('User info seq error.');
					break;
				case 'user_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('User id error.');
					break;
				case 'user_info_type':
					if( self::vdtInt( $value, 1, 255 ) == FALSE ) throw new CException('User info type error.');
					break;
				case 'user_info_value':
					if( self::vdtStr( $value, 0, 200 ) == FALSE ) throw new CException('User info value error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) $this->$method( $value );
					else throw new CException('Param error.');
			}
		}

		return $value;
	}

	public function validationUserInfo( $user_info_type, $user_info_value )
	{
		self::validation( 'user_info_type', $user_info_type );

		switch( $user_info_type )
		{
			case self::USER_INFO_TYPE_GROUP:
				if( self::vdtStr( $user_info_value, 1, 30 ) == FALSE || self::vdtReg( $user_info_value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('User info value error(GROUP).');
				break;
			case self::USER_INFO_TYPE_NAME:
				if( self::vdtStr( $user_info_value, 1, 45 ) == FALSE ) throw new CException('User info value error(NAME).');
				break;
			case self::USER_INFO_TYPE_NICK:
				if( self::vdtStr( $user_info_value, 1, 45 ) == FALSE ) throw new CException('User info value error(NICK).');
				break;
			case self::USER_INFO_TYPE_EMAIL:
				if(! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $user_info_value)) throw new CException('User info value error(EMAIL).');
				break;
			case self::USER_INFO_TYPE_PHONE:
				if(ctype_digit(strtr( $user_info_value , '-', '' )) == FALSE) throw new CException('User info value error(PHONE).');
				break;
			case self::USER_INFO_TYPE_QQ:
				if( self::vdtStr( $user_info_value, 1, 200 ) == FALSE ) throw new CException('User info value error(QQ).');
				break;
			case self::USER_INFO_TYPE_SEX:
				if( self::vdtStr( $user_info_value, 1, 200 ) == FALSE ) throw new CException('User info value error(SEX).');
				break;
			case self::USER_INFO_TYPE_AREA:
				if( self::vdtStr( $user_info_value, 1, 30 ) == FALSE || self::vdtReg( $user_info_value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('User info value error(AREA).');
				break;
			case self::USER_INFO_TYPE_BIRTHDAY:
				if( self::vdtDate( $user_info_value) == FALSE ) throw new CException('User info value error(AREA).');
				break;
			default:
				self::validation( 'user_info_value', $user_info_value );
		}
	}

	/**
	 * get user info types
	 * @return array
	 */
	public function getUserInfoTypes()
	{
		return array(
				self::USER_INFO_TYPE_GROUP		=> array( 'name'=>'所属用户组',	'max_input_num'=>0,	'is_must'=>TRUE ),
				self::USER_INFO_TYPE_NAME		=> array( 'name'=>'真实姓名',		'max_input_num'=>1,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_NICK		=> array( 'name'=>'昵称',		'max_input_num'=>1,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_EMAIL		=> array( 'name'=>'E-mail',		'max_input_num'=>0,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_PHONE		=> array( 'name'=>'联系电话',		'max_input_num'=>0,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_QQ			=> array( 'name'=>'QQ',			'max_input_num'=>0,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_SEX		=> array( 'name'=>'性别',		'max_input_num'=>1,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_AREA		=> array( 'name'=>'地区',		'max_input_num'=>1,	'is_must'=>FALSE ),
				self::USER_INFO_TYPE_BIRTHDAY	=> array( 'name'=>'生日',		'max_input_num'=>1,	'is_must'=>FALSE ),
		);
	}
}