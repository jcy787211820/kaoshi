<?php
/**
 * category mapping stady data manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;

class CategoryMapStudyData extends Manager
{
	public	$category_map_study_data_seq	= 0,	// int(10) UN PK AI
			$category_code					= '',	// varchar(30)
			$study_data_seq					= 0;	// int(10) UN


	public function __construct()
	{
		parent::__construct(array(
			'database'		=> Config::database('default')['DATABASE'],
			'table'			=> Config::database('default')['TABLE_PERFIX'] . 'category_map_study_data',
			'primary_key'	=> 'category_map_study_data_seq',
			'create_log'	=> FALSE,
		));
	}

	/**
	 * load row data by primary key
	 * @param (int) $category_map_study_data_seq
	 * @return single array
	 */
	public function loadBySeq( $category_map_study_data_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` = " . Database::escapeString( $category_map_study_data_seq ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load multi rows data by study data seq
	 * @param (int) $study_data_seq
	 * @return single array
	 */
	public function loadByStudyDataSeq( $study_data_seq )
	{
		$this->setWhere(array(
				"`study_data_seq`=" . Database::escapeString( $study_data_seq ),
		));
		return $this->select( MYSQL_ASSOC );
	}

	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert($option=array())
	{
		$this->setFields(array(
				'category_code'			=> $this->category_code,
				'study_data_seq'		=> $this->study_data_seq,
		));
		return parent::insert();
	}

	/**
	 * insert multi rows data
	 * @param (array) $data
	 * @return boolean
	 */
	public function inserts( $data )
	{
		$this->setFields( $data );
		return parent::insert();
	}

	/**
	 * delete row
	 * @param (array) $category_seqs
	 * @return boolean
	 */
	public function delete(array $category_map_study_data_seqs = NULL )
	{
		$this->setWhere(array(
				"`category_map_study_data_seq` in(" . implode( ",", array_map(array('\core\Database','escapeString'), $category_map_study_data_seqs )) . ")"
		));
		return parent::delete();
	}

	/**
	 * delete multi row
	 * @param (array) $study_data_seqs
	 * @return boolean
	 */
	public function deleteByStudyDataSeqs(array $study_data_seqs = NULL )
	{
		$this->setWhere(array(
				"`study_data_seq` in(" . implode( ",", array_map(array('\core\Database','escapeString'), $study_data_seqs )) . ")"
		));
		return parent::delete();
	}

	/**
	 * fileds validation
	 */
	public function validation( $key, $value, $is_multi = FALSE )
	{
		if( $is_multi == TRUE )
		{
			foreach( $value AS $tmp )
			{
				$this->validation( $key, $tmp );
			}
		}
		else
		{
			switch( $key )
			{
				case 'category_map_study_data_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Category map study data_seq error.');
					break;
				case 'category_code':
					if( self::vdtStr( $value, 1, 30 ) == FALSE || self::vdtReg( $value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('Category Code error.');
					break;
				case 'study_data_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Study data seq error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) self::$method( $value );
					else throw new CException('Param error.');
			}
		}
		return $value;
	}

	/**
	 * data format
	 * @param (array) $data
	 */
	public function format( &$data )
	{
		return $data;
	}
}