<?php
/**
 * user group table database processing
 */
namespace manager\userGroup;
use \core\Database AS Database;
trait Base
{
	/**
	 * load row data by primary key
	 * @param (int) $user_group_seq
	 * @return single array
	 */
	public function loadBySeq( $user_group_seq )
	{
		$this->setWhere(array(
			"`{$this->primary_key}`=" . Database::escapeString( $user_group_seq ),
			'`user_group_delete_flag` = "F"',
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}
	/**
	 * load depth key data
	 * @return multi array
	 */
	public function loadUsedDepthArr()
	{
		$lists	= array();
		$this->setWhere(array(
			'`user_group_use_flag` = "T"',
			'`user_group_delete_flag` = "F"',
		));
		$tmps	= $this->select();
		foreach( $tmps AS $key => $tmp )
		{
			$depth	= strlen( $tmp['user_group_code'] ) / 2;
			if(isset( $lists[$depth] ) == FALSE) $lists[$depth] = array();
			$lists[$depth][]	= $tmp;
			unset( $tmps[$key] );
		}
		unset( $tmps );
		return $lists;
	}
	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$this->setFields(array(
			'`user_group_seq`',
			'`user_group_code`',
			'`user_group_name`',
			'`user_group_use_flag`',
			'`user_group_edit_time`',
			'`user_group_edit_id`',
			'FROM_UNIXTIME(`user_group_edit_time`,"%Y-%m-%d %H:%i:%s") AS `user_group_edit_date_vm`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`user_group_code` ASC', '`user_group_seq` DESC'));
		return $this->select();
	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function sInsert(array $data )
	{
		//user group code  is 00 ~ ZZZZZZZZZZ
		$this->setFields(array(
			'user_group_code'		=> "(
					SELECT LPAD(
							CONV(CONV(IFNULL(MAX(`user_group_code`),'{$data['user_group_parent_code']}00'),36,10)+1,10,36)
							,LENGTH(IFNULL(MAX(`user_group_code`),'{$data['user_group_parent_code']}00'))
							,0
						)
					FROM `{$this->model->getObjVar('database')}`.`{$this->model->getObjVar('table')}` AS `tmp`
					WHERE `user_group_code` LIKE " . Database::escapeString( $data['user_group_parent_code'] . '__' ) . "
				)",
			'user_group_name'		=> $data['user_group_name'],
			'user_group_use_flag'	=> $data['user_group_use_flag'],
			'user_group_edit_time'	=> REQUEST_TIME,
			'user_group_edit_id'	=> 'system',
		));
		$this->setUnescapes(array('user_group_code'));
		return parent::insert();
	}

	/**
	 * update data
	 * @param (array) $data
	 * @return boolean
	 */
	public function sUpdate(array $data )
	{
		$this->setFields(array(
			'user_group_name'		=> $data['user_group_name'],
			'user_group_use_flag'	=> $data['user_group_use_flag'],
			'user_group_edit_time'	=> REQUEST_TIME,
			'user_group_edit_id'	=> 'system',
		));
		$this->setWhere(array(
			'user_group_seq'		=> '`user_group_seq`=' . Database::escapeString( $data['user_group_seq'] ),
		));
		return parent::update();
	}
}