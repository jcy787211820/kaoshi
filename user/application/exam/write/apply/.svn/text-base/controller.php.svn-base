<?php
includeTraits('Login');
includeTraits('Permission');
use \core\Controller AS Controller,
	\core\Config AS Config,
	\core\MsgException AS MsgException,
	\core\Go AS Go;
class Apply extends Controller
{
	use \traits\login, \traits\permission;

	public function __construct()
	{
		parent::__construct();
		$this->frontCheckLogin();
		$this->checkPermission();
	}
	/**
	 * request form
	 */
	public function index()
	{
		/*
		 * var data
		*/
		$assign_data	= array();

		/*
		 * include class
		 */
		$write_exam_apply_manager	= Go::manager('WriteExamApply');

		/*
		 * load data
		 */
		$write_exam_apply			= $write_exam_apply_manager->loadByUserId( $_SESSION['user_id'] );
		$write_exam_apply			= empty( $write_exam_apply ) ? get_object_vars( $write_exam_apply_manager ) : $write_exam_apply;
		$write_exam_apply			= $write_exam_apply_manager->format( $write_exam_apply );

		/*
		 * view
		 */
		$assign_data['write_exam_apply']	= $write_exam_apply;
		parent::layout('default');
		parent::view( $assign_data );
	}

	/**
	 * request
	 */
	public function request()
	{
		/*
		 * var data
		 */
		$assign_data	= array('error' => TRUE, 'message' => '');
		try
		{
			/*
			 * load class
			 */
			$write_exam_apply_manager	= Go::manager('WriteExamApply');

			/*
			 * setting data
			 */
			$write_exam_apply_manager->write_exam_apply_cert_type	= $write_exam_apply_manager->validationWriteExamApplyCertType(initPost('write_exam_apply_cert_type'),'必须填写证件类型.(45字以内)');
			$write_exam_apply_manager->write_exam_apply_cert_id		= $write_exam_apply_manager->validationWriteExamApplyCertId(initPost('write_exam_apply_cert_id'),'必须填写证件号码.(100字以内)');
			$write_exam_apply_manager->write_exam_apply_file_uri	= $this->_uploadWriteExamApplyFile();
			$write_exam_apply_manager->write_exam_apply_memo		= $write_exam_apply_manager->validationWriteExamApplyMemo(initPost('write_exam_apply_memo'),'说明请控制在200字以内.');
			$write_exam_apply_manager->user_id						= $_SESSION['user_id'];
			$write_exam_apply_manager->write_exam_apply_status		= $write_exam_apply_manager::WRITE_EXAM_APPLY_STATUS_REQUEST;

			/*
			 * insert
			 */
			$assign_data['error']	= $write_exam_apply_manager->insert() !== TRUE;
		}
		catch(MsgException $e)
		{
			$assign_data['error']	= TRUE;
			$assign_data['message']	= $e->getMessage();
		}
		catch(Exception $e)
		{
			$assign_data['error']	= TRUE;
			$assign_data['message']	= '系统发生错误.';
		}

		/*
		 * echo
		 */
		parent::layout('blank');
		parent::view( $assign_data );
	}

	/**
	 * request
	 */
	public function update()
	{
		/*
		 * var data
		*/
		$assign_data	= array('error' => TRUE, 'message' => '');
		try
		{
			/*
			 * load class
			 */
			$write_exam_apply_manager	= Go::manager('WriteExamApply');

			/*
			 * Org data
			 */
			$write_exam_apply			= $write_exam_apply_manager->loadByUserId( $_SESSION['user_id'] );
			if(		empty( $write_exam_apply )
				||	$write_exam_apply['write_exam_apply_status'] >= $write_exam_apply_manager::WRITE_EXAM_APPLY_STATUS_AGREE
			){
				$assign_data['error']	= TRUE;
				$assign_data['message']	= '系统错误.';
			}
			else
			{
				/*
				 * setting data
				 */
				$write_exam_apply_manager->write_exam_apply_cert_type	= $write_exam_apply_manager->validationWriteExamApplyCertType(
																				initPost('write_exam_apply_cert_type'),
																				'必须填写证件类型.(45字以内)'
																			);
				$write_exam_apply_manager->write_exam_apply_cert_id		= $write_exam_apply_manager->validationWriteExamApplyCertId(
																				initPost('write_exam_apply_cert_id'),
																				'必须填写证件号码.(100字以内)'
																			);
				$write_exam_apply_manager->write_exam_apply_memo		= $write_exam_apply_manager->validationWriteExamApplyMemo(
																				initPost('write_exam_apply_memo'),
																				'说明请控制在200字以内.'
																			);
				$write_exam_apply_manager->write_exam_apply_file_uri	= $this->_replaceWriteExamApplyFile( $write_exam_apply['write_exam_apply_file_uri'] );
				$write_exam_apply_manager->user_id						= $_SESSION['user_id'];
				$write_exam_apply_manager->write_exam_apply_seq			= $write_exam_apply['write_exam_apply_seq'];
				$write_exam_apply_manager->write_exam_apply_status		= $write_exam_apply['write_exam_apply_status'] == $write_exam_apply_manager::WRITE_EXAM_APPLY_STATUS_REFUSE
																		? $write_exam_apply_manager::WRITE_EXAM_APPLY_STATUS_UPDATE
																		: $write_exam_apply['write_exam_apply_status'];

				/*
				 * update
				 */
				$assign_data['error']	= $write_exam_apply_manager->update() !== TRUE;
			}
		}
		catch(MsgException $e)
		{
			$assign_data['error']	= TRUE;
			$assign_data['message']	= $e->getMessage();
		}
		catch(Exception $e)
		{
			$assign_data['error']	= TRUE;
			$assign_data['message']	= '系统发生错误.';
		}

		/*
		 * echo
		 */
		parent::layout('blank');
		parent::view( $assign_data );
	}

	/**
	 * reupload file
	 * @param (string) $org_write_exam_apply_file_uri
	 */
	private function _replaceWriteExamApplyFile( $org_write_exam_apply_file_uri )
	{
		if( $_FILES['write_exam_apply_file']['size'] == 0 ) return $org_write_exam_apply_file_uri;
		$org_write_exam_apply_file_path	= str_replace( Config::base('UPLOAD_BASE_URI'), Config::base('UPLOAD_PATH'), $org_write_exam_apply_file_uri );
		$org_write_exam_apply_file_uri	= $this->_uploadWriteExamApplyFile();
		@unlink( $org_write_exam_apply_file_path );
		return $org_write_exam_apply_file_uri;
	}

	/**
	 * upload file
	 */
	private function _uploadWriteExamApplyFile()
	{
		$upload_plugin	= Go::plugin('Upload');
		$upload_plugin->setProperty('upload_field','write_exam_apply_file');
		$upload_plugin->setProperty('upload_dir', Config::base('UPLOAD_PATH') . DIRECTORY_SEPARATOR . 'write_exam_apply');
		$upload_plugin->setProperty('upload_base_uri', Config::base('UPLOAD_BASE_URI') . DIRECTORY_SEPARATOR . 'write_exam_apply');
		$upload_plugin->setProperty('upload_limit_size', 2096552);
		$upload_infos	= $upload_plugin->doAction();
		if( $upload_infos['error'] == TRUE ) throw new MsgException( $upload_infos['message'] );
		return $upload_infos['data']['upload_uri'];
	}
}
