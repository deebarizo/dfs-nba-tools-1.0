<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fstats_ds extends CI_Controller 
{

	public function index()
	{
		date_default_timezone_set('America/Chicago');

		$today = date('Y-m-d');

		if (time() < strtotime($today.'11:58PM'))
		{
			$date = $today;
		}

		if (time() >= strtotime($today.'11:58PM') AND time() < strtotime($today.'11:59PM'))
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

		$date_15_days_ago = date('Y-m-d', strtotime('15 days ago'));

		foreach ($stats as $key => $player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$sql = 'SELECT `minutes` FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :opening_day AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':opening_day', '2013-10-29');
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_mpg_2013[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);

			$sql = 'SELECT `minutes` FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :date_15_days_ago AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':date_15_days_ago', $date_15_days_ago);
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_mpg_last_15_days[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);
		}

		$mpg['2013'] = $this->get_mpg_for_players($data_mpg_2013);

		$mpg['last_15_days'] = $this->get_mpg_for_players($data_mpg_last_15_days);

		foreach ($stats as &$player) 
		{
			foreach ($mpg['2013'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['mpg_2013'] = $value['mpg']; 

					break;
				}
			}

			foreach ($mpg['last_15_days'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['mpg_last_15_days'] = $value['mpg']; 

					break;
				}
			}
		}

		unset($player);

		// get cv

		foreach ($stats as $key => $player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$sql = 'SELECT (fpts_ds - :fppg_2013)*(fpts_ds - :fppg_2013) as diff_squared FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :opening_day AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':fppg_2013', $player['fppg_2013']);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':opening_day', '2013-10-29');
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_cv_2013[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);
			$data_cv_2013[$player['name']]['fppg'][] = $player['fppg_2013'];

			$sql = 'SELECT (fpts_ds - :fppg_last_15_days)*(fpts_ds - :fppg_last_15_days) as diff_squared FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :date_15_days_ago AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':fppg_last_15_days', $player['fppg_last_15_days']);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':date_15_days_ago', $date_15_days_ago);
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_cv_last_15_days[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);
			$data_cv_last_15_days[$player['name']]['fppg'][] = $player['fppg_last_15_days'];
		}

		$cv['2013'] = $this->get_cv_for_players($data_cv_2013);

		$cv['last_15_days'] = $this->get_cv_for_players($data_cv_last_15_days);

		foreach ($stats as &$player) 
		{
			foreach ($cv['2013'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['cv_2013'] = $value['cv']; 

					break;
				}
			}

			foreach ($cv['last_15_days'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['cv_last_15_days'] = $value['cv']; 

					break;
				}
			}
		}

		unset($player);

		// get last game stats

		foreach ($stats as $key => &$player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$sql = 'SELECT `minutes`, `fpts_ds` FROM `irlstats` 
					WHERE name = :name ORDER BY `date` DESC LIMIT 1';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->execute(); 

			$result = $s->fetchAll(PDO::FETCH_ASSOC);

			$player['mpg_last_game'] = $result[0]['minutes'];
			$player['fppg_last_game'] = $result[0]['fpts_ds'];

			$modified_salary = $player['salary'] / 1000;
			$player['vr_last_game'] = number_format($player['fppg_last_game'] / $modified_salary, 2);
		}

		unset($player);

		echo '<pre>'; 
		var_dump($stats); 
		# var_dump($mpg);
		# var_dump($cv);
		echo '</pre>'; exit();
	}

	public function get_cv_for_players($data_cv)
	{
		foreach ($data_cv as $key => &$player) 
		{
			$total_games = 0;

			$total_diff_squared = 0;

			foreach ($player as $value) 
			{
				if (isset($value['diff_squared']) AND $value['diff_squared'] != NULL) 
				{ 
					$total_games += 1;

					$total_diff_squared += $value['diff_squared'];
				}
			}

			if ($total_games -1 > 0 AND $player['fppg'][0] > 0) 
			{ 
				$variance = $total_diff_squared /  $total_games - 1;
				$stdev = sqrt($variance);
				$player['cv'] = number_format(($stdev / $player['fppg'][0]) * 100, 2);
			}
			else
			{
				$player['cv'] = number_format(0, 2);
			}
		}

		unset($player);

		return $data_cv;
	}

	public function get_mpg_for_players($data_mpg)
	{
		foreach ($data_mpg as $key => &$player) 
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

			if ($total_games > 0) 
			{ 
				$player['mpg'] = number_format($total_minutes /  $total_games, 2);
			}
			else
			{
				$player['mpg'] = number_format(0, 2);
			}
		}

		unset($player);

		return $data_mpg;
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