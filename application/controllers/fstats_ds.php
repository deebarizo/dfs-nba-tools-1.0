<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fstats_ds extends CI_Controller 
{

	public function index()
	{
		$this->load->helper('url');

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

		$url_segment = preg_replace('/\d\d(\d\d)-(\d\d)-(\d\d)/', '$2$3$1', $date);

		echo $url_segment;

		$csv_files['2012'] = fopen(base_url('files/ds/NBA_2012_'.$url_segment.'.csv'), 'r');

		$row = 0;

		while (($data = fgetcsv($csv_files['2012'], 2000, ',')) !== false)
		{
		    $num = count($data);

		    if ($data[0] !== 'FirstName')
		    {
			    $stats['2012'][$row] = array(
			       	'name' => $data[0].' '.$data[1],
			       	'team' => $data[2]
			    );		    	
		    }

			$row++;
		}

		echo '<pre>'; var_dump($stats); echo '</pre>'; exit();
	}

}