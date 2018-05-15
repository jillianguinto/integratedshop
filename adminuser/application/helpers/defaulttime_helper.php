<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists('settimezome_taipei'))
	{
		function settimezome_taipei()
		{	
            date_default_timezone_set('Asia/Taipei');
		}
	}
	
