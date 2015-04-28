<?php
namespace plugin;
/**
 * upload file plugin
 */
class Upload
{
	private $upload_field				= NULL,			// upload input field name $_FILES[$upload_field]
			$upload_base_uri			= NULL,			// file upload base uri
			$upload_dir					= NULL,			// file upload path
			$upload_name				= NULL,			// The filename to allow,if null then this is md5( tmp name + microtime )。If upload multi files,then cannot setting upload name.
			$upload_limit_size			= array(),		// 最大文件大小
			$upload_limit_types			= array(),		// upload limit types
			$upload_limit_extensions	= array(),		// upload limit types
			$upload_error				= FALSE,
			$upload_error_message		= '';

	/**
     * Construct
     */
	public function __construct( $init_data = array())
	{
		$this->init( $init_data );
    }

    /**
     * init
     */
    public function init( $init_data )
    {
    	foreach( $init_data AS $key => $value)
    	{
			$this->setProperty( $key, $value );
    	}
    }

    /**
     * set property value
     */
    public function setProperty( $key, $value )
    {
		if(property_exists( $this, $key ) == TRUE)
		{
			$this->{$key}	= $value;
			return TRUE;
		}
		return FALSE;
    }

    /**
     * action function
     * @return (array) $upload_infos
     * 利用函数递归的方式将上传完成的信息，和原来$_FILES里面的信息合并以后返回
     * 'name'
	 * 'type'
	 * 'tmp_name'
	 * 'error'
	 * 'size'
	 * 'upload_path'
	 * 'message'
     */
    public function doAction()
    {
    	/*
    	 * check property is normal
    	 */
    	if(!isset( $_FILES[$this->upload_field] )) $this->_setError('None upload file.');
    	if(is_writable( $this->upload_dir ) == FALSE)
    	{
    		if(mkdir( $this->upload_dir, 0744 ) == FALSE) $this->_setError('This upload dir is not writable.');
    	}
    	if(!is_null( $this->upload_name ) && !is_string( $_FILES[$this->upload_field] )) $this->_setError('Upload multi files, cannot setting upload name.');

    	if( $this->upload_error == TRUE ) return $this->_getError();

    	/*
    	 * upload
    	 */
    	$upload_infos	= $_FILES[$this->upload_field];		//return upload $_FILES info + uploaded info
    	$this->_fileUpload( $upload_infos );

    	/*
    	 * return
    	 */
    	return $this->_makeReturnData( $upload_infos );
    }

