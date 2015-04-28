<?php
/**
 * configure
 */
namespace core;
use Core\CException;
class Config
{
	/**
	 * var data
	 */
	private static	$base		= NULL,		// base ini data
					$database	= NULL;		// database ini data

	/**
	 * Construct
	 * @throws CException
	 */
	public function __construct()
	{
		if(defined('CONFIG_PATH') == FALSE) throw new CException('Undefined config path.');
	}
	/**
	 * get base config data
	 * @see config/base.ini
	 * @throws CException
	 */
	public static function base( $key = NULL )
	{
		/*
		 * set base config data
		 */
		if( self::$base == NULL )
		{
			$ini_file	= CONFIG_PATH . DIRECTORY_SEPARATOR . 'base.ini';
			if(is_file( $ini_file ) == FALSE ) throw new CException('Not found base ini file.');
			self::$base	= parse_ini_file( $ini_file, FALSE );
		}

		/*
		 * return
		 */
		if( $key == NULL) return self::$base;
		if(isset( self::$base[$key] )) return self::$base[$key];
		return FALSE;
	}
	/**
	 * get database config data
	 * @see config/database.ini
	 * @throws CException
	 */
	public static function database( $key = NULL )
	{
		/*
		 * set database config data
		 */
		if(self::$database == NULL)
		{
			$ini_file	= CONFIG_PATH . DIRECTORY_SEPARATOR . 'database.ini';
			if(is_file( $ini_file ) == FALSE ) throw new CException('Not found database ini file.');
			self::$database	= parse_ini_file( $ini_file, TRUE );
		}

		/*
		 * return
		 */
		if( $key == NULL) return self::$database;
		if(isset( self::$database[$key] )) return self::$database[$key];
		return FALSE;
	}
}