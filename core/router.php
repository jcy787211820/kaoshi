<?php
namespace core;
/**
 * Router find application class function, setting params;
 */
class Router
{
	/**
	 * var data
	 */
	public static	$controller	= NULL,			//Controller file
					$class		= NULL,			//Controller class
					$function	= NULL,			//Controller function
					$params		= NULL;			//Controller params
	/**
	 * sha1( this class vars value)
	 */
	public static function key()
	{
		return sha1(json_encode(get_class_vars(__CLASS__)));
	}
	/**
	 * run application controller
	 */
	public static function action()
	{
		/*
		 * Parse request URI
		 */
		self::parseUri();

		/*
		 * Use cache
		 */
		if(self::cache() == TRUE) exit;

		/*
		 * Include controller
		 */
		self::load();
	}

	/**
	 * include controller file
	 * @throws CException
	 */
	private static function load()
	{
		$controller	= new self::$class();
		$method		= self::$function;
		$params		= self::$params;
		if(method_exists( $controller, $method ) == FALSE ) throw new CException('This method is not find.');
		call_user_func_array(array( $controller, $method ), $params );
	}
	/**
	 * read cache
	 */
	private static function cache()
	{
		/*
		 * Var data
		 */
		$used	= FALSE;		// Is use cache. This is function return value.
		$file	= Config::base('CACHE_DIR') . self::key();	// Cache file.

		/*
		 * Read cache
		 */
		if(is_file( $file ) === TRUE)
		{
			$fopen		= fopen( $file, 'rb' );
			flock( $fopen, LOCK_EX );
			$expire_time	= fread( $fopen, 10 );		// File ten words before is unix time stamp.
			if( REQUEST_TIME < $expire_time )
			{
				$used	= TRUE;
				$size	= filesize( $file );
				if(feof( $fopen ) == FALSE)
				{
					echo fread( $fopen, $size );
				}
			}
			flock( $fopen, LOCK_UN );
			fclose( $fopen );
		}

		/*
		 * Return
		 */
		return $used;
	}
	/**
	 * set application php file.
	 * @throws CException
	 */
	private static function parseUri()
	{
		/*
		 * get URI path
		 */
		$uri_path	= parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		/*
		 * unset URI path in request data
		 */
		unset( $_GET[$uri_path], $_POST[$uri_path], $_REQUEST[$uri_path] );

		/*
		 * parse uri
		 */
		$parse_uri	= trim( $uri_path, DIRECTORY_SEPARATOR );
		$parse_uri	= empty( $parse_uri ) == TRUE ?  array( 'index' ) : explode( DIRECTORY_SEPARATOR, $parse_uri );

		/*
		 * set base property
		 */
		$tmp_path	= Config::base('APPLICATION_PATH');
		foreach( $parse_uri AS $key => $item )
		{
			$tmp_path	.= DIRECTORY_SEPARATOR . strtolower( $item );

			if(is_file( $tmp_path . DIRECTORY_SEPARATOR . 'controller.php' ) == TRUE )
			{
				self::$controller	= $tmp_path . DIRECTORY_SEPARATOR . 'controller.php';
				self::$class		= ucfirst( $item );
				self::$function		= empty( $parse_uri[$key + 1] ) ? 'index' : $parse_uri[$key + 1];
				self::$params		= array_splice( $parse_uri, $key + 2 );
				break;
			}

			if(empty( $parse_uri[$key + 1] ) == FALSE && is_dir( $tmp_path . DIRECTORY_SEPARATOR . strtolower( $parse_uri[$key + 1] )) == TRUE ) continue;
			throw new CException('Controller file is not found.');
		}
	}
}