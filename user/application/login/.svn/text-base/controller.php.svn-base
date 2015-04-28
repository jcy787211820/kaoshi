<?php
includeTraits('Login');
includeTraits('Permission');
use \core\CException AS CException,
	\core\MsgException AS MsgException,
	\core\Controller AS Controller,
	\core\Go AS Go;
/**
 * user login
 */
class Login extends Controller
{
	use \traits\login,
		\traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->checkPermission();
	}
	public function index()
	{
		/***********************************************************************************************************************
		 * VAR DATA
		***********************************************************************************************************************/
		/**********************************************************************************************************************/
//var_dump($assign_data);
		/**********************************************************************************************************************
		 * RESULT
		***********************************************************************************************************************/
		parent::layout('blank');
		parent::view();
		/**********************************************************************************************************************/
	}

	public function action()
	{
		/*
		 * check captcha
		 */
		$this->_checkCaptcha(initPost('captcha'));

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
		$user_password		= $user_manager->validation( 'user_password',	initPost('user_password', FALSE, FALSE));

		/*
		 * login action
		 */
		$assign_data['error']	= $this->frontLogin( $user_id, $user_password ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode($assign_data);
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
		$session_name		= md5('user_captcha_join');
		$session_path		= '/login';
		$session_domain		= $_SERVER['SERVER_NAME'];
		$session_secure		= FALSE;
		$session_httponly	= TRUE;
		$image_width		= 50;
		$image_height		= 30;

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
