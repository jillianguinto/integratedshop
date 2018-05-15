<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists('get_baseurl'))
	{
		function get_baseurl()
		{
			
			$CI 		= & get_instance();
			$sessionid 	= $CI->input->cookie('useraccount',true);
		
			return  "http://".$_SERVER['SERVER_NAME'];
		}
		
	}
	

