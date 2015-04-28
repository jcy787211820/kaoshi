<?php
/**
 * Test manager
 */
namespace manager;
use \core\MsgException,
	\core\Validation AS Validation;

use	\core\Config AS Config;
class Test extends Manager
{
	const
	/**
	 * CONST VALUE
	 */
	TEST_TYPE_RADIO			= 1,
	TEST_TYPE_CHECKBOX		= 2,
	TEST_TYPE_T_F			= 3,
	TEST_TYPE_ETC			= 9;

	protected
	/**
	 * test types
	 */
	$test_types						= array(
		self::TEST_TYPE_RADIO		=> '单选题',
		self::TEST_TYPE_CHECKBOX	=> '多选题',
		self::TEST_TYPE_T_F			=> '是非题',
		self::TEST_TYPE_ETC			=> '客观题',
	);

	public
	/**
	 * fields VALUE
	 */
	$fields						= array(
		'test_seq'				=> 0,		// int(10) UN PK AI
		'user_id'				=> '',		// varchar(45)
		'test_type'				=> 0,		// tinyint(3) UN
		'test_question'			=> '',		// varchar(2000)
		'test_answer_json'		=> '[]',	// varchar(2000)
		'test_timeout'			=> 0,		// smallint(5) UN
		'test_analysis'			=> '',		// varchar(2000)test_analysis
		'test_only_paper'		=> 'F',		// enum('T','F')
		'test_check_flag'		=> 'F',		// enum('T','F')
		'test_price'			=> '0.00',	// decimal(14,2) UN
		'test_answer_price'		=> '0.00',	// decimal(14,2) UN
		'test_analysis_price'	=> '0.00',	// decimal(14,2) UN
		'test_insert_time'		=> 0,		// int(10) UN
		'test_insert_id'		=> '',		// varchar(45)
		'test_edit_time'		=> 0,		// int(10) UN
		'test_edit_id'			=> '',		// varchar(45)
		'test_delete_flag'		=> 'F',		// enum('T','F')
	);

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'test',
				'primary_key'	=> 'test_seq',
		));
	}

	/**
	 * get test types
	 * @param (string) $type	1、all, return all 2、impersonal(客观的；非个人的)
	 * @return multitype:string
	 */
	public function getTestTypes( $type = 'all' )
	{
		$return_types	= $this->test_types;
		if( $type == 'impersonal' )
		{
			$return_keys	= array(self::TEST_TYPE_RADIO,self::TEST_TYPE_CHECKBOX,self::TEST_TYPE_T_F);
			foreach( $return_types AS $key => $value ){if(!in_array( $key, $return_keys)) unset( $return_types[$key] );}
		}
		return $return_types;
	}

	public function getTestTypeValue( $test_type_key )
	{
		return $this->test_types[$test_type_key];
	}

	public function makeTestAnswerJson( $test_type, $test_possible_answers, $test_real_answers )
	{
		$return_answer_data	= array();
		switch( $test_type )
		{
			case self::TEST_TYPE_RADIO:
				$return_answer_data['answers']	= array();
				foreach( $test_possible_answers AS $key => $test_possible_answer_arr )
				{
					$current_answers				= array();
					$isset_real_answer				= FALSE;
					foreach( $test_possible_answer_arr AS $index => $test_possible_answer )
					{
						$is_real_answer	= 'F';
						if(isset( $test_real_answers[$key][$index] ) && $test_real_answers[$key][$index] == 'T' )
						{
							if( $isset_real_answer == TRUE ) throw new MsgException('系统错误:单选题正确答案只能有一个选项.');

							$is_real_answer			= 'T';
							$isset_real_answer		= TRUE;
						}
						if( $test_possible_answer === '' ) throw new MsgException('每个选项都不允许是空字符.');

						$current_answers[]			= array(
							'answer_content'		=> $test_possible_answer,
							'is_real_answer'		=> isset( $test_real_answers[$key][$index] ) ? 'T' : 'F',
						);
					}
					if( $isset_real_answer == FALSE ) throw new MsgException('单选题必须选择一个正确答案.');
					$return_answer_data['answers'][]	= $current_answers;
				}
				break;
			case self::TEST_TYPE_CHECKBOX:
				$return_answer_data['answers']	= array();
				foreach( $test_possible_answers AS $key => $test_possible_answer_arr )
				{
					$current_answers				= array();
					$isset_real_answer				= FALSE;
					foreach( $test_possible_answer_arr AS $index => $test_possible_answer )
					{
						$is_real_answer	= 'F';
						if(isset( $test_real_answers[$key][$index] ) && $test_real_answers[$key][$index] == 'T' )
						{
							$is_real_answer			= 'T';
							$isset_real_answer		= TRUE;
						}
						if( $test_possible_answer === '' ) throw new MsgException('每个选项都不允许是空字符.');
						$current_answers[]			= array(
							'answer_content'		=> $test_possible_answer,
							'is_real_answer'		=> isset( $test_real_answers[$key][$index] ) ? 'T' : 'F',
						);
					}
					if( $isset_real_answer == FALSE ) throw new MsgException('多选题必须选择正确答案.');
					$return_answer_data['answers'][]	= $current_answers;
				}
				break;
			case self::TEST_TYPE_T_F:
				if( $test_real_answers != 'T' && $test_real_answers != 'F' ) throw new MsgException('系统错误:是非题答案选择错误.');
				$return_answer_data['answers']		= $test_real_answers;
				break;
			default:
				throw new MsgException('系统错误:试题类型错误.');
		}

		$return_answer_data	= json_encode( $return_answer_data );
		return $return_answer_data;
	}

	public function validationTestType( $test_type, $check_type = 'all' )
	{
		if(!array_key_exists( $test_type, $this->getTestTypes( $check_type ) )) throw new MsgException('系统错误：不存在的试题类型.');
	}

	public function validationTestQuestion( $test_question )
	{
		if(!Validation::vdtStr( $test_question, 1, 2000 )) throw new MsgException('必须填写问题内容,并且在2000个字符以内.');
	}

	public function validationTestAnswerJson( $test_answer_json )
	{
		if(!Validation::vdtStr( $test_answer_json, 0, 2000 )) throw new MsgException('问题答案选项内容超长,无法保存题目.');
	}

	public function validationTestAnalysis( $test_analysis )
	{
		if(!Validation::vdtStr( $test_analysis, 0, 2000 )) throw new MsgException('试题解析不能超过2000字符.');
	}

	public function validationTestTimeout( $test_timeout )
	{
		if(!Validation::vdtInt( $test_timeout, 0, 65535 )) throw new MsgException('答题超时时间不能超过65535秒.');
	}

	public function validationTestPrice( $test_price )
	{
		if(!Validation::vdtDec( $test_price, 12, 2 )) throw new MsgException('试题价格取值范围是0.00~999,999,999,999.99 .');
	}

	public function validationTestAnswerPrice( $test_answer_price )
	{
		if(!Validation::vdtDec( $test_answer_price, 12, 2 )) throw new MsgException('查看试题答案价格取值范围是0.00~999,999,999,999.99 .');
	}

	public function validationTestAnalysisPrice( $test_analysis_price )
	{
		if(!Validation::vdtDec( $test_analysis_price, 12, 2 )) throw new MsgException('查看试题解析价格取值范围是0.00~999,999,999,999.99 .');
	}
}
