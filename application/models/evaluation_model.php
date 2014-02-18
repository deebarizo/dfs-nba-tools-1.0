<?php
class evaluation_model extends CI_Model 
{
	public function get_pts_fpts_correlation($date)
	{
		ini_set('max_execution_time', 10800); // 10800 seconds = 3 hours

		$teams = $this->calculations->get_team_stats($date);

		// calculate correlation 
		// http://www.mathsisfun.com/data/correlation.html

		$num_games = 0;
		$total_pts = 0;
		$total_fpts = 0;

		foreach ($teams as $team) 
		{
			$num_games += $team['home_num_games'] + $team['road_num_games'];
			$total_pts += $team['total_pts'];
			$total_fpts += $team['total_fpts'];
		}

		$total_pts_mean = $total_pts / $num_games;
		$total_fpts_mean = $total_fpts / $num_games;

		$sql = 'SELECT `team1`, `team2`, `score1`, `score2`, `date` 
				FROM `games`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$games_with_pts = $s->fetchAll(PDO::FETCH_ASSOC);	

		$sql = 'SELECT `team`, SUM(`fpts_ds`) as total_fpts, `date`
				FROM `irlstats`
				GROUP BY `date`, `team`
				ORDER BY `date`';
		$s = $this->db->conn_id->prepare($sql);
		$s->execute(); 

		$games_with_fpts = $s->fetchAll(PDO::FETCH_ASSOC);	

		$axb_a_squared_b_squared = array();

		foreach ($games_with_fpts as $key => $row) 
		{
			foreach ($games_with_pts as $value) 
			{
				for ($i = 1; $i <= 2; $i++) 
				{ 
					if ($row['team'] == $value['team'.$i] AND $row['date'] == $value['date'])
					{
						$axb_a_squared_b_squared[$key] = array(
							'team' => $row['team'], 
							'pts' => $value['score'.$i],
							'fpts' => $row['total_fpts'],
							'axb' => ($value['score'.$i] - $total_pts_mean) * ($row['total_fpts'] - $total_fpts_mean),
							'a_squared' => pow($value['score'.$i] - $total_pts_mean, 2),
							'b_squared' => pow($row['total_fpts'] - $total_fpts_mean, 2),
							'date' => $value['date']
						);

						break;
					}
				}

				if (isset($axb_a_squared_b_squared[$key])) { break; }
			}
		}

		$total_axb = 0;
		$total_a_squared = 0;
		$total_b_squared = 0;

		foreach ($axb_a_squared_b_squared as $value) 
		{
			$total_axb += $value['axb'];
			$total_a_squared += $value['a_squared'];
			$total_b_squared += $value['b_squared'];
		}

		$correlation = number_format($total_axb / sqrt($total_a_squared * $total_b_squared), 4);

		echo "PTS and FPTS DS Correlation: ".$correlation;

		# echo '<pre>'; 
		# var_dump($games_with_pts); 
		# var_dump($games_with_fpts); 
		# echo '</pre>'; exit();
	}
}