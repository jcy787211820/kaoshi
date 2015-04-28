<?php
namespace traits;
use	\core\Go AS Go,
	\core\Database AS Database,
	\core\CException AS CException,
	\core\MsgException AS MsgException;
trait Login
{
	private $def_session_expired	= 1200,
			$cookie_use_domain		= NULL;

	public function adminCheckLogin()
	{
		if( $this->adminIsLogin() == FALSE )
		{
			if(isAjax() === TRUE) throw new MsgException('请先登录.');
			else exit("<script>parent.location.href='/login';</script>header('Location:/login');");
		}
	}
	/**
	 * admin login check
	 */
	public function adminIsLogin()
	{
		/*
		 * VAR DATA
		 */
		$is_login		= FALSE;
		$cookie_name	= $this->_adminLoginSessionName();

		/*
		 * check
		 */
		$is_login		= $this->_checkCookie( $cookie_name );

		/*
		 * return
		 */
		return $is_login;
	}

	/**
	 * admin login
	 * @param (string) $user_id
	 * @param (string) $user_password
	 * @return boolean
	 */
	public function adminLogin( $user_id, $user_password )
	{
		/*
		 * VAR DATA
		 */
		$is_ok					= FALSE;

		/*
		 * load class
		 */
		$user_manager		= Go::manager('User');
		$session_manager	= Go::manager('Session');

		/*
		 * user info
		 */
		$user				= $this->_getUserInfo( $user_id, $user_password );

		/*
		 * check permission
		 */
		$this->checkPermission( $user );

		/*
		 * set session data
		 */
		$session			= array();

		//set cookie name, set session data
		$cookie_name						= $this->_adminLoginSessionName();
		$session['session_data']		= json_encode(array( 'user_id' => $user_id ));
		$session['session_key']			= sha1( $cookie_name. $session_manager->session_data );
		$session['session_expired']		= REQUEST_TIME + $this->def_session_expired;
		$cookie_value					= md5( $cookie_name ) . $session['session_key'] . md5( $session['session_data'] );

		// set cookie
		$is_ok					= setcookie(
				$cookie_name,
				$cookie_value,
				$session['session_expired'],
				'/',
				$this->cookie_use_domain,
				FALSE,
				TRUE
		);

		// set session data
		if( $is_ok === TRUE )
		{
			return $this->_setLoginData( $user, $session );
		}

		return FALSE;
	}

	/**
	 * admin logout
	 */
	public function adminLogOut()
	{
		/*
		 * VAR DATA
		 */
		$cookie_name	= $this->_adminLoginSessionName();

		/*
		 * OUT
		*/
		$this->_cleanCookie( $cookie_name );
	}

	/**
	 * front check login
	 */
	public function frontCheckLogin()
	{
		if( $this->frontIsLogin() == FALSE )
		{
			if(isAjax() === TRUE) throw new MsgException('请先登录.');
			else header('Location:' . getBaseConfig('USER_BASE_URI') . '/login');
		}
	}

	/**
	 * Front login check
	 */
	public function frontIsLogin()
	{
		/*
		 * VAR DATA
		*/
		$is_login		= FALSE;
		$cookie_name	= $this->_frontLoginSessionName();

		/*
		 * check
		 */
		$is_login		= $this->_checkCookie( $cookie_name );

		/*
		 * return
		 */
		return $is_login;
	}

	/**
	 * Front Login
	 */
	public function frontLogin( $user_id, $user_password )
	{
		/*
		 * VAR DATA
		 */
		$is_ok					= FALSE;

		/*
		 * user info
		 */
		$user				= $this->_getUserInfo( $user_id, $user_password );

		/*
		 * set session data
		 */
		$session			= array();

		//set cookie name, set session data
		$cookie_name						= $this->_frontLoginSessionName();
		$session['session_data']			= json_encode(array( 'user_id' => $user_id ));
		$session['session_key']				= sha1( $cookie_name. $session['session_data'] );
		$session['session_expired']			= REQUEST_TIME + $this->def_session_expired;
		$cookie_value						= md5( $cookie_name ) . $session['session_key'] . md5( $session['session_data'] );

		// set cookie
		$is_ok					= setcookie(
				$cookie_name,
				$cookie_value,
				$session['session_expired'],
				'/',
				$this->cookie_use_domain,
				FALSE,
				TRUE
		);

		// set session data
		if( $is_ok === TRUE )
		{
			return $this->_setLoginData( $user, $session );
		}

		return FALSE;
	}

