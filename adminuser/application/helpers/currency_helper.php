<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if ( ! function_exists('currency_ph'))
	{
		function currency_ph($number=0)
		{	
			$amount = "&#8369; ". number_format($number, 2);
			return $amount;
		}
	}
	
	if ( ! function_exists('currency_usd'))
	{
		function currency_usd($number=0)
		{	
			$amount = "&#x24; ". number_format($number, 2);
			return $amount;
		}
	}	
