<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists('message_handler'))
	{
		function message_handler($message, $type)
		{	
			if(strtolower($type) == 'success'):
				$errorhandler = "<div class='alert alert-success'><i class='glyphicon glyphicon-ok-sign'></i> $message</div>";
			elseif(strtolower($type) == 'info'):
				$errorhandler = "<div class='alert alert-info'><i class='glyphicon glyphicon-info-sign'></i> $message</div>";
			elseif(strtolower($type) == 'warning'):
				$errorhandler = "<div class='alert alert-warning'><i class='glyphicon glyphicon-warning-sign'></i> $message</div>";
			elseif(strtolower($type) == 'danger'):
				$errorhandler = "<div class='alert alert-danger'><i class='glyphicon glyphicon-remove-sign'></i>  $message</div>";
			endif;	
			
			return $errorhandler;
		}
	}
	
