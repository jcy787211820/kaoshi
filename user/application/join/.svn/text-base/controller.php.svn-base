<?php
includeTraits('Permission');
includeTraits('User');
use \core\CException AS CException,
	\core\MsgException AS MsgException,
	\core\Controller AS Controller,
	\core\Go AS Go;
class Join extends Controller
{
	use \traits\user,
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
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$user_manager			= Go::manager('User');

		/*
		 * post data
		 */
		$user_manager->user_id			= $user_manager->validation( 'user_id',			initPost('user_id'));
		$user_manager->user_password	= $user_manager->validation( 'user_password',	initPost('user_password', FALSE, FALSE));
		$captcha						= initPost('captcha');
		$user_password_confirm			= initPost('user_password_confirm', FALSE, FALSE);

		/*
		 * check
		 */
		$this->_checkCaptcha( $captcha );
		if( $user_manager->user_password != $user_password_confirm ) throw new MsgException('两次输入密码不一致.');
		if( $this->isExist(false) == TRUE ) throw new MsgException('用户Id已经存在,请重新填写!');

		/*
		 * init data
		 */
		$user_manager->user_active_flag		= 'T';
		$user_manager->user_admin_add_flag	= 'F';
		$user_manager->user_password		= $user_manager->encryptsPassword( $user_manager->user_password );
		$user_infos							= array();

		/*
		 * process
		 */
		$assign_data['error']	= self::newUser( $user_manager, $user_infos ) !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	/**
	 * user id is exist
	 */
	public function isExist( $echo = TRUE )
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$user_manager			= Go::manager('User');

		/*
		 * post data
		 */
		$user_id					= $user_manager->validation( 'user_id',		initPost('user_id'));
		$assign_data['is_exist']	= $user_manager->isExist( $user_id );
		$assign_data['error']		= FALSE;


		/*
		 * return
		 */
		if( $echo == TRUE ) echo json_encode( $assign_data );
		return $assign_data['is_exist'];
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
		$session_path		= '/join';
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
