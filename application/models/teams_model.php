<?php
class teams_model extends CI_Model 
{

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
	}

	public function get_overview($team)
	{
		$modded_team = $this->mod_team_abbr->mod_daily_abbr($team);

		$sql = 'SELECT team, poss_per_48, MAX(date)
				FROM  `pace` 
				WHERE team = "League Average"';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_ASSOC);
		$league_avg_pace = $result[0]['poss_per_48'];

		$sql = 'SELECT AVG(threepm_per_game), AVG(threepa_per_game), AVG(threep_percentage), 
					   AVG(fta_per_game), AVG(oreb_percentage), AVG(dreb_percentage), AVG(treb_percentage), 
					   AVG(ast_per_game), AVG(to_per_game), AVG(stl_per_game), AVG(blk_per_game), MAX(date)
				FROM `team_opp_stats`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_ASSOC);
		$league_avg_team_opp_stats = $result[0];

		$sql = 'SELECT pace.team, poss_per_48, `threepm_per_game`, `threepa_per_game`, `threep_percentage`, `fta_per_game`, `oreb_percentage`, `dreb_percentage`, `treb_percentage`, `ast_per_game`, `to_per_game`, `stl_per_game`, `blk_per_game`, MAX(pace.date)
				FROM  `teams` 
				INNER JOIN  `pace` ON teams.name_br = pace.team
				INNER JOIN  `team_opp_stats` ON teams.name_espn = team_opp_stats.team
				WHERE abbr_espn = :modded_team';
		$s = $this->db->conn_id->prepare($sql);
		$s->bindValue(':modded_team', $modded_team);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_ASSOC);
		$team_opp_stats = $result[0];

		$pace_adj = ($league_avg_pace - $team_opp_stats['poss_per_48']) / $league_avg_pace;

		$team_opp_stats['comp_threepm_per_game'] = (($team_opp_stats['threepm_per_game'] * $pace_adj) + $team_opp_stats['threepm_per_game']) - $league_avg_team_opp_stats['AVG(threepm_per_game)'];
		$team_opp_stats['comp_threepa_per_game'] = (($team_opp_stats['threepa_per_game'] * $pace_adj) + $team_opp_stats['threepa_per_game']) - $league_avg_team_opp_stats['AVG(threepa_per_game)'];
		$team_opp_stats['comp_threep_percentage'] = ($team_opp_stats['threep_percentage'] - $league_avg_team_opp_stats['AVG(threep_percentage)']) * 100;
		$team_opp_stats['comp_fta_per_game'] = (($team_opp_stats['fta_per_game'] * $pace_adj) + $team_opp_stats['fta_per_game']) - $league_avg_team_opp_stats['AVG(fta_per_game)'];
		$team_opp_stats['comp_oreb_percentage'] = ($team_opp_stats['oreb_percentage'] - $league_avg_team_opp_stats['AVG(oreb_percentage)']) * 100;
		$team_opp_stats['comp_dreb_percentage'] = ($team_opp_stats['dreb_percentage'] - $league_avg_team_opp_stats['AVG(dreb_percentage)']) * 100;
		$team_opp_stats['comp_treb_percentage'] = ($team_opp_stats['treb_percentage'] - $league_avg_team_opp_stats['AVG(treb_percentage)']) * 100;
		$team_opp_stats['comp_ast_per_game'] = (($team_opp_stats['ast_per_game'] * $pace_adj) + $team_opp_stats['ast_per_game']) - $league_avg_team_opp_stats['AVG(ast_per_game)'];
		$team_opp_stats['comp_to_per_game'] = (($team_opp_stats['to_per_game'] * $pace_adj) + $team_opp_stats['to_per_game']) - $league_avg_team_opp_stats['AVG(to_per_game)'];
		$team_opp_stats['comp_stl_per_game'] = (($team_opp_stats['stl_per_game'] * $pace_adj) + $team_opp_stats['stl_per_game']) - $league_avg_team_opp_stats['AVG(stl_per_game)'];
		$team_opp_stats['comp_blk_per_game'] = (($team_opp_stats['blk_per_game'] * $pace_adj) + $team_opp_stats['blk_per_game']) - $league_avg_team_opp_stats['AVG(blk_per_game)'];

		unset($stat);

		# echo '<pre>';
		# var_dump($pace_adj);
		# var_dump($league_avg_pace);
		# var_dump($league_avg_team_opp_stats);
		# var_dump($team_opp_stats);
		# echo '</pre>'; exit();

		$sql = 'SELECT 
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
				FROM `irlstats`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 	

		$result = $s->fetchAll(PDO::FETCH_ASSOC);			
		$all_opp_fantasy_stats = $result[0];

		if ($modded_team == 'all')
		{
			$sql = 'SELECT `opponent`, 
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
					GROUP BY `opponent`
					ORDER BY `opponent`';
			$s = $this->db->conn_id->prepare($sql);
			$s->execute(); 	

			$opp_fantasy_stats = $s->fetchAll(PDO::FETCH_ASSOC);		
		}
		else
		{
			$sql = 'SELECT `opponent`, 
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
					WHERE opponent = :team';
			$s = $this->db->conn_id->prepare($sql);
			$s->bindValue(':team', $modded_team);
			$s->execute(); 	

			$opp_fantasy_stats = $s->fetchAll(PDO::FETCH_ASSOC);			
		}

		foreach ($opp_fantasy_stats as &$stat) 
		{
			$stat['twop_comp'] = round($stat['twop'] - $all_opp_fantasy_stats['twop'], 2);
			$stat['threep_comp'] = round($stat['threep'] - $all_opp_fantasy_stats['threep'], 2);
			$stat['ft_comp'] = round($stat['ft'] - $all_opp_fantasy_stats['ft'], 2);
			$stat['oreb_comp'] = round($stat['oreb'] - $all_opp_fantasy_stats['oreb'], 2);
			$stat['dreb_comp'] = round($stat['dreb'] - $all_opp_fantasy_stats['dreb'], 2);
			$stat['ast_comp'] = round($stat['ast'] - $all_opp_fantasy_stats['ast'], 2);
			$stat['stl_comp'] = round($stat['stl'] - $all_opp_fantasy_stats['stl'], 2);
			$stat['blk_comp'] = round($stat['blk'] - $all_opp_fantasy_stats['blk'], 2);
			$stat['turnovers_comp'] = round($stat['turnovers'] - $all_opp_fantasy_stats['turnovers'], 2);
		}

		unset($stat);

		# echo '<pre>';
		# var_dump($opp_fantasy_stats);
		# echo '</pre>'; exit();	

		return array($team_opp_stats, $opp_fantasy_stats);
	}

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
		$team = $this->mod_team_abbr->mod_daily_abbr($team);

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
		$teams = $this->calculations->get_team_stats($latest_date_in_irlstats_db);

		# echo '<pre>'; 
		# var_dump($teams);
		# var_dump($teams); 
		# var_dump($correlation); 
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
