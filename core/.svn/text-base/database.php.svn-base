<?php
/**
 * database
 */
namespace core;
use core\CException AS CException;
use \mysqli AS mysqli;

/**
 * select result type
 */
//define( 'MYSQL_ASSOC', 1 );
//define( 'MYSQL_NUM', 2 );
//define( 'MYSQL_BOTH', 3 );
define( 'MYSQL_ONE', 4 );
define( 'MYSQL_ROW_ASSOC', 5 );
define( 'MYSQL_ROW_NUM', 6 );
define( 'MYSQL_ROW_BOTH', 7 );
class Database
{
	public static	$resource		= NULL,		// database concent resource
					$trans_action	= FALSE;	// database used transaction
	/**
	 * construct
	 */
	public function __construct(){}

	/**
	 * use database
	 * @param (string) $name
	 * @param (boolean) $pconnect
	 */
	public static function db( $name = 'default' )
	{
		$config	= Config::database($name);
		self::connect( $config);
		self::setCharSet( $config['CHARSET'] );
	}
	/**
	 * connect database
	 * @param (array) $config
	 * @param (boolean) pconnect
	 * @throws CException
	 */
	public static function connect( $config )
	{
		self::$resource = new mysqli( $config['HOST'], $config['USER'], $config['PASSWORD'], $config['DATABASE'],$config['PORT']);
		if( self::$resource->connect_error != NULL ) throw new CException('Database connect failed. ~ ' . self::$resource->connect_error );
	}

	public static function close()
	{
		if( self::$resource != NULL ) self::$resource->close();
	}

	/**
	 * set database charset
	 * @param (string) $charset
	 * @throws CException
	 */
	public static function setCharSet( $charset )
	{
		if( self::$resource == FALSE ) throw new CException( 'Database could not connect.' );
		if( self::$resource->set_charset( $charset ) == FALSE)  throw new CException('Failed to set database character set. ~ ' . self::$resource->error);
	}

	/**
	 * Setting use database name
	 * @param (string) $database_name
	 * @throws CException
	 */
	public static function setDatabase( $database_name )
	{
		if( self::$resource == FALSE ) throw new CException( 'Database could not connect.' );
		if( self::$resource->select_db( $database_name ) == FALSE)	throw new CException('Failed to set database name. ~ ' . self::$resource->error);
	}

	/**
	 * query
	 * @param (string) $sql
	 * @throws CException
	 */
	public static function query( $sql )
	{
		if(is_string( $sql ) == FALSE )	throw new CException( 'Sql cannot be a string.' );
		if( self::$resource == NULL ) self::db();

		return self::$resource->query((string) $sql);
	}
	/**
	 * get select query data
	 * @param (resource) $query
	 * @param (int) $result_type
	 * @throws CException
	 * @return array
	 */
	public static function result( $query, $result_type = MYSQL_ASSOC )
	{
		/*
		 * var data
		 */
		$result	= array();

		/*
		 * make data
		 */
		switch( $result_type )
		{
 			case MYSQL_ASSOC:
 				while( $row	= $query->fetch_assoc())
 				{
 					$result[] = $row;
 				}
 				break;
 			case MYSQL_NUM:
 				while( $row	= $query->fetch_row())
 				{
 					$result[] = $row;
 				}
 				break;
 			case MYSQL_BOTH:
 				while( $row	= $query->fetch_array())
 				{
 					$result[] = $row;
 				}
 				break;
			case MYSQL_ROW_ASSOC:
				$result = $query->fetch_assoc();
				break;
			case MYSQL_ROW_NUM:
				$result = $query->fetch_row();
				break;
			case MYSQL_ROW_BOTH:
				$result = $query->fetch_array();
				break;
			case MYSQL_ONE:
				$row	= $query->fetch_row();
				$result	= is_array( $row ) ? current( $row ) : $row;
				break;
			default:
				throw new CException('Result type 错误. ~ ' . mysql_error());
		}
		$query->free();

		/*
		 * return
		 */
		return $result;
	}
	/**
	 * get affected rows.
	 */
	public static function affectedRows()
	{
		return self::$resource->affected_rows;
	}

	/**
	 * get last query primary key.
	 */
	public static function insertId()
	{
		return self::$resource->insert_id;
	}

	/**
	 * escape mysql string
	 * @param (string) $string
	 * @return string
	 */
	public static function escapeString( $string )
	{
		if( self::$resource == NULL ) self::db();
		$string	= self::$resource->real_escape_string( $string );
		return "'{$string}'";
	}

	/**
	 * transaction start
	 */
	public static function transtart()
	{
		if( self::$resource == NULL ) self::db();
		self::$resource->autocommit(FALSE);
		self::$trans_action	= TRUE;
	}

	/**
	 * transaction rollback
	 */
	public static function rollback()
	{
		self::$resource->rollback();
		self::$resource->autocommit(TRUE);
		self::$trans_action	= FALSE;
	}

	/**
	 * transaction rollback
	 */
	public static function commit()
	{
		self::$resource->commit();
		self::$resource->autocommit(TRUE);
		self::$trans_action	= FALSE;
	}

	public static function getTransActionStatus()
	{
		return self::$trans_action;
	}
}