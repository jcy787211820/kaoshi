<?php
/**
 * go
 */
namespace core;
use \core\Config AS Config;
class Go
{
	/**
	 * Construct
	 */
	public function __construct(){}

	/**
	 * load manager class
	 * @param (string) $manager
	 * @return object
	 */
	public static function manager( $manager )
	{
		/*
		 * var data
		 */
		$class		= "\manager\\{$manager}";
		$file		= Config::base('MANAGER_PATH') . DIRECTORY_SEPARATOR . lcfirst( $manager ) . DIRECTORY_SEPARATOR . 'class.php';

		/*
		 * include file
		 */
		if(is_file( $file ) == FALSE ) throw new CException( 'Manager name error. ~ ' . $manager );
		if(class_exists( $class ) == FALSE)
		{
			if(class_exists( '\manager\\Manager' ) == FALSE) require_once Config::base('MANAGER_PATH') . DIRECTORY_SEPARATOR . '__class.php';
			require_once $file;
		}

		/*
		 * return
		 */
		return new $class();
	}

	/**
	 * load plugin class
	 * @param (string) $plugin
	 * @return object
	 */
	public static function plugin( $plugin )
	{
		/*
		 * var data
		 */
		$class		= "\plugin\\{$plugin}";
		$file		= Config::base('PLUGIN_PATH') . DIRECTORY_SEPARATOR . lcfirst( $plugin ) . DIRECTORY_SEPARATOR . 'class.php';

		/*
		 * include file
		 */
		if(is_file( $file ) == FALSE ) throw new CException( 'Plugin name error. ~ ' . $plugin );
		if(class_exists( $class ) == FALSE)
		{
			require_once $file;
		}

		/*
		 * return
		 */
		return new $class();
	}
}