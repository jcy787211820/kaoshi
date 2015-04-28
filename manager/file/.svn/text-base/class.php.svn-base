<?php
/**
 * Test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation,
	\core\Config AS Config;
class File extends Manager
{
	public
	/**
	 * fields VALUE
	 */
	$fields							= array(
		'file_seq'					=> 0,	// int(10) UN PK AI
		'file_title'				=> '',	// varchar(100)
		'file_description'			=> '',	// varchar(200)
		'file_uri'					=> '',	// varchar(100)
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'file',
				'primary_key'	=> 'file_seq',
		));
	}

	/**
	 * Validation
	 * @param (string) $file_title
	 * @throws MsgException
	 */
	public function validationFileTitle( $file_title )
	{
		if(!Validation::vdtStr( $file_title, 1, 100 )) throw new MsgException('必须填写文件标题,并且在100个字符以内.');
	}

	public function validationFileDescription( $file_description )
	{
		if(!Validation::vdtStr( $file_description, 0, 200 )) throw new MsgException('文件描述不能超过200个字符.');
	}
}
