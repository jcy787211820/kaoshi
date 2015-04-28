<?php
use \core\Entrance AS Entrance;
use \core\MsgException AS MsgException;
// $sm = memory_get_usage();
try
{
	/**
	 * Base const
	 */
	define('REQUEST_TIME', $_SERVER['REQUEST_TIME'] );
	define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR  . '..' . DIRECTORY_SEPARATOR );
	if( $_SERVER['SERVER_NAME'] == 'admin.real.dev')
	{
		define('CONFIG_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'config');
	}
	else
	{
		define('CONFIG_PATH', $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'dev');
	}
	/**
	 * @todo Use core
	 */
	require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'entrance.php';

	Entrance::run();

}
catch(MsgException $e)
{
	if(isAjax() == TRUE ) echo json_encode(array('error' => TRUE, 'message' => $e->getMessage()));
	else echo $e->getMessage();
}
catch(Exception $e)
{
	/**
	 * @todo Development environment print exception
	 */
	if( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] )
	{
		if(isAjax() == TRUE ) echo json_encode(array('error' => TRUE, 'message' => $e->getMessage()));
		else throw $e;
		exit;
	}
	/**
	 * @todo Online environment print exception, 404 not found;
	 */

	header("Status: 404 Not Found");
	echo 'This page is not find.';
}
// $em = memory_get_peak_usage();
// echo 'Start use memory : ' , round( $sm  / (1024 *1024) , 4)  , '(Mb)';
// echo 'Start end memory : ' , round( $em  / (1024 *1024) , 4)  , '(Mb)';
// echo 'Use max memory :' , round(($em - $sm) / (1024 *1024),4)  , '(Mb)';

