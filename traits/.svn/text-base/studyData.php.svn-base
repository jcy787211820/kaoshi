<?php
namespace traits;
use \core\CException,
	\core\Go AS Go,
	\core\Database AS Database;
trait StudyData
{
	/**
	 * insert new study data
	 * @param \manager\StudyData $study_data_manager
	 * @param array $category_codes
	 */
	public function newStudyData(\manager\StudyData $study_data_manager, array $category_codes )
	{
		/*
		 * check $category_codes
		 */
		if(empty( $category_codes )) throw new CException('文档展示的分类不能为空.');
		$category_codes				= array_unique( $category_codes );
		foreach( $category_codes AS $key => $category_code )
		{
			foreach( $category_codes AS $chk_category_code )
			{
				if(stripos( $category_code, $chk_category_code ) === 0 && $category_code != $chk_category_code )
				{
					unset( $category_codes[$key] );
					break;
				}
			}
		}

		/*
		 * class
		 */
		$category_map_study_data_manager	= Go::manager('CategoryMapStudyData');

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$study_data_manager->insert();
			if(!empty( $category_codes ))
			{
				$category_study_data_maps		= array();
				foreach( $category_codes AS $category_code )
				{
					$category_study_data_maps[]	= array(
							'category_code'		=> $category_code,
							'study_data_seq'	=> $study_data_manager->study_data_seq,
					);
				}
				$category_map_study_data_manager->inserts( $category_study_data_maps );
			}

			/*****************/
			Database::commit();
			/*****************/
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}

		/*
		 * return
		 */
		return TRUE;
	}

	/**
	 * insert update Study data
	 * @param \manager\StudyData $study_data_manager
	 * @param array $category_codes
	 */
	public function updateStudyData(\manager\StudyData $study_data_manager, array $category_codes )
	{
		/*
		 * check $category_codes
		 */
		if(empty( $category_codes )) throw new CException('文档展示的分类不能为空.');
		$category_codes	= array_unique( $category_codes );
		foreach( $category_codes AS $key => $category_code )
		{
			foreach( $category_codes AS $chk_category_code )
			{
				if(stripos( $category_code, $chk_category_code ) === 0 && $category_code != $chk_category_code )
				{
					unset( $category_codes[$key] );
					break;
				}
			}
		}

		/*
		 * class
		 */
		$category_map_study_data_manager	= Go::manager('CategoryMapStudyData');

		/*
		 * category mapping infos
		 */
		$delete_category_map_study_data_seqs	= array();	//delete seq
		$already_category_map_study_data_cds	= array();	//already exists category codes
		$category_map_study_data_manager->setFields(array('category_map_study_data_seq','category_code'));
		$category_map_study_datas				= $category_map_study_data_manager->loadByStudyDataSeq( $study_data_manager->study_data_seq );
		foreach( $category_map_study_datas AS $category_map_study_data )
		{
			$is_set		= FALSE;
			if(in_array( $category_map_study_data['category_code'], $category_codes ))
			{
				$is_set	= TRUE;
				$already_category_map_study_data_cds[]	= $category_map_study_data['category_code'];
			}
			if( $is_set == FALSE ) $delete_category_map_study_data_seqs[]	= $category_map_study_data['category_map_study_data_seq'];
		}
		$category_codes	= array_diff( $category_codes, $already_category_map_study_data_cds );
		unset( $category_map_study_datas, $already_category_map_study_data_cds );

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$study_data_manager->update();
			if(!empty( $category_codes ))
			{
				$category_map_study_datas		= array();
				foreach( $category_codes AS $category_code )
				{
					$category_map_study_datas[]	= array(
							'category_code'		=> $category_code,
							'study_data_seq'	=> $study_data_manager->study_data_seq,
					);
				}
				$category_map_study_data_manager->inserts( $category_map_study_datas );
			}
			if(!empty( $delete_category_map_study_data_seqs )) $category_map_study_data_manager->delete( $delete_category_map_study_data_seqs );

			/*****************/
			Database::commit();
			/*****************/
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}

		/*
		 * return
		 */
		return TRUE;
	}

	/**
	 * delete document
	 * @param Array $document_seqs
	 */
	public function deleteStudyData(array $study_data_seqs )
	{

		/*
		 * class
		*/
		$study_data_manager					= Go::manager('StudyData');
		$category_map_study_data_manager	= Go::manager('CategoryMapStudyData');

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$study_data_manager->delete( $study_data_seqs );
			$category_map_study_data_manager->deleteByStudyDataSeqs( $study_data_seqs );
			/*****************/
			Database::commit();
			/*****************/
		}
		catch(Exception $e)
		{
			/*******************/
			Database::rollback();
			/*******************/
			throw $e;
		}

		/*
		 * return
		*/
		return TRUE;
	}
}