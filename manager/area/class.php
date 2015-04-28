<?php
/**
 * area manager
 */
namespace manager;
use \core\CException AS CException,
	\core\Database AS Database,
	\core\Config AS Config;
class Area extends Manager
{
	public	$area_seq			= 0,	// smallint(5) UN PK AI
			$area_code			= '',	// varchar(30) UN
			$area_name			= '',	// varchar(45)
			$area_gov_code		= '',	// varchar(30)
			$area_edit_time		= '',	// int(10) UN
			$area_edit_id		= '',	// varchar(45)
			$area_delete_flag	= 'F';	//enum('T','F')

	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'area',
				'primary_key'	=> 'area_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $area_seq
	 * @return single array
	 */
	public function loadBySeq( $area_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}`=" . Database::escapeString( $area_seq ),
				'`area_delete_flag` = "F"',
				));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load row data by Area Code
	 * @param (int) $area_code
	 * @return single array
	 */
	public function loadByCode( $area_code )
	{
		$this->setWhere(array(
				'`area_code` = ' . Database::escapeString( $area_code ),
				'`area_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load row data by Area Codes
	 * @param (int) $area_codes
	 * @return single array
	 */
	public function loadByCodes( $area_codes )
	{
		$this->setWhere(array(
				'`area_code` IN(' . implode(',', array_map(array('\core\Database','escapeString'), $area_codes )) . ')',
				'`area_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ASSOC );
	}

	/**
	 * load children data
	 * @param (string) $area_parent_code
	 * @return double array
	 */
	public function loadByParentCode( $area_parent_code )
	{
		$lists	= array();
		$this->setWhere(array(
				'`area_code` LIKE ' . Database::escapeString( $area_parent_code . '__' ),
				'`area_delete_flag` = "F"',
		));

		$this->setOrder(array('`area_seq` ASC'));
		return $this->select( MYSQL_ASSOC );
	}
	/**
	 * load not delete All
	 * @return array
	 */
	public function loadAll()
	{
		/*
		 * set where
		*/
		$this->setWhere(array(
				'`area_delete_flag` = "F"',
		));
		/*
		 * load
		*/
		return $this->select( MYSQL_ASSOC );
	}
	/**
	 * mapping name
	 */
	public function mappingName( $data )
	{
		/*
		 * load area data
		 */
		$area_codes	= array();
		$areas		= array();
		foreach( $data AS $key => $value )
		{
			if(isset( $value['area_code'] ))
			{
				$area_code_depth	= strlen( $value['area_code'] ) / 2;
				for($i = 1; $i <= $area_code_depth; $i++ )
				{
					$area_code				= substr( $value['area_code'], 0, $i * 2 );
					$area_codes[$area_code]	= $area_code;
				}
			}
		}

		if(!empty( $area_codes ))
		{
			$this->setFields(array(
					'`area_code`',
					'`area_name`',
			));
			$areas	= $this->resetListsKey((array) $this->loadByCodes( $area_codes ), 'area_code' );
		}

		/*
		 * set area_name
		 */
		foreach( $data AS $key => $value )
		{
			$data[$key]['area_name']	= '';
			if(isset( $value['area_code'] ))
			{
				$area_code_depth	= strlen( $value['area_code'] ) / 2;
				for($i = 1; $i <= $area_code_depth; $i++ )
				{
					$area_code					= substr( $value['area_code'], 0, $i * 2 );
					if(isset( $areas[$area_code] ))
					{
						$data[$key]['area_name']	.= $i == 1 ? $areas[$area_code]['area_name'] : "->{$areas[$area_code]['area_name']}";
					}
				}
			}
		}

		/*
		 * return data
		 */
		return $data;
	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert( $area_parent_code = NULL )
	{
		$this->area_code	= "(
			SELECT IFNULL(LPAD(CONV(CONV(MAX(`area_code`),36,10)+1,10,36)," . (strlen((string) $area_parent_code ) + 2) . ",'0'),'{$area_parent_code}00')
			FROM `{$this->database}`.`{$this->table}` AS `tmp`
			WHERE `area_code` LIKE " . Database::escapeString( $area_parent_code . '__' ) . "
		)";
		$this->setUnescapes(array('area_code'));

		$this->setFields(array(
				'area_code'			=> $this->area_code,
				'area_name'			=> $this->area_name,
				'area_gov_code'		=> $this->area_gov_code,
				'area_edit_time'	=> $this->editTime('area_edit_time'),
				'area_edit_id'		=> $this->editId('area_edit_id'),
		));
		return parent::insert();
	}
	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function update()
	{
		//set log keys
		$this->log_keys	= array( $this->area_seq );

		$this->setFields(array(
				'area_name'			=> $this->area_name,
				'area_gov_code'		=> $this->area_gov_code,
				'area_edit_time'	=> $this->editTime('area_edit_time'),
				'area_edit_id'		=> $this->editId('area_edit_id'),
		));
		$this->setWhere(array(
				'`area_seq`=' . Database::escapeString( $this->area_seq ),
		));
		return parent::update();
	}
	/**
	 * delete row
	 * @return boolean
	 */
	public function delete()
	{
		//set log keys
		$this->log_keys	= array( $this->area_seq );

		$this->setFields(array(
				'area_delete_flag'	=> 'T',
				'area_edit_time'	=> $this->editTime('area_edit_time'),
				'area_edit_id'		=> $this->editId('area_edit_id'),
		));
		$this->setWhere(array(
				'`area_seq` = '	. Database::escapeString( $this->area_seq ),
		));
		return parent::update();
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
				case 'area_seq':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Area seq error.');
					break;
				case 'area_parent_code':
					if(!empty( $value )) self::validation('area_code', $value );
					break;
				case 'area_code':
					if( self::vdtStr( $value, 1, 30 ) == FALSE || self::vdtReg( $value, '@([A-Z0-9]{2})+@') == FALSE ) throw new CException('Area Code error.');
					break;
				case 'area_name':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Area name error.');
					break;
				case 'area_gov_code':
					if( self::vdtStr( $value, 1, 30 ) == FALSE ) throw new CException('Area goverment Code error.');
					break;
				case 'area_edit_time':
					if( self::vdtInt( $value, 1, 4294967295 ) == FALSE ) throw new CException('Area edit time error.');
					break;
				case 'area_edit_id':
					if( self::vdtStr( $value, 1, 45 ) == FALSE ) throw new CException('Area edit id error.');
					break;
				case 'area_delete_flag':
					if( self::vdtFlg( $value ) == FALSE ) throw new CException('Area delete flag error.');
					break;
				default:
					$method	= strtr( $key, array( '_' => '' ));
					if(method_exists( $this, $method )) $this->$method( $value );
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
		if(isset( $data['area_edit_time'] ) == TRUE )
		{
			$data['area_edit_time_vm']	= $data['area_edit_time'] > 0 ? date('Y-m-d H:i:s', $data['area_edit_time'] ) : '';
		}

		return $data;
	}
}