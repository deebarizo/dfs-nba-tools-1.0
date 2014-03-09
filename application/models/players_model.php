<?php
class players_model extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function get_fpts_distribution($player)
	{
		$name = $this->modify_name_ds($player);

		$sql = 'SELECT name,
						ROUND(((SUM(fgm-threepm) * 2) + ((SUM(fga-threepa) - SUM(fgm-threepm)) * -0.5)) / SUM(fpts_ds) * 100, 2) AS twop, 
						ROUND(((SUM(threepm) * 3) + (SUM(threepa-threepm) * -0.5)) / SUM(fpts_ds) * 100, 2) AS threep, 
						ROUND(((SUM(ftm) * 1) + (SUM(fta-ftm) * -0.5)) / SUM(fpts_ds) * 100, 2) AS ft, 
						ROUND((SUM(oreb) * 1.25) / SUM(fpts_ds) * 100, 2) AS oreb,
						ROUND((SUM(dreb) * 1.25) / SUM(fpts_ds) * 100, 2) AS dreb,
						ROUND((SUM(ast) * 1.5) / SUM(fpts_ds) * 100, 2) AS ast,
						ROUND((SUM(stl) * 2) / SUM(fpts_ds) * 100, 2) AS stl,
						ROUND((SUM(blk) * 2) / SUM(fpts_ds) * 100, 2) AS blk,
						ROUND((SUM(turnovers) * -1) / SUM(fpts_ds) * 100, 2) AS turnovers,
						SUM(fpts_ds) AS fpts_ds
				FROM `irlstats`
				WHERE name = :name';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':name', $name);
		$s->execute(); 

		$result = $s->fetchAll(PDO::FETCH_ASSOC);
		$fpts_distribution = $result[0];

		return $fpts_distribution;
	}

	public function get_game_log($player)
	{
		$name = $this->modify_name_ds($player);

		$sql = 'SELECT * FROM `irlstats` 
				INNER JOIN games
				ON irlstats.date = games.date
				WHERE name = :name
				AND (irlstats.team = games.team1 OR irlstats.team = games.team2)
				ORDER BY games.date DESC';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':name', $name);
		$s->execute(); 

		$game_log = $s->fetchAll(PDO::FETCH_ASSOC);

		foreach ($game_log as $key => &$value) 
		{
			$value['pm_date'] = preg_replace('/\-/', '', $value['date']);

			$value['pm_team1'] = $this->change_team_abbr_for_pm($value['team1']);
			$value['pm_team2'] = $this->change_team_abbr_for_pm($value['team2']);

		 	$value['pm_link'] = 'http://popcornmachine.net/cgi-bin/gameflow.cgi?date='.$value['pm_date'].'&game='.$value['pm_team1'].$value['pm_team2'];
		}

		unset($value);

		return $game_log;
	}

	public function get_todays_players($date, $csv_files)
	{
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

		$latest_date = date('Y-m-d', strtotime('1 days ago', strtotime($date)));

		$date_15_days_ago = date('Y-m-d', strtotime('15 days ago', strtotime($date)));

		foreach ($stats as $key => &$player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$player['url_segment'] = preg_replace('/\s/', '_', $player['name']);

			$sql = 'SELECT `minutes`, `fpts_ds`, fpts_ds / minutes as fppm_ds FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :opening_day AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':opening_day', '2013-10-29');
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_mpg_2013[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);

			$sql = 'SELECT `minutes`, `fpts_ds`, fpts_ds / minutes as fppm_ds FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :date_15_days_ago AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':date_15_days_ago', $date_15_days_ago);
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_mpg_last_15_days[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);

			$last_game_array = end($data_mpg_last_15_days[$player['name']]);

			$player['minutes_last_game'] = $last_game_array['minutes'];
		}

		unset($player);

		$mpg['2013'] = $this->get_minutes_stats_for_players($data_mpg_2013);

		$mpg['last_15_days'] = $this->get_minutes_stats_for_players($data_mpg_last_15_days);

		foreach ($stats as &$player) 
		{
			foreach ($mpg['2013'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['mpg_2013'] = $value['mpg']; 
					$player['fppm_ds_pg_2013'] = $value['fppm_ds_pg']; 
					$player['cv_fppm_ds_2013'] = $value['cv_fppm_ds']; 

					break;
				}
			}

			foreach ($mpg['last_15_days'] as $key => $value) 
			{
				if ($player['name'] == $key)
				{
					$player['mpg_last_15_days'] = $value['mpg']; 
					$player['fppm_ds_pg_last_15_days'] = $value['fppm_ds_pg']; 
					$player['cv_fppm_ds_last_15_days'] = $value['cv_fppm_ds']; 

					break;
				}
			}

			$player['mpg_ch'] = number_format($player['mpg_last_15_days'] - $player['mpg_2013'], 2);

			$player['mpg_ch_last_game'] = number_format($player['minutes_last_game'] - $player['mpg_2013'], 2);
		}

		unset($player);

		// get cv

		foreach ($stats as $key => $player) 
		{
			$modified_name = $this->modify_name_ds($player['name']);

			$sql = 'SELECT (fpts_ds - :fppg_2013)*(fpts_ds - :fppg_2013) as diff_squared_fpts_ds FROM `irlstats` 
					WHERE `name` = :name AND `date` BETWEEN :opening_day AND :latest_date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':fppg_2013', $player['fppg_2013']);
			$s->bindValue(':name', $modified_name);
			$s->bindValue(':opening_day', '2013-10-29');
			$s->bindValue(':latest_date', $latest_date);
			$s->execute(); 

			$data_cv_2013[$player['name']] = $s->fetchAll(PDO::FETCH_ASSOC);
			$data_cv_2013[$player['name']]['fppg'][] = $player['fppg_2013'];

			$sql = 'SELECT (fpts_ds - :fppg_last_15_days)*(fpts_ds - :fppg_last_15_days) as diff_squared_fpts_ds FROM `irlstats` 
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

		// get actual results

		$sql = 'SELECT DISTINCT `date` FROM `irlstats` ORDER BY `date` DESC LIMIT 1';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$result = $s->fetchAll(PDO::FETCH_COLUMN);
		$latest_date_in_db = $result[0];

		if (strtotime($date) <= strtotime($latest_date_in_db)) 
		{
			foreach ($stats as $key => &$player) 
			{
				$modified_name = $this->modify_name_ds($player['name']);

				$sql = 'SELECT * FROM `irlstats` 
						WHERE name = :name AND `date` = :date LIMIT 1';
				$s = $this->db->conn_id->prepare($sql);
				$s->bindValue(':name', $modified_name);
				$s->bindValue(':date', $date);
				$s->execute(); 

				$result = $s->fetchAll(PDO::FETCH_ASSOC);

				if (empty($result))
				{
					$player['actual_min'] = 0;
					$player['actual_fpts'] = 0;
					$player['actual_fppm'] = 0;

					$player['actual_vr'] = 0;						
				}
				else
				{
					$player['actual_min'] = $result[0]['minutes'];

					if ($player['actual_min'] === NULL OR $player['actual_min'] == 0)
					{
						$player['actual_min'] = 0;
						$player['actual_fpts'] = 0;
						$player['actual_fppm'] = 0;

						$player['actual_vr'] = 0;	
					}
					else
					{
						$player['actual_fpts'] = $result[0]['fpts_ds'];
						$player['actual_fppm'] = number_format($player['actual_fpts'] / $player['actual_min'], 2);

						$modified_salary = $player['salary'] / 1000;
						$player['actual_vr'] = number_format($player['actual_fpts'] / $modified_salary, 2);	
					}
				}
			}

			unset($player);
		}

		# echo '<pre>'; 
		# var_dump($stats); 
		# var_dump($mpg);
		# var_dump($cv);
		# echo '</pre>'; exit();

		return $stats;
	}

	public function get_cv_for_players($data_cv)
	{
		foreach ($data_cv as $key => &$player) 
		{
			$total_games = 0;

			$total_diff_squared_fpts_ds = 0;

			foreach ($player as $value) 
			{
				if (isset($value['diff_squared_fpts_ds']) AND $value['diff_squared_fpts_ds'] != NULL) 
				{ 
					$total_games += 1;

					$total_diff_squared_fpts_ds += $value['diff_squared_fpts_ds'];
				}
			}

			if ($total_games > 1 AND $player['fppg'][0] > 0) 
			{ 
				$variance = $total_diff_squared_fpts_ds / ($total_games - 1);
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

	public function get_minutes_stats_for_players($data_mpg)
	{
		foreach ($data_mpg as $key => &$player) 
		{
			$total_games = 0;

			$total_minutes = 0;

			$total_fppm_ds = 0;

			foreach ($player as $value) 
			{
				if ($value['minutes'] != NULL) 
				{ 
					$total_games += 1;

					$total_minutes += $value['minutes'];

					$total_fppm_ds += $value['fppm_ds'];
				}
			}

			$player['total_games'] = $total_games;

			if ($total_games > 0) 
			{ 
				$player['mpg'] = number_format($total_minutes / $total_games, 2);
				$player['fppm_ds_pg'] = $total_fppm_ds / $total_games;
			}
			else
			{
				$player['mpg'] = number_format(0, 2);
				$player['fppm_ds_pg'] = 0;
			}
		}

		unset($player);

		foreach ($data_mpg as $key => &$player) 
		{
			$total_diff_squared_fppm_ds = 0;

			foreach ($player as $value) 
			{
				if (isset($value['minutes']) AND $value['minutes'] != NULL) 
				{ 
					$total_diff_squared_fppm_ds += pow($value['fppm_ds'] - $player['fppm_ds_pg'], 2);
				}
			}

			if ($player['total_games'] > 1 AND $player['fppm_ds_pg'] != 0) 
			{ 
				$variance = $total_diff_squared_fppm_ds / ($player['total_games'] - 1);
				$stdev = sqrt($variance);
				$player['cv_fppm_ds'] = number_format(($stdev / $player['fppm_ds_pg']) * 100, 2);
			}
			else
			{
				$player['cv_fppm_ds'] = number_format(0, 2);
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

	function change_team_abbr_for_pm($team_abbr)
	{
		switch ($team_abbr) 
		{
			case 'GS':
				return 'GSW';
			case 'NO':
				return 'NOR';
			case 'NY':
				return 'NYK';
			case 'PHX':
				return 'PHO';
			case 'SA':
				return 'SAS';
			case 'UTAH':
				return 'UTH';
			case 'WSH':
				return 'WAS';
			default:
				return $team_abbr;
		}
	}

}