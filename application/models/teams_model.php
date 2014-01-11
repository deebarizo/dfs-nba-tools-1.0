<?php
class teams_model extends CI_Model 
{

	public function get_team_dvp($team, $date)
	{
		$date = new DateTime($date);
		$date->modify('-1 day');
		$day_before = $date->format('Y-m-d');

		$sql = 'SELECT * FROM `dvp` 
				INNER JOIN `teams`
				ON dvp.team = teams.name_dvp
				WHERE teams.abbr_ds = :team AND dvp.date = :day_before';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':team', $team);
		$s->bindValue(':day_before', $day_before);
		$s->execute(); 

		$dvp = $s->fetchAll(PDO::FETCH_ASSOC);

		header('Content-Type: application/json');

		echo json_encode($dvp);
	}

	public function get_team_rotation($team, $date)
	{
		$team = $this->modify_team_abbr($team);

		$sql = 'SELECT DISTINCT `date` FROM `irlstats` 
				WHERE `team` = :team AND `date` < :date 
				ORDER BY `date` DESC LIMIT 7';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':team', $team);
		$s->bindValue(':date', $date);
		$s->execute(); 

		$dates = $s->fetchAll(PDO::FETCH_COLUMN);
		$dates = array_reverse($dates);

		foreach ($dates as $key => $date) 
		{
			$sql = 'SELECT * FROM `irlstats` 
					INNER JOIN  `games` ON irlstats.date = games.date
					WHERE `team` = :team AND games.date = :date
					AND (games.team1 = :team OR games.team2 = :team)';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':team', $team);
			$s->bindValue(':date', $date);
			$s->execute();	

			$games[] = $s->fetchAll(PDO::FETCH_ASSOC);	
		}

		header('Content-Type: application/json');

		echo json_encode($games);
	}

	public function modify_team_abbr($team)
	{
		switch ($team) 
		{
		    case 'PHO':
		        return 'PHX';
		    case 'UTA':
		        return 'UTAH';
		    case 'WAS':
		        return 'WSH';
		}

		return $team;
	}

	public function get_all_teams()
	{
		$this->load->database();

		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$sql = 'SELECT DISTINCT `date` FROM `irlstats` ORDER BY `date` DESC LIMIT 1';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$result = $s->fetchAll(PDO::FETCH_COLUMN);
		$latest_date = $result[0];

		$date_range = $this->create_date_range_array('2013-10-29', $latest_date); 

		foreach ($date_range as $key => $date) 
		{
			$sql = 'SELECT `team1`, `score1`, `team2`, `score2`, `date` FROM `games` WHERE `date` = :date';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':date', $date);
			$s->execute(); 

			$result = $s->fetchAll(PDO::FETCH_ASSOC);
			if (empty($result) === false)
			{
				$schedule[$date] = $result;
			}
		}

		foreach ($schedule as &$games) 
		{
			foreach ($games as &$game) 
			{
				for ($i = 1; $i <= 2; $i++) 
				{ 
					$sql = 'SELECT SUM(`fpts_ds`) FROM `irlstats` 
							WHERE `team` = :team AND `date` = :date GROUP BY team';
					$s = $this->db->conn_id->prepare($sql);
					$s->bindValue(':team', $game['team'.$i]);
					$s->bindValue(':date', $game['date']);
					$s->execute(); 

					$result = $s->fetchAll(PDO::FETCH_COLUMN, 0);
					$game['fpts'.$i] = $result[0];	

					$game['ratio'.$i] = $game['fpts'.$i] / $game['score'.$i];
				}
			}

			unset($game);				
		}

		unset($games);

		// calculate standard deviation and coefficient of variation

		$stats['count'] = 0;

		$stats['ratios']['sum'] = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$stats['count'] += 2;

