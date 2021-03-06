<?php
namespace traits;
trait Validation
{
	/**
	 * validation integer
	 * @param (int) $value
	 * @param (int) $min
	 * @param (int) $max
	 * @return boolean
	 */
	public static function vdtInt( $value, $min = 1, $max = 4294967295 )
	{
		return ctype_digit((string) $value ) && $value >= $min && $value <= $max ;
	}
	/**
	 * validation integer
	 * @param (int) $value
	 * @param (int) $min
	 * @param (int) $max
	 * @return boolean
	 */
	public static function vdtDec( $value, $int_qty = 1, $dec_qty = 1, $positive = FALSE )
	{
		if(is_numeric( $value ) == FALSE ) return FALSE;
		@list( $int_val, $dec_val )	= explode( '.', $value );
		if( $positive == TRUE ) return is_numeric($value) && $value >= 0 && strlen( $int_val ) <= $int_qty && strlen( $dec_val ) <= $dec_qty;
		else return strlen( $int_val ) <= $int_qty && strlen( $dec_val ) <= $dec_qty;
	}
	/**
	 * validation regular
	 * @param (string) $value
	 * @param (string) $reg
	 * @return boolean
	 */
	public static function vdtReg( $value, $reg )
	{
		return preg_match( $reg, $value ) === 1;
	}
	/**
	 * validation string
	 * @param (string) $value
	 * @param (int) $min_len
	 * @param (int) $max_len
	 * @return boolean
	 */
	public static function vdtStr( $value, $min_len = 0 , $max_len = 45 )
	{
		$len	= strlen(trim( $value ));
		return $len >= $min_len && $len <= $max_len;
	}
	/**
	 * validation flag
	 * @param (string) $value
	 * @return boolean
	 */
	public static function vdtFlg( $value )
	{
		return $value == 'T' || $value == 'F';
	}
	/**
	 * validation date time
	 */
	public static function vdtDatTim( $value )
	{
		return date('YmdHis', strtotime( $value )) == str_replace(array('-',' ',':'), '', $value);
	}

	/**
	 * validation date
	 */
	public static function vdtDate( $value )
	{
		return date('Ymd', strtotime( $value )) == str_replace(array('-',' ',':'), '', $value);
	}

	/**
	 * validation page number
	 * @param (int) $page
	 * @return boolean
	 */
	public function vdtPage( $page )
	{
		return self::vdtInt( $page, 1 ) ;
	}
	/**
	 * validation total number
	 * @param (int) $total
	 * @return boolean
	 */
	public function vdtTotal( $total )
	{
		return self::vdtInt( $total, 0 ) ;
	}
	/**
	 * validation page rows
	 * @param (int) $rows
	 * @return boolean
	 */
	public function vdtRows( $rows )
	{
		return self::vdtInt( $rows, 1 ) ;
	}
}