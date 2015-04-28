<?php
namespace plugin;
/**
 * captcha plugin
 */
class Captcha
{
    private	$session_name		= 'session_name',
 	 		$session_path		= '',
 	 		$session_domain		= '',
 	 		$session_secure 	= FALSE,
 	 		$session_httponly	= FALSE,
    		$width      		= 0,
    		$height     		= 0,
			$bg_color			= NULL,
			$txt_color			= NULL,
    		$has_point			= TRUE;

    /**
     * Construct
     * @param   string  $folder
     * @param   integer $width
     * @param   integer $height
     */
	public function __construct(
		$session_name		= NULL,
		$session_path		= '',
		$session_domain		= '',
		$session_secure		= FALSE,
		$session_httponly	= FALSE,
		$width				= NULL,
		$height				= NULL
	){
    	if( $session_name != NULL && $width != NULL && $height != NULL ) $this->init(
    			$session_name,
    			$session_path,
    			$session_domain,
    			$session_secure,
    			$session_httponly,
    			$width,
    			$height
    	);
    }

    /**
     * init
     */
    public function init( $session_name, $session_path, $session_domain, $session_secure, $session_httponly, $width, $height )
    {
    	$this->session_name			= $session_name;
    	$this->session_path			= $session_path;
    	$this->session_domain		= $session_domain;
    	$this->session_secure		= $session_secure;
    	$this->session_httponly		= $session_httponly;
    	$this->width    			= $width;
    	$this->height   			= $height;
    }

    /**
     * set property value
     */
    public function setVarValue( $key, $value )
    {
		if(property_exists( $this, $key ) == TRUE)
		{
			$this->{$key}	= $value;
		}
    }

    /**
     * check captcha
     * @param   string  $word
     * @return  bool
     */
    public function check( $captcha )
    {
    	$cookie	= $this->_getCookie();
    	$this->_unsetCookie();
		return !empty( $cookie ) && $this->_encrypts(strtoupper( $captcha )) === $cookie;
    }

    /**
     * make captcha image
     * @param   string  $word   验证码
     * @return  mix
     */
	public function generateImage( $word = NULL )
    {
    	/*
    	 * make captcha word
    	 */
        $word = is_null( $word ) ? $this->_generateWord() : $word;

        /*
         * captch word length
         */
        $letters = strlen( $word );

        /*
         * create image
         */
        $img	= imagecreatetruecolor ( $this->width, $this->height );

        // color
		$bg_color	= is_null( $this->bg_color )
					? imagecolorallocate( $img, rand( 0, 128 ), rand( 0, 128 ), rand( 0, 128 ))
					: imagecolorallocate( $img, $this->bg_color[0], $this->bg_color[1], $this->bg_color[2] );
		$txt_color	= is_null( $this->txt_color )
					? imagecolorallocate( $img , rand( 128, 255 ), rand( 128, 255 ), rand( 128, 255 ))
					: imagecolorallocate( $img , $this->txt_color[0], $this->txt_color[1], $this->txt_color[2] );

		// background
		imagefilledrectangle( $img, 0, 0, $this->width, $this->height, $bg_color );

		// add point
		if($this->has_point	== TRUE )
		{
			for($i = 0; $i < 100; $i++)
			{
				$point_color	= imagecolorallocate( $img, rand( 0, 255 ), rand( 0, 255 ), rand( 0, 255 ));
				$rand_x			= rand(0, $this->width);
				$rand_y			= rand(0, $this->height);
				imageline( $img, $rand_x, $rand_y, $rand_x + 1, $rand_y + 1, $point_color );
			}
		}

		// add letter
		$x = ($this->width - (imagefontwidth(5) * $letters)) / 2;
		$y = ($this->height - imagefontheight(5)) / 2;
		imagestring($img, 5, $x, $y, $word, $txt_color);

		// print image
		header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
		// HTTP/1.1
		header('Cache-Control: private, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0, max-age=0', false);
		// HTTP/1.0
		header('Pragma: no-cache');
		header('Content-type: image/jpeg');
		imagejpeg($img);
		imagedestroy($img);
    }

    /**
     * encrypts captha
     * @param   string  $word
     * @return  string
     */
    private function _encrypts( $word )
    {
		return sha1(md5( $word ) . $_SERVER['REMOTE_ADDR'] );
    }

    /**
     * make catpcha
     * @access  private
     * @param   integer $length
     * @return  string
     */
	private function _generateWord( $length = 4 )
    {
    	/*
    	 * make word
    	 */
		$chars	= '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
		$word	= substr(str_shuffle( $chars ), 0, $length );

        /*
         * session
         */
		$this->_setCookie($this->_encrypts( $word ));

		/*
		 * return
		 */
		return $word;
    }

    /**
     * get cookie
     */
    private function _getCookie()
    {
    	if(isset( $_COOKIE[$this->_cookieName()] )) return $_COOKIE[$this->_cookieName()];
    	else return FALSE;
    }

    /**
     * set cookie
     */
    private function _setCookie( $value )
    {
    	setcookie( $this->_cookieName(), $value, 0, $this->session_path, $this->session_domain, $this->session_secure, $this->session_httponly );
    }

    /**
     * unset cookie
     */
    private function _unsetCookie()
    {
    	setcookie( $this->_cookieName(), '', 1 );
    }

    /**
     * get cookie name
     * @return string
     */
    private function _cookieName()
    {
    	return md5($this->session_name . $_SERVER['REMOTE_ADDR'] );
    }
}
?>