    /**
     * do upload file action
     * 本函数递归方式动作,应对递归方式生成的$_FILES,
     * 假设$keys	= array(),并且$_FILES['name']是array,含有$_FILES['name']['a'],那么将循环$_FILES['name'],并回掉函数（方式是( $upload_infos, array('a')),
     * 假设$keys	= array('a'),并且$_FILES['name']是array,含有$_FILES['name']['a']['aa'],那么将循环$_FILES['name']['aa'],并回掉函数（方式是( $upload_infos, array('a','aa')),
     * 同上类推....
     * 假设$keys	= array('a','aa',0),并且$_FILES['name']['a']['aa'][0]是上传文件的字符串名字,那么将进行$_FILES['name']['a']['aa'][0]文件的上传处理.
     * @param (array) $upload_infos
     * 本函数不直接改变 $upload_infos,
     * $upload_infos初始值等于$_FILES,
     * 在某个$key文件上传完成的时候将 $upload_infos传入‘setUploadInfo’方法，
     * 并设置这个$key文件上传以后的信息。
     * @param (array) $keys
     * $keys 用户判断当前处理文件的KEY。
     */
	private function _fileUpload(& $upload_infos, $keys = array())
	{
		/*
		 * INIT VARS
		 */
		$upload_file_name			= $upload_infos['name'];
		$upload_file_type			= $upload_infos['type'];
		$upload_file_tmp_name		= $upload_infos['tmp_name'];
		$upload_file_error			= $upload_infos['error'];
		$upload_file_size			= $upload_infos['size'];
		$upload_file_upload_path	= '';
		$upload_file_upload_uri		= '';
		$upload_file_message		= '';
		foreach( $keys AS $key )
		{
			$upload_file_name		= $upload_file_name[$key];
			$upload_file_type		= $upload_file_type[$key];
			$upload_file_tmp_name	= $upload_file_tmp_name[$key];
			$upload_file_error		= $upload_file_error[$key];
			$upload_file_size		= $upload_file_size[$key];
		}

		/*
		 * If $upload_file_name is string name, then upload start
		 */
    	if(is_string( $upload_file_name ))
    	{
			switch( $upload_file_error )
			{
				// None error.
				case UPLOAD_ERR_OK:
						// setting move upload base info
						$upload_file_extensions	= strtolower(strstr( $upload_file_name, '.' ));
						$upload_name	= is_null( $this->upload_name )
										? sha1( $upload_file_tmp_name . uniqid( $_SERVER['REMOTE_ADDR'] )) . $upload_file_extensions
										: $this->upload_name;
						$upload_path	= $this->upload_dir . DIRECTORY_SEPARATOR . $upload_name;
						$upload_uri		= $this->upload_base_uri . DIRECTORY_SEPARATOR . $upload_name;

						// check file extensions
						if(!empty( $this->upload_limit_extensions ) && !in_array(substr( $upload_file_extensions, 1 ), $this->upload_limit_extensions ))
						{
							$upload_file_error		= -1;
							$upload_file_message	= "Upload error, file extension is limited";
						}

						// check file mime type
						if(!empty( $this->upload_limit_types ) && !in_array( $upload_file_type, $this->upload_limit_types ))
						{
							$upload_file_error		= -1;
							$upload_file_message	= "Upload error, file mime type is limited";
						}

						// file size check
						if( $upload_file_size <= 0 )
						{
							$upload_file_error		= -1;
							$upload_file_message	= "Upload error, the file size is equal to zero";
						}
						else if( $upload_file_size > $this->upload_limit_size)
						{
							$upload_file_error		= -1;
							$upload_file_message	= "Upload error, the file size cannot greater than {$this->upload_limit_size}.";
						}

						if( $upload_file_error === UPLOAD_ERR_OK )
						{
							if(move_uploaded_file( $upload_file_tmp_name, $upload_path ) == TRUE)
							{
								$upload_file_upload_path	= $upload_path;
								$upload_file_upload_uri		= $upload_uri;
								$upload_file_message		= "There is no error, the file uploaded with success";
							}
							else
							{
								$upload_file_error		= -1;
								$upload_file_message	= "Upload Error, move action error.";
							}
						}
					break;
				// Error set message.
				case UPLOAD_ERR_INI_SIZE:
						$upload_file_message	= "The uploaded file exceeds the upload_max_filesize directive in php.ini";
					break;
				case UPLOAD_ERR_FORM_SIZE:
						$upload_file_message	= "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
					break;
				case UPLOAD_ERR_PARTIAL:
						$upload_file_message	= "The uploaded file was only partially uploaded";
					break;
				case UPLOAD_ERR_NO_FILE:
						$upload_file_message	= "No file was uploaded";
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
						$upload_file_message	= "Missing a temporary folder";
					break;
				case UPLOAD_ERR_CANT_WRITE:
						$upload_file_message	= "Failed to write file to disk";
					break;
				case UPLOAD_ERR_EXTENSION:
						$upload_file_message	= "File upload stopped by extension";
					break;
				default:
						$upload_file_error		= -1;
						$upload_file_message	= "Unknown upload error";
			}

			//check upload is error
			if( $upload_file_error !== UPLOAD_ERR_OK ) $this->_setError('文件上传时有错误发生.');

			//Current uploaded info
			$upload_info		= array(
				'name'			=> $upload_file_name,
				'type'			=> $upload_file_type,
				'tmp_name'		=> $upload_file_tmp_name,
				'error'			=> $upload_file_error,
				'size'			=> $upload_file_size,
				'upload_path'	=> $upload_file_upload_path,
				'upload_uri'	=> $upload_file_upload_uri,
				'message'		=> $upload_file_message,
			);

			//Do $upload_info append to $upload_infos
			$this->_setUploadInfo(
				$upload_infos['name'],
				$upload_infos['type'],
				$upload_infos['tmp_name'],
				$upload_infos['error'],
				$upload_infos['size'],
				$upload_infos['upload_path'],
				$upload_infos['upload_uri'],
				$upload_infos['message'],
				$upload_info,
				$keys
			);
    	}
    	/*
    	 * If current $upload_file_name is array, then callback self function.
    	 */
    	else if(is_array( $upload_file_name ))
    	{
			foreach( $upload_file_name AS $upload_file_name_key => $upload_file_name_value )
			{
				$next_keys		= $keys;
				$next_keys[]	= $upload_file_name_key;
				$this->_fileUpload(
					$upload_infos,
					$next_keys
				);
			}
    	}
    }

