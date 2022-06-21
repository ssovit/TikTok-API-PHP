<?php
namespace TikTok;

use Exception;

class TikTokRequest
{
	public $app_secret;

	public $headerParams = array();

	public $udfParams = array();

	public $fileParams = array();

	public $httpMethod = 'POST';

	public function __construct($app_secret,$httpMethod = 'POST')
	{
		$this->app_secret = $app_secret;
		$this->httpMethod = $httpMethod;

		if($this->startWith($app_secret,"//"))
		{
			throw new Exception("api name is invalid. It should be start with /");			
		}
	}


	function addApiParam($key,$value)
	{

		if(!is_string($key))
		{
			throw new Exception("api param key should be string");
		}

		if(is_object($value))
		{
			$this->udfParams[$key] = json_decode($value);
		}
		else
		{
			$this->udfParams[$key] = $value;
		}
	}

	function addFileParam($key,$content,$mimeType = 'application/octet-stream')
	{
		if(!is_string($key))
		{
			throw new Exception("api file param key should be string");
		}

		$file = array(
            'type' => $mimeType,
            'content' => $content,
            'name' => $key
        );
		$this->fileParams[$key] = $file;
	}

	function addHttpHeaderParam($key,$value)
	{
		if(!is_string($key))
		{
			throw new Exception("http header param key should be string");
		}

		if(!is_string($value))
		{
			throw new Exception("http header param value should be string");
		}

		$this->headerParams[$key] = $value;
	}

	function startWith($str, $needle) {
	    return strpos($str, $needle) === 0;
	}
}

?>