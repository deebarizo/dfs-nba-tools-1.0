<?php
class evaluation_model extends CI_Model 
{
	public function get_team_stats($date)
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

		

		echo '<pre>'; 
		var_dump($total_pts_mean);
		var_dump($total_fpts_mean); 
		# var_dump($correlation); 
		echo '</pre>'; exit();

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
	}
}