<?php
/**
 * Test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation;

use	\core\Config AS Config;
class TestPaperFile extends Manager
{
	public
	/**
	 * fields VALUE
	 */
	$fields							= array(
		'test_paper_file_seq'		=> 0,	// int(10) UN PK AI
		'test_paper_seq'			=> 0,	// int(10) UN
		'file_seq'					=> 0,	// int(10) UN
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'test_paper_file',
				'primary_key'	=> 'test_paper_file_seq',
		));
	}
}