    /**
     * set uploaded info
     * @param (array) $name
     * @param (array) $type
     * @param (array) $tmp_name
     * @param (array) $error
     * @param (array) $size
     * @param (array) $upload_path
     * @param (array) $upload_uri
     * @param (array) $message
     * @param (array) $upload_info
     * @param (array) $keys
     * 根据$keys把需要添加的upload_info,添加到对应的key的upload_infos
     */
    private function _setUploadInfo(& $name,& $type,& $tmp_name,& $error,& $size,& $upload_path,& $upload_uri,& $message, $upload_info, $keys )
    {
    	/*
    	 * 如果只上传了一个文件，并且$_FILES['name']为string的时候,$keys应该直接是空的array()
    	 */
    	if(empty( $keys ))
    	{
    		$name			= $upload_info['name'];
    		$type			= $upload_info['type'];
    		$tmp_name		= $upload_info['tmp_name'];
    		$error			= $upload_info['error'];
    		$size			= $upload_info['size'];
    		$upload_path	= $upload_info['upload_path'];
    		$upload_uri		= $upload_info['upload_uri'];
    		$message		= $upload_info['message'];
    	}
    	/*
    	 * 如果只上传了多个文件，根据$keys回掉本函数，添加upload_info
    	 */
    	else
    	{
	    	$key	= array_shift( $keys );
	    	//如果到了最后一个key，则写入upload_info
	    	if(empty( $keys ))
	    	{
    			$name[$key]			= $upload_info['name'];
    			$type[$key]			= $upload_info['type'];
    			$tmp_name[$key]		= $upload_info['tmp_name'];
    			$error[$key]		= $upload_info['error'];
    			$size[$key]			= $upload_info['size'];
    			$upload_path[$key]	= $upload_info['upload_path'];
    			$upload_uri[$key]	= $upload_info['upload_uri'];
    			$message[$key]		= $upload_info['message'];
	    	}
	    	//如果存在next key，则回掉函数
	    	else
	    	{
	    		$this->_setUploadInfo(
	    			$name[$key],
	    			$type[$key],
	    			$tmp_name[$key],
	    			$error[$key],
	    			$size[$key],
	    			$upload_path[$key],
	    			$upload_uri[$key],
	    			$message[$key],
	    			$upload_info,
	    			$keys
	    		);
	    	}
    	}
    }

    /**
     * setting error
     * @param (string) $error_message
     */
	private function _setError( $error_message = '' )
	{
		$this->upload_error			= TRUE;
		$this->upload_error_message	= $error_message;
	}

	/**
	 * Get error
	 * @param (string) $error_message
	 */
	private function _getError()
	{
		return array(
			'error'		=> $this->upload_error,
			'message'	=> $this->upload_error_message,
		);
	}

	/**
	 * make return data
	 * @param (array) $data
	 * @return array
	 */
	private function _makeReturnData( $data = array())
	{
		return array(
			'error'		=> $this->upload_error,
			'message'	=> $this->upload_error_message,
			'data'		=> $data,
		);
	}
}
?>