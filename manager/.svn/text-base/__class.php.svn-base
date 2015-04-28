<?php
/**
 * base manager
 */
namespace manager;
includeTraits('Validation');
use \core\Database AS Database,
	\core\CException AS CException,
	\core\Go AS Go;
class Manager
{

	use \traits\validation;

	public		$primary_key		= NULL,				//	table primary key
				$fields				= NULL,				//	table fileds
				$unescapes			= NULL,				//	none escape fileds
				$database			= NULL,				//	database name
				$table				= NULL,				//	table name
				$where				= NULL,				//	query where
				$order				= NULL,				//	query order
				$group				= NULL,				//	query group
				$limit				= NULL,				//	query limit
				$query				= NULL,				//	query result
				$create_log			= FALSE,
				$log_keys			= array();

	/**
	 * construct set this db
	 * @param (array) $init_data key is this class vars
	 */
	public function __construct(array $init_data = array() )
	{
		if(empty( $init_data ) == TRUE ) return;

		$cls_vars	= get_class_vars(__CLASS__);
		foreach( $cls_vars AS $cls_var_nm => $cls_var_val )
		{
			if(isset( $init_data[$cls_var_nm] ) == TRUE )
			{
				$this->$cls_var_nm	= $init_data[$cls_var_nm];
				unset( $init_data[$cls_var_nm] );
			}
			if(empty( $init_data )) break;
		}
	}

	/**
	 * set query fileds
	 * @param (array) $fileds
	 */
	public function setFields( $fileds )
	{
		$this->fields	= $fileds;
	}
	/**
	 * set none escape fileds
	 * @param (array) $keys
	 */
	public function setUnescapes( $keys )
	{
		$this->unescapes	= $keys;
	}

	/**
	 * ser query where
	 * @param (array) $where
	 */
	public function setWhere( $where )
	{
		$this->where	= $where;
	}

	/**
	 * set query group
	 * @param (array) $group
	 */
	public function setGroup( $group )
	{
		$this->group	= $group;
	}

	/**
	 * set query order
	 * @param (array) $order
	 */
	public function setOrder( $order )
	{
		$this->order	= $order;
	}
	/**
	 * set list page number and page count row
	 * @param (int) $page
	 * @param (size) $size
	 */
	public function setPage( $page, $size )
	{
		$this->setLimit(( $page - 1 ) * $size, $size );
	}

