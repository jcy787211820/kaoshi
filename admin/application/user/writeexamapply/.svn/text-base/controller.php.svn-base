<?php
/**
 * permission manager
 */
includeTraits('Login');
includeTraits('Permission');

use \core\Go AS Go,
	\core\Controller AS Controller,
	\core\Database AS Database,
	\core\Validation AS Validation,
	\core\CException AS CException;
class WriteExamApply extends Controller
{
	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->adminCheckLogin();
		$this->checkPermission();
	}

	/**
	 * view page
	 * Enter description here ...
	 */
	public function index()
	{
		/*
		 * var data
		 */
		$assign_data		= array();

		/*
		 * view
		 */
		parent::view( $assign_data );
	}

	/**
	 * get list data
	 */
	public function listed()
	{
		/*
		 * var data
		 */
		$assign_data = array('error'=>TRUE);

		/*
		 * load class
		 */
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * post data
		 */
		$page	= Validation::vdtPage(initGet('page'));
		$total	= Validation::vdtTotal(initGet('total'));
		$rows	= Validation::vdtRows(initGet('rows'));
		if( $page * $rows > $total && $page > 1 ) throw new MsgException('Param Error.');

		/*
		 * get lists
		 */
		$lists			= $write_exam_apply_manager->loadPage( $page, $rows, $this->_mkWhere() );

		/*
		 * get total
		 */
		if(!empty( $lists ) && $total == 0 ) $total	= $write_exam_apply_manager->preTotal();

		/*
		 * assign data
		 */
		$assign_data['total']	= $total;
		$assign_data['lists']	= $lists;
		$assign_data['error']	= FALSE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}
	/**
	 * edit form
	 * Enter description here ...
	 * @param (int) $user_group_seq
	 */
	public function form()
	{
		/*
		 * var data
		 */
		$assign_data				= array('error' => TRUE);

		/*
		 * load class
		 */
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * post data
		 */
		switch(initPost('action'))
		{
			case 'edit':
				$write_exam_apply_seq		= $write_exam_apply_manager->validationWriteExamApplySeq(initPost('write_exam_apply_seq'));
				$write_exam_apply			= $write_exam_apply_manager->loadBySeq( $write_exam_apply_seq );
				break;
			default:
				throw new CException('系统异常：Acion error.');
		}

		/*
		 * set assign data
		 */
		$assign_data['write_exam_apply']			= $write_exam_apply_manager->format( $write_exam_apply );

		/*
		 * view
		 */
		parent::view( $assign_data );
	}
	/**
	 * Agree the apply
	 */
	public function agree()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		 */
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * post data
		 */
		$write_exam_apply_manager->write_exam_apply_seq			= $write_exam_apply_manager->validationWriteExamApplySeq(initPost(
				'write_exam_apply_seq'
		));
		$write_exam_apply_manager->write_exam_apply_admin_memo	= $write_exam_apply_manager->validationWriteExamApplyAdminMemo(initPost(
				'write_exam_apply_admin_memo'
		));

		/*
		 * process
		 */
		$assign_data['error']	= $write_exam_apply_manager->updateAdminAgree() !== TRUE;

		/*
		 * echo
		 */
		echo json_encode( $assign_data );
	}

	/**
	 * Refuse the apply
	 */
	public function refuse()
	{
		/*
		 * var data
		*/
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		*/
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * post data
		*/
		$write_exam_apply_manager->write_exam_apply_seq			= $write_exam_apply_manager->validationWriteExamApplySeq(initPost(
				'write_exam_apply_seq'
		));
		$write_exam_apply_manager->write_exam_apply_admin_memo	= $write_exam_apply_manager->validationWriteExamApplyAdminMemo(initPost(
				'write_exam_apply_admin_memo'
		));

		/*
		 * process
		*/
		$assign_data['error']	= $write_exam_apply_manager->updateAdminRefuse() !== TRUE;

		/*
		 * echo
		*/
		echo json_encode( $assign_data );
	}

	/**
	 * Close the apply
	 */
	public function close()
	{
		/*
		 * var data
		*/
		$assign_data	= array('error' => TRUE);

		/*
		 * load class
		*/
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * post data
		*/
		$write_exam_apply_manager->write_exam_apply_seq			= $write_exam_apply_manager->validationWriteExamApplySeq(initPost(
				'write_exam_apply_seq'
		));
		$write_exam_apply_manager->write_exam_apply_admin_memo	= $write_exam_apply_manager->validationWriteExamApplyAdminMemo(initPost(
				'write_exam_apply_admin_memo'
		));

		/*
		 * process
		*/
		$assign_data['error']	= $write_exam_apply_manager->updateAdminClose() !== TRUE;

		/*
		 * echo
		*/
		echo json_encode( $assign_data );
	}

	/**
	 * set where by this listed
	 */
	private function _mkWhere()
	{
		/*
		 * var data
		 */
		$where	= array();

		/*
		 * load class
		*/
		$permission_manager			= Go::manager('Permission');

		/*
		 * post data
		 */
		$user_id						= initGet('user_id');
		$write_exam_apply_cert_type		= initGet('write_exam_apply_cert_type');
		$write_exam_apply_cert_id		= initGet('write_exam_apply_cert_id');

		/*
		 * make where value
		 */
		if(strlen( $user_id ) > 0) $where[]	= '`user_id` = ' . Database::escapeString( $user_id );
		if( $write_exam_apply_cert_type == 'etc' ) $where[]	= "`write_exam_apply_cert_type` <> '身份证'";
		else if(strlen( $write_exam_apply_cert_type ) > 0) $where[]	= '`write_exam_apply_cert_type` = ' . Database::escapeString( $write_exam_apply_cert_type );
		if(strlen( $write_exam_apply_cert_id ) > 0 ) $where[]	= '`write_exam_apply_cert_id` = ' . Database::escapeString( $write_exam_apply_cert_id );

		/*
		 * return
		 */
		return $where;
	}
}
