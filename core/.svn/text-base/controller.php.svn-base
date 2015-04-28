<?php
/**
 * Base Controller
 */
namespace core;
class Controller
{
	/**
	 * Contruct
	 */
	public function __construct(){}

	/**
	 * set view page layout
	 * @param (string) $name
	 */
	public function layout( $name = 'default' )
	{
		View::layout( $name );
	}
	/**
	 * get view page
	 * @param (array) $assign_data
	 * @param (int) $expires
	 */
	public function view( $assign_data = array(), $expires = 0)
	{
		/*
		 * layout
		 */
		if( View::$layout == NULL ) $this->layout();

		/*
		 * set view page related layout file
		 */
		$parse_request_uri_arr	= explode( DIRECTORY_SEPARATOR, $_SERVER['REQUEST_URI'] );
		$left_menu_file	= Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . 'left.html';
		if(Config::base('LAYOUT_TYPE') == 'admin')
		{
			$left_menu_name	= $parse_request_uri_arr[1];
			$left_menu_file	= Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . 'left_' . $left_menu_name . '.html';
		}
		if(is_file( $left_menu_file )) $assign_data['left']	= file_get_contents( $left_menu_file );
		$header_file	= Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . 'header.html';
		if(is_file( $header_file )) $assign_data['header']	= file_get_contents( $header_file );
		$footer_file	= Config::base('LAYOUT_DIR') . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR . 'footer.html';
		if(is_file( $footer_file )) $assign_data['footer']	= file_get_contents( $footer_file );

		/*
		 * set view var data
		 */
		View::setAssignData( $assign_data );

		/*
		 * get view file content
		 */
		View::view();

		/*
		 * output
		 */
		view::output( $expires );
	}
}
