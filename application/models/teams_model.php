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

	public function get_all_teams($latest_date_in_irlstats_db)
	{


		echo '<pre>'; 
		var_dump($teams);
		# var_dump($teams); 
		# var_dump($correlation); 
		echo '</pre>'; exit();

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
