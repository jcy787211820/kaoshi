<?php
/**
 * user write exam permission apply manager
 */
namespace manager;
use	\core\MsgException AS MsgException,
	\core\Validation AS Validation,
	\core\Database AS Database,
	\core\Config AS Config;
class WriteExamApply extends Manager
{
	const
	/**
	 * CONST VALUE
	 */
	WRITE_EXAM_APPLY_STATUS_REQUEST	= 1,
	WRITE_EXAM_APPLY_STATUS_UPDATE	= 2,
	WRITE_EXAM_APPLY_STATUS_REFUSE	= 3,
	WRITE_EXAM_APPLY_STATUS_AGREE	= 4,
	WRITE_EXAM_APPLY_STATUS_CLOSE	= 9;

	public
	/**
	 * PROPERTY VALUE
	 */
	$write_exam_apply_seq				= 0,					// int(10) UN PK AI
	$user_id							= '',					// varchar(45)
	$write_exam_apply_cert_type			= '身份证',				// varchar(45)
	$write_exam_apply_cert_id			= '',					// varchar(100)
	$write_exam_apply_file_uri			= '',					// varchar(200)
	$write_exam_apply_memo				= '',					// varchar(200)
	$write_exam_apply_admin_memo		= '',					// varchar(200)
	$write_exam_apply_status			= 0,					// tinyint(1) UN
	$write_exam_apply_insert_time		= 0,					// int(10) UN
	$write_exam_apply_insert_id			= '',					// varchar(45)
	$write_exam_apply_insert_ip			= '000.000.000.000',	// varchar(15)
	$write_exam_apply_edit_time			= 0,					// int(10) UN
	$write_exam_apply_edit_id			= '',					// varchar(45)
	$write_exam_apply_edit_ip			= '000.000.000.000';	// varchar(15)

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct(array(
				'database'		=> Config::database('default')['DATABASE'],
				'table'			=> Config::database('default')['TABLE_PERFIX'] . 'write_exam_apply',
				'primary_key'	=> 'write_exam_apply_seq',
				'create_log'	=> TRUE,
		));
	}
	/**
	 * load row data by primary key
	 * @param (int) $user_level_seq
	 * @return single array
	 */
	public function loadBySeq( $write_exam_apply_seq )
	{
		$this->setWhere(array(
				"`{$this->primary_key}` =" . Database::escapeString( $write_exam_apply_seq ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load row data by user id
	 * @param (int) $user_id
	 * @return single array
	 */
	public function loadByUserId( $user_id )
	{
		$this->setWhere(array(
				"`user_id` =" . Database::escapeString( $user_id ),
		));
		return $this->select( MYSQL_ROW_ASSOC );
	}

	/**
	 * load admin lists data
	 * @return double array
	 */
	public function loadPage( $page = 1, $rows = 20, $where = array())
	{
		$this->setFields(array(
				'`write_exam_apply_seq`',
				'`user_id`',
				'`write_exam_apply_cert_type`',
				'`write_exam_apply_cert_id`',
				'`write_exam_apply_status`',
				'`write_exam_apply_insert_time`',
				'`write_exam_apply_insert_id`',
				'`write_exam_apply_insert_ip`',
				'`write_exam_apply_edit_time`',
				'`write_exam_apply_edit_id`',
				'`write_exam_apply_edit_ip`',
		));
		$this->setPage( $page, $rows );
		$this->setWhere( $where );
		$this->setOrder(array('`write_exam_apply_seq` DESC'));
		$lists	= $this->select();
		$lists	= array_map(array($this,'format'), $lists);
		return $lists;
	}
	/**
	 * insert data
	 * @param (array) $data
	 * @return boolean
	 */
	public function insert($option=array())
	{
		$this->setFields(array(
				'user_id'						=> $this->user_id,
				'write_exam_apply_cert_type'	=> $this->write_exam_apply_cert_type,
				'write_exam_apply_cert_id'		=> $this->write_exam_apply_cert_id,
				'write_exam_apply_file_uri'		=> $this->write_exam_apply_file_uri,
				'write_exam_apply_memo'			=> $this->write_exam_apply_memo,
				'write_exam_apply_admin_memo'	=> $this->write_exam_apply_admin_memo,
				'write_exam_apply_status'		=> $this->write_exam_apply_status,
				'write_exam_apply_insert_time'	=> $this->editTime('write_exam_apply_insert_time'),
				'write_exam_apply_insert_id'	=> $this->editId('write_exam_apply_insert_id'),
				'write_exam_apply_insert_ip'	=> $_SERVER['REMOTE_ADDR'],
				'write_exam_apply_edit_time'	=> $this->editTime('write_exam_apply_edit_time'),
				'write_exam_apply_edit_id'		=> $this->editId('write_exam_apply_edit_id'),
				'write_exam_apply_edit_ip'		=> $_SERVER['REMOTE_ADDR'],
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
		$this->setFields(array(
				'write_exam_apply_cert_type'	=> $this->write_exam_apply_cert_type,
				'write_exam_apply_cert_id'		=> $this->write_exam_apply_cert_id,
				'write_exam_apply_file_uri'		=> $this->write_exam_apply_file_uri,
				'write_exam_apply_memo'			=> $this->write_exam_apply_memo,
				'write_exam_apply_status'		=> $this->write_exam_apply_status,
				'write_exam_apply_edit_time'	=> $this->editTime('write_exam_apply_edit_time'),
				'write_exam_apply_edit_id'		=> $this->editId('write_exam_apply_edit_id'),
				'write_exam_apply_edit_ip'		=> $_SERVER['REMOTE_ADDR'],
		));
		$this->setWhere(array(
				'`write_exam_apply_seq`=' . Database::escapeString( $this->write_exam_apply_seq ),
				'`user_id`=' . Database::escapeString( $this->user_id ),
				'`write_exam_apply_status` IN (
					"' . self::WRITE_EXAM_APPLY_STATUS_REQUEST . '",
					"' . self::WRITE_EXAM_APPLY_STATUS_UPDATE . '",
					"' . self::WRITE_EXAM_APPLY_STATUS_REFUSE . '"
				)',
		));

		//set log keys
		$this->log_keys	= array( $this->write_exam_apply_seq );
		return parent::update();
	}

	/**
	 * Admin Agree
	 * @return boolean
	 */
	public function updateAdminAgree()
	{
		$this->write_exam_apply_status			= self::WRITE_EXAM_APPLY_STATUS_AGREE;
		$this->setFields(array(
				'write_exam_apply_admin_memo'	=> $this->write_exam_apply_admin_memo,
				'write_exam_apply_status'		=> $this->write_exam_apply_status,
				'write_exam_apply_edit_time'	=> $this->editTime('write_exam_apply_edit_time'),
				'write_exam_apply_edit_id'		=> $this->editId('write_exam_apply_edit_id'),
				'write_exam_apply_edit_ip'		=> $_SERVER['REMOTE_ADDR'],
		));
		$this->setWhere(array(
				'`write_exam_apply_seq`=' . Database::escapeString( $this->write_exam_apply_seq ),
				'`write_exam_apply_status` IN ("' . self::WRITE_EXAM_APPLY_STATUS_REQUEST . '","' . self::WRITE_EXAM_APPLY_STATUS_UPDATE . '")',
		));

		//set log keys
		$this->log_keys	= array( $this->write_exam_apply_seq );
		return parent::update();
	}

	/**
	 * Admin Refuse
	 * @return boolean
	 */
	public function updateAdminRefuse()
	{
		$this->write_exam_apply_status			= self::WRITE_EXAM_APPLY_STATUS_REFUSE;
		$this->setFields(array(
				'write_exam_apply_admin_memo'	=> $this->write_exam_apply_admin_memo,
				'write_exam_apply_status'		=> $this->write_exam_apply_status,
				'write_exam_apply_edit_time'	=> $this->editTime('write_exam_apply_edit_time'),
				'write_exam_apply_edit_id'		=> $this->editId('write_exam_apply_edit_id'),
				'write_exam_apply_edit_ip'		=> $_SERVER['REMOTE_ADDR'],
		));
		$this->setWhere(array(
				'`write_exam_apply_seq`=' . Database::escapeString( $this->write_exam_apply_seq ),
				'`write_exam_apply_status` IN ("' . self::WRITE_EXAM_APPLY_STATUS_REQUEST . '","' . self::WRITE_EXAM_APPLY_STATUS_UPDATE . '")',
		));

		//set log keys
		$this->log_keys	= array( $this->write_exam_apply_seq );
		return parent::update();
	}

	/**
	 * Admin Close
	 * @return boolean
	 */
	public function updateAdminClose()
	{
		$this->write_exam_apply_status			= self::WRITE_EXAM_APPLY_STATUS_CLOSE;
		$this->setFields(array(
				'write_exam_apply_admin_memo'	=> $this->write_exam_apply_admin_memo,
				'write_exam_apply_status'		=> $this->write_exam_apply_status,
				'write_exam_apply_edit_time'	=> $this->editTime('write_exam_apply_edit_time'),
				'write_exam_apply_edit_id'		=> $this->editId('write_exam_apply_edit_id'),
				'write_exam_apply_edit_ip'		=> $_SERVER['REMOTE_ADDR'],
		));
		$this->setWhere(array(
				'`write_exam_apply_seq`=' . Database::escapeString( $this->write_exam_apply_seq ),
				'`write_exam_apply_status` IN (
					"' . self::WRITE_EXAM_APPLY_STATUS_REQUEST . '",
					"' . self::WRITE_EXAM_APPLY_STATUS_UPDATE . '",
					"' . self::WRITE_EXAM_APPLY_STATUS_REFUSE . '"
				)',
		));

		//set log keys
		$this->log_keys	= array( $this->write_exam_apply_seq );
		return parent::update();
	}

	/**
	 * data format
	 * @param (array) $data
	 */
	public function format( &$data )
	{
		if(isset( $data['write_exam_apply_status'] ))
		{
			$write_exam_apply_status			= $data['write_exam_apply_status'];
			$data['write_exam_apply_status_vw']	= empty( $this->getWriteExamApplyStatuss()[$write_exam_apply_status] )
												? ''
												: $this->getWriteExamApplyStatuss()[$write_exam_apply_status];
		}
		if(isset( $data['write_exam_apply_insert_time'] ))
		{
			$data['write_exam_apply_insert_time_vw']	= empty( $data['write_exam_apply_insert_time'] )
			? '-'
			: date( 'Y-m-d H:i:s', $data['write_exam_apply_edit_time'] );
		}
		if(isset( $data['write_exam_apply_edit_time'] ))
		{
			$data['write_exam_apply_edit_time_vw']	= empty( $data['write_exam_apply_edit_time'] )
													? '-'
													: date( 'Y-m-d H:i:s', $data['write_exam_apply_edit_time'] );
		}
		return $data;
	}
	/**
	 * get user level types
	 * @return array
	 */
	public function getWriteExamApplyStatuss()
	{
		return array(
				self::WRITE_EXAM_APPLY_STATUS_REQUEST	=> '新申请【等待审核】',
				self::WRITE_EXAM_APPLY_STATUS_UPDATE	=> '用户修改【等待审核】',
				self::WRITE_EXAM_APPLY_STATUS_REFUSE	=> '拒绝申请【等待用户修改】',
				self::WRITE_EXAM_APPLY_STATUS_AGREE		=> '同意申请【审核已通过】',
				self::WRITE_EXAM_APPLY_STATUS_CLOSE		=> '申请被取消【需要与管理员联系才能再次申请】',
		);
	}

	/**
	 * validation
	 */
	public function validationWriteExamApplySeq( $write_exam_apply_seq, $message = NULL )
	{
		$message	= empty( $message ) ? 'Write exam apply seq error.' : $message;
		if(Validation::vdtInt( $write_exam_apply_seq, 1, 4294967295 ) == FALSE) throw new MsgException( $message );
		return $write_exam_apply_seq;
	}
	public function validationWriteExamApplyCertType( $write_exam_apply_cert_type, $message = NULL )
	{
		$message	= empty( $message ) ? 'Write exam apply cert type error.' : $message;
		if(Validation::vdtStr( $write_exam_apply_cert_type, 1, 45 ) == FALSE) throw new MsgException( $message );
		return $write_exam_apply_cert_type;
	}
	public function validationWriteExamApplyCertId( $write_exam_apply_cert_id, $message = NULL )
	{
		$message	= empty( $message ) ? 'Write exam apply cert id error.' : $message;
		if(Validation::vdtStr( $write_exam_apply_cert_id, 1, 100 ) == FALSE ) throw new MsgException( $message );
		return $write_exam_apply_cert_id;
	}
	public function validationWriteExamApplyMemo( $write_exam_apply_memo, $message = NULL )
	{
		$message	= empty( $message ) ? 'Write exam apply memo error.' : $message;
		if(Validation::vdtStr( $write_exam_apply_memo, 0, 200 ) == FALSE ) throw new MsgException( $message );
		return $write_exam_apply_memo;
	}
	public function validationWriteExamApplyAdminMemo( $write_exam_apply_admin_memo, $message = NULL )
	{
		$message	= empty( $message ) ? 'Write exam apply admin memo error.' : $message;
		if(Validation::vdtStr( $write_exam_apply_admin_memo, 0, 200 ) == FALSE ) throw new MsgException( $message );
		return $write_exam_apply_admin_memo;
	}
}
