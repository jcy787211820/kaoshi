<?php
/**
 * user manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;
class Session extends Manager
{
	public	$session_seq		= 0,					// int(10) UN PK AI
			$session_key		= '',					// char(40)
			$session_data		= '',					// varchar(2000)
			$session_expired	= 0,					// int(10) UN
			$session_edit_ip	= '000.000.000.000',	// varchar(15)
			$session_edit_time	= 0;					//int(10) UN

	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'session',
				'primary_key'	=> 'session_seq',
				'create_log'	=> FALSE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $user_seq
	 * @return single array
	 */
	public function loadByKey( $session_key )
	{
		$this->setWhere(array(
				"`session_key`=" . Database::escapeString( $session_key ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert($option=array())
	{
		/*
		 * make SQL
		 * @var (string) $sql
		 */
		$sql	= "
			INSERT INTO
				`{$this->database}`.`{$this->table}`
				(`session_key`,`session_data`,`session_expired`,`session_edit_ip`,`session_edit_time`)
			VALUES(
				" . Database::escapeString( $this->session_key) . ",
				" . Database::escapeString( $this->session_data ) . ",
				" . Database::escapeString( $this->session_expired ) . ",
				" . Database::escapeString( $_SERVER['REMOTE_ADDR'] ) . ",
				" . Database::escapeString( REQUEST_TIME ) . "
			)ON DUPLICATE KEY UPDATE
				`session_data` = " . Database::escapeString( $this->session_data ) . ",
				`session_expired` = " . Database::escapeString( $this->session_expired ) . ",
				`session_edit_ip` = " . Database::escapeString( $_SERVER['REMOTE_ADDR'] ) . ",
				`session_edit_time` = " . Database::escapeString( REQUEST_TIME );

		/*
		 * process
		 */
		$this->query	= Database::query( $sql );
		if( $this->query == FALSE ) throw new CException('SQL ERROR: ' . $sql );

		/*
		 * last insert id
		 */
		$this->{$this->primary_key}	= Database::insertId();

		/*
		 * return
		 */
		return $this->query;
	}

	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function updateExpired()
	{
		$this->setFields(array(
				'session_expired'	=> $this->session_expired,
				'session_edit_time'	=> REQUEST_TIME,
		));
		$this->setWhere(array(
				'`session_edit_ip`=' . Database::escapeString( $_SERVER['REMOTE_ADDR'] ),
				'`session_key`=' . Database::escapeString( $this->session_key ),
				'`session_seq`=' . Database::escapeString( $this->session_seq ),
		));
		return parent::update();
	}

	/**
	 * delete multi row
	 * @param (array) $user_group_seqs
	 * @return boolean
	 */
	public function deleteByKey()
	{
		$this->setWhere(array(
				'`session_key`=' . Database::escapeString( $this->session_key ),
				'`session_edit_ip`=' . Database::escapeString( $_SERVER['REMOTE_ADDR'] ),
		));
		return parent::delete();
	}

	/**
	 * data format
	 * @param (array) $data
	 */
	public function format( &$data )
	{
		$data['session_data_vm']	= empty( $data['session_data'] ) ? array() : json_decode( $data['session_data'], TRUE );
		return $data;
	}
}