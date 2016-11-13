<?php 

if (!function_exists('escape_content')) {
	function escape_content($content)
	{
		if ($content == base64_encode(base64_decode($content))){
			if(preg_match("/^\d*$/",$content)){
				return $content;
			}elseif(preg_match("/^[a-z]+$/",$content)){
				return $content;
			}else{	
				return base64_decode($content);
			} 
		}	
		return $content;
	}
}