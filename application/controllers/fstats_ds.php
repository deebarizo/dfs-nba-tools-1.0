<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fstats_ds extends CI_Controller 
{

	public function index()
	{
		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'6:00PM'))
		{
			$date = $today;
		}

		if (time() > strtotime($today.'6:00PM') AND time() < strtotime($today.'11:59PM'))
		{
			$date = date('Y-m-d',strtotime("1 days"));
		}

		
	}

}