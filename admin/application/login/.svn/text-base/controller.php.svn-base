<?php
includeTraits('Login');
includeTraits('Permission');
use \core\CException AS CException,
	\core\MsgException AS MsgException,
	\core\Controller AS Controller,
	\core\Go AS Go;
/**
 * admin login
 */
class Login extends Controller
{
	private	$_login_form_cookie_name;

	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->_login_form_cookie_name	= md5($_SERVER['SERVER_NAME'] . 'admin_login');
	}

	/**
	 * login form
	 */
	public function index()
	{
		/*
		 * VAR DATA
		 */
		$assign_data	= array();

		/*
		 * read cookie data, process login action
		 */
		$login_data						= isset( $_COOKIE[$this->_login_form_cookie_name] ) ? json_decode( $_COOKIE[$this->_login_form_cookie_name], TRUE ) : array();
		$assign_data['user_id']			= isset( $login_data['user_id'] ) ? $login_data['user_id'] : '';
		$assign_data['user_password']	= isset( $login_data['user_password'] ) ? $login_data['user_password'] : '';
		$assign_data['remember']		= isset( $login_data['remember'] ) ? $login_data['remember'] : 'F';

		parent::layout('blank');
		parent::view( $assign_data );
	}
	/**
	 * logout
	 */
	public function logout()
	{
		/*
		 * action
		 */
		$assign_data	= array('error' => TRUE);
		$this->adminLogOut();
		$assign_data['error']	= FALSE;

		/*
		 * echo result
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * login action
	 */
	public function action()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		*/
		$user_manager		= Go::manager('User');

		/*
		 * post data
		 */
		$user_id			= $user_manager->validation( 'user_id',			initPost('user_id'));
		$user_password		= $user_manager->validation( 'user_password',	initPost('user_password', FALSE, FALSE ));
		$remember			= $user_manager->validation( 'vdt_flg',			initPost('remember', 'F'));

		/*
		 * cookie
		 */
		if( $remember == 'T' )
		{
			$cookie_value		= json_encode(array(
					'user_id'		=> $user_id,
					'user_password'	=> $user_password,
					'remember'		=> 'T',
			));
			setcookie( $this->_login_form_cookie_name, $cookie_value, 0, '/login', $_SERVER['SERVER_NAME'], FALSE, TRUE );
		}
		else
		{
			setcookie( $this->_login_form_cookie_name, NULL, 1 );
		}

		/*
		 * check captcha
		 */
		$this->_checkCaptcha(initPost('captcha'));

		/*
		 * login action
		 */
		$assign_data['error']	= $this->adminLogin( $user_id, $user_password ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	/**
	 * make captcha
	 */
	public function captcha()
	{
		$this->_captchaPlugin('generateImage');
	}

	/**
	 * get captcha plugin
	 * @return unknown
	 */
	private function _captchaPlugin( $action, $captcha = NULL )
	{
		/*
		 * VAR
		 */
		$session_name		= md5('admin_captcha_login');
		$session_path		= '/login';
		$session_domain		= $_SERVER['SERVER_NAME'];
		$session_secure		= FALSE;
		$session_httponly	= TRUE;
		$image_width		= 145;
		$image_height		= 20;

		/*
		 * return captcha_plugin
		 */
		$captcha_plugin	= Go::plugin('Captcha');
		$captcha_plugin->init( $session_name, $session_path, $session_domain, $session_secure, $session_httponly, $image_width, $image_height );
		return $captcha_plugin->$action( $captcha );
	}

	/**
	 * check captcha
	 */
	private function _checkCaptcha( $captcha )
	{
		/*
		 * check
		 */
		if( $this->_captchaPlugin('check', $captcha ) == FALSE ) throw new MsgException('验证码错误.');

		/*
		 * return
		 */
		return TRUE;
	}
}
