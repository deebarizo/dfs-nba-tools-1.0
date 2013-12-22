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

		$csv_files = array(
			'last_15_days' => base_url('files/ds/NBA_Last15_'.$url_segment.'.csv'),
			'season_2013' => base_url('files/ds/NBA_2013_'.$url_segment.'.csv'),
			'season_2012' => base_url('files/ds/NBA_2012_'.$url_segment.'.csv')
		);

		foreach ($csv_files as $key => $csv_file) 
		{
			if (($handle = fopen($csv_file, 'r')) !== false) 
			{
				$row = 0;

				while (($data = fgetcsv($handle, 2000, ',')) !== false)
				{
				    $num = count($data);

				    if ($data[0] !== 'FirstName' AND $key == 'last_15_days')
				    {
					    $stats[$row] = array(
					       	'name' => $data[0].' '.$data[1],
					       	'team' => $data[2],
					       	'position' => $data[3],
					       	'matchup' => $data[5],
					       	'salary' => $data[4],
					       	'gp_last_15_days' => $data[6],
					       	'fppg_last_15_days' => $data[15]
					    );		    	
				    } 

				    if ($data[0] !== 'FirstName' AND isset($stats) AND ($key == 'season_2013' OR $key == 'season_2012'))
				    {
				    	$player_name = $data[0].' '.$data[1];

				    	$shorter_key = preg_replace('/season_/', '', $key);

				    	foreach ($stats as &$player) 
				    	{
				    		if ($player['name'] == $player_name)
				    		{
				    			$player['gp_'.$shorter_key] = $data[6];
				    			$player['fppg_'.$shorter_key] = $data[15];

				    			break;
				    		}
				    	}

				    	unset($player);
				    }

				    $row++;
				}
			} 
		}

    	foreach ($stats as &$player) 
    	{
			$modified_salary = $player['salary'] / 1000;

			$player['vr_last_15_days'] = number_format($player['fppg_last_15_days'] / $modified_salary, 2);
			$player['vr_2013'] = number_format($player['fppg_2013'] / $modified_salary, 2);
			$player['vr_2012'] = number_format($player['fppg_2012'] / $modified_salary, 2);
		}

		unset($player);

		echo '<pre>'; var_dump($stats); echo '</pre>'; exit();
	}

}