	/**
	 * Front logout
	 */
	public function frontLogOut()
	{
		/*
		 * VAR DATA
		 */
		$cookie_name	= $this->_frontLoginSessionName();

		/*
		 * OUT
		 */
		$this->_cleanCookie( $cookie_name );
	}

	private function _getUserInfo( $user_id, $user_password )
	{
		/*
		 * load class
		 */
		$user_manager		= Go::manager('User');

		/*
		 * load user
		 */
		$user	= $user_manager->loadByUsrId( $user_id );

		/*
		 * check input user info is right;
		 */
		if(empty( $user )) throw new MsgException('用户不存在.');
		if( $user['user_password'] !== $user_manager->encryptsPassword( $user_password ))  throw new MsgException('密码错误.');
		if( $user['user_active_flag'] != 'T' )  throw new MsgException('帐号未激活.');
		if( $user['user_delete_flag'] != 'F' )  throw new MsgException('用户不存在(被删除).');

		//check permission
		$this->checkPermission( $user );

		/*
		 * return
		 */
		return $user;
	}

	private function _setLoginData( $user, $session )
	{
		/*
		 * class
		 */
		$user_manager		= Go::manager('User');
		$session_manager	= Go::manager('Session');

		/*
		 * set data
		 */
		$user_manager->user_seq				= $user['user_seq'];
		$user_manager->user_edit_id			= $user['user_id'];

		$session_manager->session_data		= $session['session_data'];
		$session_manager->session_key		= $session['session_key'];
		$session_manager->session_expired	= $session['session_expired'];

		/*
		 * database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			if($user_manager->updateLoginTime() && $session_manager->insert())
			{
				/*****************/
				Database::commit();
				/*****************/
				return TRUE;
			}
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}
		return FALSE;
	}

	private function _checkCookie( $cookie_name )
	{
		/*
		 * load class
		 */
		$session_manager	= Go::manager('Session');

		/*
		 * check
		 */
		if(isset( $_COOKIE[$cookie_name] ) == TRUE )
		{
			if(md5( $cookie_name ) === substr( $_COOKIE[$cookie_name], 0, 32 ))
			{
				$session_key	= substr( $_COOKIE[$cookie_name], 32, 40);
				$session		= $session_manager->loadByKey( $session_key );
				$session_manager->format( $session );
				if(isset( $session['session_data'] ) && md5( $session['session_data'] ) == substr( $_COOKIE[$cookie_name], 72 ))
				{
					if( $session['session_expired'] >= REQUEST_TIME )
					{
						$session_manager->session_expired	= REQUEST_TIME + $this->def_session_expired;
						$session_manager->session_seq		= $session['session_seq'];
						$session_manager->session_key		= $session_key;
						$session_manager->updateExpired();

						setcookie( $cookie_name, $_COOKIE[$cookie_name], $session_manager->session_expired, '/', $this->cookie_use_domain, FALSE, TRUE );

						foreach($session['session_data_vm'] AS $key => $session_data )
						{
							$GLOBALS['_SESSION'][$key]	= $session_data;
						}

						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	private function _cleanCookie( $cookie_name )
	{

		/*
		 * load class
		 */
		$session_manager	= Go::manager('Session');

		/*
		 * action
		 */
		if(isset( $_COOKIE[$cookie_name] ))
		{
			$session_manager->session_key	= substr( $_COOKIE[$cookie_name], 32, 40 );
			$session_manager->deleteByKey();
			setcookie( $cookie_name, NULL, 1 );
		}
	}

	/**
	 * get session name for admin login
	 */
	private function _adminLoginSessionName()
	{
		$this->cookie_use_domain	= $_SERVER['SERVER_NAME'];
		return md5("admin_login{$this->cookie_use_domain}{$_SERVER['REMOTE_ADDR']}");
	}

	/**
	 * get session name for front login
	 */
	private function _frontLoginSessionName()
	{
		$this->cookie_use_domain	= trim(strstr('.', $_SERVER['SERVER_NAME'] ),'.');
		return md5("front_login{$this->cookie_use_domain}{$_SERVER['REMOTE_ADDR']}");
	}

}