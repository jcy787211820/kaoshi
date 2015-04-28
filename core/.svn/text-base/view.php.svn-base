<?php
/**
 * view html
 */
namespace core;
class View
{
	/**
	 * var data
	 */
	public static	$layout,						// layout file path
					$view,							// view file path
					$assign_data		= array(	// base assign_data
						'title'			=> '',		// meta title
						'keywords'		=> '',		// meta keywords
						'description'	=> '',		// meta description
						'header'		=> '',		// layout header
						'left'			=> '',		// layout left
						'right'			=> '',		// layout right
						'footer'		=> '',		// layout footer
						'js'			=> '',		// view js
						'css'			=> '',		// view css

					);		//view php var
	/**
	 * Construct
	 */
	public function __construct(){}

	/**
	 * set assign data
	 * @param (array) $assign
	 */
	public static function setAssignData(array $assign )
	{
		self::$assign_data	= array_merge( self::$assign_data, $assign );
	}
	/**
	 * set layout file
	 * @param (string) $file
	 * @throws CException
	 */
	public static function layout( $file = 'default' )
	{
		if(is_file( Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . $file . '.html' ) == FALSE ) throw new CException('This layout is not found.');
		self::$layout	= Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . $file . '.html';
	}

	/**
	 * set view file
	 * @throws CException
	 */
	public static function view()
	{
		$file	= dirname( Router::$controller ) . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . Router::$function . '.html';
		if(is_file( $file ) == FALSE) throw new CException('Not found view file.');
		self::$view	= $file;
	}

	/**
	 * display view html
	 * @param (int) $cache_expires
	 */
	public static function output( $cache_expires = 0 )
	{
		ob_start();
		/*
		 * set view page var data
		 */
		foreach( self::$assign_data AS $key => $data )
		{
			$$key	= $data;
		}

		/*
		 * set js content
		 */
		$js			= '';
		$js_file	= dirname( Router::$controller ) . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . Router::$function . '.js';
		if(is_file( $js_file ))
		{
			include $js_file;
			$js	= ob_get_clean();
		}

		/*
		 * set css content
		 */
		$css		= '';
		$css_file	= dirname( Router::$controller ) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . Router::$function . '.css';
		if(is_file( $css_file )) $css	= file_get_contents( $css_file );

		/*
		 * output
		 */
		ob_start();
		include self::$view;
		if(!empty( self::$layout ))
		{
			$view	= ob_get_clean();
			include self::$layout;
		}
		if( $cache_expires > 0)	// write cache
		{
			if(is_dir( Config::base('CACHE_DIR') ) == FALSE )	mkdir( Config::base('CACHE_DIR') );
			$cache_file	= Config::base('CACHE_DIR') . Router::key();
			file_put_contents( $cache_file, $cache_expires . ob_get_contents() );
		}
		while (ob_get_level() > 0)
		{
            ob_end_flush();
        }
	}
}