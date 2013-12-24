<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fstats_ds extends CI_Controller 
{

	public function index()
	{
		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'11:00PM'))
		{
			$date = $today;
		}

		if (time() >= strtotime($today.'11:00PM') AND time() < strtotime($today.'11:59PM'))
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
					       	'team' => strtoupper($data[2]),
					       	'position' => $data[3],
					       	'opponent' => strtoupper($data[5]),
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

		foreach ($stats as &$player) 
		{
			$player['name_team_position'] = $player['name'].' ('.$player['team'].'-'.$player['position'].')';
		}

		unset($player);

		// get mpg

		$this->load->database();

		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$sql = 'SELECT DISTINCT `date` FROM `irlstats` ORDER BY `date` DESC LIMIT 1';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$result = $s->fetchAll(PDO::FETCH_COLUMN);
		$latest_date = $result[0];

		foreach ($stats as $key => $player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$sql = 'SELECT `minutes` FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :first_date AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':first_date', '2013-10-29');
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$mpg[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);
		}

		foreach ($mpg as $key => &$player) 
		{
			$total_games = 0;

			$total_minutes = 0;

			foreach ($player as $value) 
			{
				if ($value['minutes'] != NULL) 
				{ 
					$total_games += 1;

					$total_minutes += $value['minutes'];
				}
			}

			$player['total_games'] = $total_games;

			$player['total_minutes'] = $total_minutes;

			if ($total_games > 0) 
			{ 
				$player['mpg'] = $total_minutes /  $total_games;
			}
			else
			{
				$player['mpg'] = 0;
			}
		}

		unset($player);

		echo '<pre>'; 
		# var_dump($stats); 
		var_dump($mpg);
		echo '</pre>'; exit();
	}

	function create_date_range_array($strDateFrom,$strDateTo)
	{
	    // takes two dates formatted as YYYY-MM-DD and creates an
	    // inclusive array of the dates between the from and to dates.

	    // could test validity of dates here but I'm already doing
	    // that in the main script

	    $aryRange=array();

	    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }

	    return $aryRange;
	}

	function modify_name_ds($name)
	{
		switch ($name) 
		{
		    case 'Lou Williams':
		        return 'Louis Williams';
		    case 'Patrick Mills':
		        return 'Patty Mills';
		    case 'Louis Amundson':
		        return 'Lou Amundson';
		}

		return $name;
	}

}