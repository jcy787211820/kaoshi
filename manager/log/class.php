<?php
/**
 * Log manager
 */
namespace manager;
use	\core\Router AS Router,
	\core\Config AS Config;
class Log extends Manager
{
	const
		LOG_TYPE_INSERT				= '1',
		LOG_TYPE_UPDATE				= '2',
		LOG_TYPE_DELETE				= '3';
	public	$log_seq				= 0,					// int(10) UN PK AI
			$log_type				= 0,					// tinyint(1) UN
			$log_table				= '',					// varchar(45)
			$log_key				= '',					// varchar(100)
			$log_data				= '',					// longtext
			$log_router				= '',					// varchar(500)
			$log_insert_time		= 0,					// int(10) UN
			$log_insert_id			= '',					// varchar(45)
			$log_insert_ip			= '000.000.000.000';	// varchar(45)

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'log',
				'primary_key'	=> 'log_seq',
				'create_log'	=> FALSE,
		));
	}

	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert($option=array())
	{
		$this->log_data		= json_encode( $this->log_data );
		$this->log_router	= Router::$controller . '->' . Router::$class . '->' . Router::$function . '->' . implode(',', Router::$params );

		$this->setFields(array(
			'log_type'				=> $this->log_type,
			'log_table'				=> $this->log_table,
			'log_key'				=> $this->log_key,
			'log_data'				=> $this->log_data,
			'log_router'			=> $this->log_router,
			'log_insert_time'		=> $this->editTime('log_insert_time'),
			'log_insert_id'			=> $this->editId('log_insert_id', FALSE ),
			'log_insert_ip'			=> $_SERVER['REMOTE_ADDR'],
		));
		return parent::insert($option);
	}

	public function insertByLogKeys( $log_keys )
	{
		$this->log_data		= json_encode( $this->log_data );
		$this->log_router	= Router::$controller . '->' . Router::$class . '->' . Router::$function . '->' . implode(',', Router::$params );

		$fields				= array();
		foreach( $log_keys AS $log_key )
		{
			$fields[]					= array(
				'log_type'				=> $this->log_type,
				'log_table'				=> $this->log_table,
				'log_key'				=> $log_key,
				'log_data'				=> $this->log_data,
				'log_router'			=> $this->log_router,
				'log_insert_time'		=> $this->editTime('log_insert_time'),
				'log_insert_id'			=> $this->editId('log_insert_id', FALSE ),
				'log_insert_ip'			=> $_SERVER['REMOTE_ADDR'],
			);
		}
		$this->setFields( $fields );
		return parent::insert();
	}
}
