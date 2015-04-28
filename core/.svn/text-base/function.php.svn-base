<?php
use core\Router AS Router,
	core\Config AS Config,
	core\Security AS Security;
/**
 *	auto include class file
 */
function __autoload( $class )
{
	/*
	 * Auto include core Exception class
	 */
	if(stripos( $class, 'Exception' ) !== FALSE )
	{
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cexception' . '.php';
		if(is_file( $file ) == TRUE ) require_once $file;
		return;
	}


	/*
	 * Auto include class in core namespace
	 */
	if(strpos( $class, 'core\\') === 0 )
	{
		$this_dir	= dirname(__FILE__);
		$class		= str_replace('core\\','', strtolower( $class ));
		if(is_file($this_dir . DIRECTORY_SEPARATOR . $class . '.php') == TRUE ) require_once $this_dir . DIRECTORY_SEPARATOR . $class . '.php';
		return;

	}

	/*
	 * Auto include controller file
	 */
	if(class_exists('core\\Router'))
	{
		$pares_controller_arr	= explode( DIRECTORY_SEPARATOR . strtolower( $class ) . DIRECTORY_SEPARATOR, Router::$controller );
		if(empty( $pares_controller_arr[1] ) == FALSE && trim( $pares_controller_arr[1], DIRECTORY_SEPARATOR ) == 'controller.php' )
		{
			require_once Router::$controller;
			return;
		}
	}
}
/**
 *
 */
function includeTraits( $trait_name )
{
	if(trait_exists(ucfirst( $trait_name )) == FALSE )
	{
		$trait_name	= lcfirst( $trait_name );
		$trait_dir	= PROJECT_ROOT . DIRECTORY_SEPARATOR . 'traits' . DIRECTORY_SEPARATOR;
		$trait_path	= "{$trait_dir}{$trait_name}.php";
		if(is_readable( $trait_path ) == TRUE )
		{
			require_once $trait_path;
			return TRUE;
		}
	}
	RETURN FALSE;
}
/**
 * ajax request check
 */
function isAjax()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) == TRUE && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
}
/**
 * GET
 */
function initGet( $key, $init = NULL, $trim = TRUE )
{
	$value	= isset( $_GET[$key] ) ? $_GET[$key] : $init;
	$value	= Security::xssClean( $value, $trim );
	return $value;
}
/**
 * POST
 */
function initPost( $key, $init = NULL, $trim = TRUE )
{
	$value	= isset( $_POST[$key] ) ? $_POST[$key] : $init;
	$value	= Security::xssClean( $value, $trim );
	return $value;
}
/**
 * request
 */
function initRequest( $key, $init = NULL, $trim = TRUE )
{
	$value	= isset( $_REQUEST[$key] ) ? $_REQUEST[$key] : $init;
	$value	= Security::xssClean( $value, $trim );
	return $value;
}
/**
 * getConfigData
 */
function getBaseConfig( $config_name )
{
	return Config::base($config_name);
}

/**
 * encode seq
 */
function core_encode( $key )
{
	return urlencode(base64_encode( $key . $_SERVER['REMOTE_ADDR'] ));
}

/**
 * decode seq
 */
function core_decode( $key )
{
	$decode_key	= base64_decode(urldecode( $key ));
	$addr_index	= strrpos( $decode_key, $_SERVER['REMOTE_ADDR'] );

	return substr( $decode_key, 0, $addr_index );
}
/**
 * array_column php5.5 is existed
 */
if(!function_exists( 'array_column' ))
{
	function array_column( array $input , $column_key = NULL , $index_key = NULL )
	{
		$result_array = array();
		foreach( $input AS $input_key => $input_value )
		{
			$result_key					= is_null( $index_key ) ? $input_key : $input_value[$index_key];
			$result_array[$result_key]	= is_null( $column_key ) ? $input_value : $input_value[$column_key];
		}
		return $result_array;
	}
}