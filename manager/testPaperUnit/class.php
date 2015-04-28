<?php
/**
 * Test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation;

use	\core\Config AS Config;
class TestPaperUnit extends Manager
{
	public
	/**
	 * fields VALUE
	 */
	$fields								= array(
		'test_paper_unit_seq'			=> 0,	// int(10) UN PK AI
		'tarent_test_paper_unit_seq'	=> 0,	// int(10) UN
		'test_paper_seq'				=> 0,	// int(10) UN
		'test_paper_unit_title'			=> '',	// varchar(200)
		'test_paper_unit_point'			=> '',	// smallint(5) UN
		'test_paper_unit_sort'			=> '',	// tinyint(3) UN
		'test_paper_unit_description'	=> '',	// varchar(2000)
		'test_paper_unit_edit_time'		=> 0,	// int(10) UN
		'test_paper_unit_edit_id'		=> '',	// varchar(45)
		'test_paper_unit_delete_flag'	=> 'F',	// enum('T','F')
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'test_paper_unit',
				'primary_key'	=> 'test_paper_unit_seq',
		));
	}

	public function validationParentTestPaperUnitSeq( $tarent_test_paper_unit_seq )
	{
		if(!Validation::vdtInt( $tarent_test_paper_unit_seq, 0, 4294967295 )) throw new MsgException('父级小标题不存在.');
	}

	public function validationTestPaperUnitTitle( $test_paper_unit_title )
	{
		if(!Validation::vdtStr( $test_paper_unit_title, 1, 200 )) throw new MsgException('小标题不能为空,且不能超过200个字符.');
	}

	public function validationTestPaperUnitDescription( $test_paper_unit_description )
	{
		if(!Validation::vdtStr( $test_paper_unit_description, 0, 2000 )) throw new MsgException('小标题描述不能超过2000个字符.');
	}
}
