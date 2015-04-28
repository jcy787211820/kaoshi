<?php
/**
 * Test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation;

use	\core\Config AS Config;
class TestPaper extends Manager
{
	public
	/**
	 * fields VALUE
	 */
	$fields							= array(
		'test_paper_seq'			=> 0,			// int(10) UN PK
		'user_id'					=> '',			// varchar(45)
		'test_paper_name'			=> '',			// varchar(45)
		'test_paper_description'	=> '',			// varchar(200)
		'test_paper_point'			=> '0',			// smallint(5) UN
		'test_paper_price'			=> '0.00',		// decimal(14,2) UN
		'test_paper_answer_price'	=> '0.00',		// decimal(14,2) UN
		'test_paper_analysis_price'	=> '0.00',		// decimal(14,2) UN
		'test_paper_timeout'		=> '0',			// tinyint(3) UN
		'test_paper_check_flag'		=> 'F',			// enum('T','F')
		'test_paper_insert_time'	=> '0',			// int(10) UN
		'test_paper_insert_id'		=> '',			// varchar(45)
		'test_paper_edit_time'		=> '0',			// int(10) UN
		'test_paper_edit_id'		=> '',			// varchar(45)
		'test_paper_delete_flag'	=> 'F',			// enum('T','F')
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'test_paper',
				'primary_key'	=> 'test_paper_seq',
		));
	}

	public function validationTestPaperName( $test_paper_name )
	{
		if(!Validation::vdtStr( $test_paper_name, 1, 45 )) throw new MsgException('必须填写试卷名,并且在45个字符以内.');
	}

	public function validationTestPaperDescription( $test_paper_description )
	{
		if(!Validation::vdtStr( $test_paper_description, 0, 200 )) throw new MsgException('试卷描述只能200个字符以内.');
	}

	public function validationTestPaperTimeout( $test_paper_timeout )
	{
		if(!Validation::vdtInt( $test_paper_timeout, 0, 255 )) throw new MsgException('试卷限制时间不能超过255分钟.');
	}

	public function validationTestPaperPrice( $test_paper_price )
	{
		if(!Validation::vdtDec( $test_paper_price, 12, 2 )) throw new MsgException('试卷价格取值范围是0.00~999,999,999,999.99 .');
	}

	public function validationTestPaperAnswerPrice( $test_paper_answer_price )
	{
		if(!Validation::vdtDec( $test_paper_answer_price, 12, 2 )) throw new MsgException('查看答案价格取值范围是0.00~999,999,999,999.99 .');
	}

	public function validationTestPaperAnalysisPrice( $test_paper_analysis_price )
	{
		if(!Validation::vdtDec( $test_paper_analysis_price, 12, 2 )) throw new MsgException('查看试题解析价格取值范围是0.00~999,999,999,999.99 .');
	}
}