	/**
	 * set query limit
	 * @param (int) $limit_start
	 * @param (int) $limit_end
	 */
	public function setLimit( $limit_start, $limit_end )
	{
		$this->limit	= array( $limit_start, $limit_end );
	}
	/**
	 * select count
	 * @return int
	 */
	public function preTotal()
	{
		$tmp_fields	= $this->fields;
		$tmp_limit	= $this->limit;
		$this->setFields(array('COUNT(*) AS `count`'));
		$this->setLimit(0,1);
		$total			= $this->select( MYSQL_ONE );
		$this->fields	= $tmp_fields;
		$this->limit	= $tmp_limit;
		return $total;
	}
	/**
	 * select data
	 * @param (int) $rst_typ
	 * @return array
	 */
	public function select( $rst_typ = MYSQL_ASSOC )
	{
		/*
		 * make SQL
		 * @var (string) $sql
		 */
		if( $this->fields == NULL ) $this->fields = array('*');
		$sql = "
			SELECT
				" . implode(',', $this->fields ) . "
			FROM
				`{$this->database}`.`{$this->table}` " .
			(count( $this->where ) > 0 ? ' WHERE ' . implode(' AND ', $this->where ) : '' ) .
			(count( $this->group ) > 0 ? ' GROUP BY ' . implode(',', $this->group ) : '' ) .
			(count( $this->order ) > 0 ? ' ORDER BY ' . implode(',', $this->order ) : '' ) .
			(count( $this->limit ) > 0 ? ' LIMIT ' . implode(',', $this->limit ) : '' );

		/*
		 * process
		 */
		$this->query	= Database::query( $sql );
		if( $this->query == FALSE ) throw new CException('SQL ERROR: ' . $sql );

		/*
		 * return
		 */
		return Database::result( $this->query, $rst_typ );
	}
	/**
	 * insert data
	 * @return boolean
	 * $option array(
	 * 	'IGNORE'
	 * )
	 */
	public function insert($option = array())
	{
		/*
		 * init var option
		 */
		$ignore				= isset( $option['IGNORE'] ) ? $option['IGNORE'] : '';

		/*
		 * make insert data
		 * @var (array) $field_names
		 * @var (array) $field_values
		 */
		$field_names 	= array();
		$field_values	= array();
		if(is_array(current( $this->fields )) == TRUE)	// insert multi row
		{
			foreach( $this->fields AS $key => $field )
			{
				$tmp_field_values	= array();
				if(empty( $field_names )) $field_names = array_keys( $field );
				foreach( $field AS $field_name => $field_value )
				{
					$tmp_field_values[]			= is_array( $this->unescapes ) && in_array( $field_name, $this->unescapes ) ? $field_value : Database::escapeString( $field_value );
				}
				$field_values[$key]	= '(' . implode(',', $tmp_field_values ) . ')';
			}
		}
		else	//insert one row
		{
			$tmp_field_values	= array();
			foreach((array) $this->fields AS $field_name => $field_value )
			{
				$field_names[]			= $field_name;
				$tmp_field_values[]		= is_array( $this->unescapes ) && in_array( $field_name, $this->unescapes ) ? $field_value : Database::escapeString( $field_value );
			}
			$field_values[]	= '(' . implode(',', $tmp_field_values ) . ')';
		}

		/*
		 * make SQL
		 * @var (string) $sql
		 */
		$sql	= "
			INSERT {$ignore} INTO
				`{$this->database}`.`{$this->table}` (`" . implode("`,`", $field_names ) . "`)
			VALUES " . implode(",", $field_values);


		/*
		 * process
		 */
		$this->query	= Database::query( $sql );
		if( $this->query == FALSE ) throw new CException('SQL ERROR: ' . $sql );

		/*
		 * last insert id
		 */
		$this->{$this->primary_key}	= Database::insertId();

		/*
		 * return
		 */
		return $this->query;
	}
	/**
	 * update data
	 * @return boolean
	 */
	public function update()
	{
		/*
		 * make update data
		 * @var (array) $update_items
		 */
		$update_items	= array();
		foreach( $this->fields AS $field_name => $field_value )
		{
			if(is_array( $this->unescapes ) && in_array( $field_name, $this->unescapes )) $update_items[]	= "`{$field_name}` = {$field_value}";
			else $update_items[]	= "`{$field_name}` = " . Database::escapeString( $field_value );
		}

		/*
		 * make SQL
		 * @var (string) $sql
		 */
		$sql = "
			UPDATE
				`{$this->database}`.`{$this->table}` " .
			"SET" .  implode( ',', $update_items ) .
			(count( $this->where ) > 0 ? ' WHERE ' . implode(' AND ', $this->where ) : '' );

		/*
		 * process
		 */
		$this->query	= Database::query( $sql );

		/*
		 * return
		 */
		return $this->query;
	}
	/**
	 * delete data
	 * @return boolean
	 */
	public function delete()
	{
		/*
		 * make SQL
		 * @var (string) $sql
		 */
		$sql	= "
			DELETE FROM
				`{$this->database}`.`{$this->table}` " .
			(count( $this->where ) > 0 ? ' WHERE ' . implode(' AND ', $this->where ) : '' );

		/*
		 * process
		 */
		$this->query	= Database::query( $sql );

		/*
		 * return
		 */
		return $this->query;
	}

	/**
	 * reset query result key
	 * @param (string) $key
	 */
	public function resetListsKey( $lists, $key, $return_multi = FALSE )
	{
		/*
		 * VAR
		 */
		$result = array();

		/*
		 * make result
		 */
		foreach( $lists AS $list )
		{
			if(isset( $list[$key] ) == TRUE )
			{
				$result_key				= $list[$key];
				if( $return_multi == TRUE )
				{
					if(isset( $result[$result_key] ) == FALSE ) $result[$result_key]	= array();
					$result[$result_key][]	= $list;
				}
				else
				{
					$result[$result_key]	= $list;
				}
			}
		}

		/*
		 * return
		 */
		return $result;
	}

	/**
	 * edit id
	 */
	protected function editId( $edit_id_field, $must = TRUE )
	{
		if(!isset( $this->{$edit_id_field} ))  throw new CException('Field name error: ' . $edit_id_field );
		if(isset( $_SESSION['user_id'] ) && empty( $this->{$edit_id_field} )) return $_SESSION['user_id'];
		else if(empty( $this->{$edit_id_field} ) && $must == TRUE ) throw new CException("{$edit_id_field} Error.");
		else return $this->{$edit_id_field};
	}

	/**
	 * edit time
	 */
	protected function editTime( $edit_time_field )
	{
		if(!isset( $this->{$edit_time_field} ))  throw new CException('Field name error: ' . $edit_time_field );

		/*
		 * set
		*/
		if(defined('REQUEST_TIME') && empty( $this->{$edit_time_field} )) return REQUEST_TIME;
		else if(empty( $this->{$edit_time_field} )) throw new CException("{$edit_time_field} Error.");
		else return $this->{$edit_time_field};
	}
}