				$stats['ratios']['sum'] += $game['ratio1'];
				$stats['ratios']['sum'] += $game['ratio2'];
			}
		}

		$stats['ratios']['mean'] = $stats['ratios']['sum'] / $stats['count'];

		$diff_squared = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$diff_squared += pow($game['ratio1'] - $stats['ratios']['mean'], 2); 
				$diff_squared += pow($game['ratio2'] - $stats['ratios']['mean'], 2); 
			}
		}

		$variance = $diff_squared / ($stats['count'] - 1);
		$stats['stdev'] = sqrt($variance);

		$stats['cv'] = $stats['stdev'] / $stats['ratios']['mean'];

		// calculate correlation

		$stats['pts']['sum'] = 0;
		$stats['fpts']['sum'] = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$stats['pts']['sum'] += $game['score1'];
				$stats['pts']['sum'] += $game['score2'];

				$stats['fpts']['sum'] += $game['fpts1'];
				$stats['fpts']['sum'] += $game['fpts2'];
			}
		}

		$stats['pts']['mean'] = $stats['pts']['sum'] / $stats['count'];	

		$stats['fpts']['mean'] = $stats['fpts']['sum'] / $stats['count'];	

		$correlation['axb'] = 0;
		$correlation['a_squared'] = 0;
		$correlation['b_squared'] = 0;

		foreach ($schedule as $games) 
		{
			foreach ($games as $game) 
			{
				$correlation['axb'] += ($game['score1'] - $stats['pts']['mean']) * ($game['fpts1'] - $stats['fpts']['mean']);
				$correlation['axb'] += ($game['score2'] - $stats['pts']['mean']) * ($game['fpts2'] - $stats['fpts']['mean']);

				$correlation['a_squared'] += pow(($game['score1'] - $stats['pts']['mean']), 2);
				$correlation['a_squared'] += pow(($game['score2'] - $stats['pts']['mean']), 2);

				$correlation['b_squared'] += pow(($game['fpts1'] - $stats['fpts']['mean']), 2);
				$correlation['b_squared'] += pow(($game['fpts2'] - $stats['fpts']['mean']), 2);
			}
		}	

		$correlation['answer'] = $correlation['axb'] / (sqrt($correlation['a_squared'] * $correlation['b_squared']));

		$sql = 'SELECT abbr_espn, name_sao FROM `teams`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$teams = $s->fetchAll(PDO::FETCH_ASSOC);

		foreach ($teams as &$team) 
		{
			$team['count'] = 0;

			$team['pts'] = 0;
			$team['fpts'] = 0;

			$team['pts_opp'] = 0;
			$team['fpts_opp'] = 0;
		}

		unset($team);

		foreach ($teams as &$team) 
		{
			foreach ($schedule as $games) 
			{
				foreach ($games as $game) 
				{
					for ($i = 1; $i <= 2; $i++) 
					{ 
						if ($game['team'.$i] == $team['abbr_espn'])
						{
							$team['count'] += 1;

							$team['pts'] += $game['score'.$i];
							$team['fpts'] += $game['fpts'.$i];

							if ($i == 1) { $n = 2; }
							if ($i == 2) { $n = 1; }

							$team['pts_opp'] += $game['score'.$n]; 
							$team['fpts_opp'] += $game['fpts'.$n];
						}
					}
				}
			}
		}

		unset($team);

		foreach ($teams as &$team) 
		{
			$team['ratio'] = $team['fpts'] / $team['pts'];

			$team['pts_per_game'] = $team['pts'] / $team['count'];
			$team['fpts_per_game'] = $team['fpts'] / $team['count'];

			$team['ratio_opp'] = $team['fpts_opp'] / $team['pts_opp'];

			$team['pts_opp_per_game'] = $team['pts_opp'] / $team['count'];
			$team['fpts_opp_per_game'] = $team['fpts_opp'] / $team['count'];
		}

		unset($team);

		// calculate stdev and cv for teams

		$team_stats['count'] = 0;

		$team_stats['ratios']['sum'] = 0;

		foreach ($teams as $team) 
		{
			$team_stats['count'] += 1;

			$team_stats['ratios']['sum'] += $team['ratio'];
		}

		$team_stats['ratios']['mean'] = $team_stats['ratios']['sum'] / $team_stats['count'];

		$diff_squared = 0;

		foreach ($teams as $team) 
		{
			$diff_squared += pow($team['ratio'] - $team_stats['ratios']['mean'], 2); 
		}

		$variance = $diff_squared / ($team_stats['count'] - 1);
		$team_stats['stdev'] = sqrt($variance);

		$team_stats['cv'] = $team_stats['stdev'] / $team_stats['ratios']['mean'];

		# echo '<pre>'; 
		# var_dump($team_stats);
		# var_dump($teams); 
		# var_dump($correlation); 
		# var_dump($stats);
		# var_dump($schedule);
		# echo '</pre>'; exit();

		return $teams;
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

}
