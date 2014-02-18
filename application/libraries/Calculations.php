<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Calculations 
{
	public function get_team_stats($latest_date_in_irlstats_db)
	{
		$CI =& get_instance();
		$CI->load->database();

		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$sql = 'SELECT `abbr_espn` as team, `name_sao`
				FROM `teams`';
		$s = $CI->db->conn_id->prepare($sql);
		$s->execute(); 

		$teams = $s->fetchAll(PDO::FETCH_ASSOC);

		$sql = 'SELECT `team2`, COUNT(`team2`) as num_games, SUM(`score2`) as total_pts, SUM(`score1`) AS total_pts_opp
				FROM `games` 
				WHERE `date` BETWEEN :starting_date AND :ending_date
				GROUP BY `team2`
				ORDER BY `team2`';
		$s = $CI->db->conn_id->prepare($sql);
		$s->bindValue(':starting_date', '2013-10-29');
		$s->bindValue(':ending_date', $latest_date_in_irlstats_db);
		$s->execute(); 

		$ppg['home'] = $s->fetchAll(PDO::FETCH_ASSOC);

		$sql = 'SELECT `team1`, COUNT(`team1`) as num_games, SUM(`score1`) as total_pts, SUM(`score2`) AS total_pts_opp
				FROM `games` 
				WHERE `date` BETWEEN :starting_date AND :ending_date
				GROUP BY `team1`
				ORDER BY `team1`';
		$s = $CI->db->conn_id->prepare($sql);
		$s->bindValue(':starting_date', '2013-10-29');
		$s->bindValue(':ending_date', $latest_date_in_irlstats_db);
		$s->execute(); 

		$ppg['road'] = $s->fetchAll(PDO::FETCH_ASSOC);

		$sql = 'SELECT `team`, SUM(`fpts_ds`) as total_fpts
				FROM `irlstats` 
				WHERE `date` BETWEEN :starting_date AND :ending_date
				GROUP BY `team`
				ORDER BY `team`';
		$s = $CI->db->conn_id->prepare($sql);
		$s->bindValue(':starting_date', '2013-10-29');
		$s->bindValue(':ending_date', $latest_date_in_irlstats_db);
		$s->execute(); 

		$fppg = $s->fetchAll(PDO::FETCH_ASSOC);		

		$sql = 'SELECT `opponent`, SUM(`fpts_ds`) as total_fpts_opp
				FROM `irlstats` 
				WHERE `date` BETWEEN :starting_date AND :ending_date
				GROUP BY `opponent`
				ORDER BY `opponent`';
		$s = $CI->db->conn_id->prepare($sql);
		$s->bindValue(':starting_date', '2013-10-29');
		$s->bindValue(':ending_date', $latest_date_in_irlstats_db);
		$s->execute(); 

		$fppg_opp = $s->fetchAll(PDO::FETCH_ASSOC);		

		foreach ($teams as &$row) 
		{
			foreach ($ppg['home'] as $value) 
			{
				if ($row['team'] == $value['team2'])
				{
					$row['home_num_games'] = $value['num_games'];
					$row['home_total_pts'] = $value['total_pts'];
					$row['home_total_pts_opp'] = $value['total_pts_opp'];

					break;
				}
			}

			foreach ($ppg['road'] as $value) 
			{
				if ($row['team'] == $value['team1'])
				{
					$row['road_num_games'] = $value['num_games'];
					$row['road_total_pts'] = $value['total_pts'];
					$row['road_total_pts_opp'] = $value['total_pts_opp'];

					break;
				}
			}		

			foreach ($fppg as $value) 
			{
				if ($row['team'] == $value['team'])
				{
					$row['total_fpts'] = $value['total_fpts'];

					break;
				}
			}	

			foreach ($fppg_opp as $value) 
			{
				if ($row['team'] == $value['opponent'])
				{
					$row['total_fpts_opp'] = $value['total_fpts_opp'];

					break;
				}
			}		
		}

		unset($row);

		foreach ($teams as &$row) 
		{
			$row['total_games'] = $row['home_num_games'] + $row['road_num_games'];
			
			$row['total_pts'] = $row['home_total_pts'] + $row['road_total_pts'];
			$row['total_pts_opp'] = $row['home_total_pts_opp'] + $row['road_total_pts_opp'];

			$row['pts_per_game'] = $row['total_pts'] / $row['total_games'];
			$row['pts_per_game_opp'] = $row['total_pts_opp'] / $row['total_games'];

			$row['fpts_per_game'] = $row['total_fpts'] / $row['total_games'];
			$row['fpts_per_game_opp'] = $row['total_fpts_opp'] / $row['total_games'];

			$row['ratio_fpt_per_pt'] = $row['total_fpts'] / $row['total_pts'];
			$row['ratio_fpt_per_pt_opp'] = $row['total_fpts_opp'] / $row['total_pts_opp'];			
		}

		unset($row);

		return $teams;
	}
}