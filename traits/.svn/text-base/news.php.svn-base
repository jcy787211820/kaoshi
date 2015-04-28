<?php
namespace traits;
use \core\CException,
	\core\Go AS Go,
	\core\Database AS Database;
trait News
{
	/**
	 * insert new news
	 * @param \manager\News $news_manager
	 * @param array $category_codes
	 */
	public function newNews(\manager\News $news_manager, array $category_codes )
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
		$category_map_news_manager	= Go::manager('CategoryMapNews');

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$news_manager->insert();
			if(!empty( $category_codes ))
			{
				$category_map_news		= array();
				foreach( $category_codes AS $category_code )
				{
					$category_map_news[]	= array(
							'category_code'		=> $category_code,
							'news_seq'			=> $news_manager->news_seq,
					);
				}
				$category_map_news_manager->inserts( $category_map_news );
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
	 * insert update news
	 * @param \manager\News $news_manager
	 * @param array $category_codes
	 */
	public function updateNews(\manager\News $news_manager, array $category_codes )
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
		$category_map_news_manager	= Go::manager('CategoryMapNews');

		/*
		 * category mapping infos
		 */
		$delete_category_map_news_seqs	= array();	//delete seq
		$already_category_map_news_cds	= array();	//already exists category codes
		$category_map_news_manager->setFields(array('category_map_news_seq','category_code'));
		$category_map_newss				= $category_map_news_manager->loadByNewsSeq( $news_manager->news_seq );
		foreach( $category_map_newss AS $category_map_news )
		{
			$is_set		= FALSE;
			if(in_array( $category_map_news['category_code'], $category_codes ))
			{
				$is_set	= TRUE;
				$already_category_map_news_cds[]	= $category_map_news['category_code'];
			}
			if( $is_set == FALSE ) $delete_category_map_news_seqs[]	= $category_map_news['category_map_news_seq'];
		}
		$category_codes	= array_diff( $category_codes, $already_category_map_news_cds );
		unset( $category_map_newss, $already_category_map_news_cds );

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$news_manager->update();
			if(!empty( $category_codes ))
			{
				$category_map_newss		= array();
				foreach( $category_codes AS $category_code )
				{
					$category_map_newss[]	= array(
							'category_code'	=> $category_code,
							'news_seq'		=> $news_manager->news_seq,
					);
				}
				$category_map_news_manager->inserts( $category_map_newss );
			}
			if(!empty( $delete_category_map_news_seqs )) $category_map_news_manager->delete( $delete_category_map_news_seqs );

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
	 * delete news
	 * @param Array $news_seqs
	 */
	public function deleteNews(array $news_seqs )
	{

		/*
		 * class
		*/
		$news_manager				= Go::manager('News');
		$category_map_news_manager	= Go::manager('CategoryMapNews');

		/*
		 * process database
		 */
		try
		{
			/********************/
			Database::transtart();
			/********************/
			$news_manager->delete( $news_seqs );
			$category_map_news_manager->deleteByNewsSeqs( $news_seqs );
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