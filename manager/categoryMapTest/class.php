<?php
/**
 * Category map test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation;

use	\core\Config AS Config;
class CategoryMapTest extends Manager
{
	public
	/**
	 * fields VALUE
	 */
	$fields						= array(
		'category_map_test_seq'	=> 0,	// int(10) UN PK AI
		'category_code'			=> '',	// varchar(30)
		'test_seq'				=> 0,	// int(10) UN
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'category_map_test',
				'primary_key'	=> 'category_map_test_seq',
				'create_log'	=> TRUE,
		));
	}
}
