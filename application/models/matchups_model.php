<?php
class matchups_model extends CI_Model 
{

	public function get_todays_matchups($games, $teams)
	{
		if (empty($games['has_lines']) === false)
		{				
			foreach ($games['has_lines'] as &$game) 
			{
				for ($i = 1; $i <= 2; $i++) 
				{ 
					if ($i == 1) { $opp = 2; }
					if ($i == 2) { $opp = 1; }

					foreach ($teams as $team) 
					{
						if ($game['team'.$i] == $team['name_sao'])
						{
							$game['team_abbr'.$i] = $team['abbr_espn'];

							$game['pts_plus_minus'.$i] = $game['score'.$i] - $team['pts_per_game'];

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['team_abbr'.$opp] = $row['abbr_espn'];

									$ratio = ($team['ratio'] + $row['ratio_opp']) / 2;

									break;
								}
							}

							$game['fpts_plus_minus'.$i] = $game['pts_plus_minus'.$i] * $ratio;

							$game['line_adj'.$i] = $game['fpts_plus_minus'.$i] / $team['fpts_per_game'];

							break;
						}
					}
				}
			}

			unset($game);
		}

		if (empty($games['no_lines']) === false)
		{
			foreach ($games['no_lines'] as &$game) 
			{
				for ($i = 1; $i <= 2; $i++) 
				{ 
					if ($i == 1) { $opp = 2; }
					if ($i == 2) { $opp = 1; }

					foreach ($teams as $team) 
					{
						if ($game['team'.$i] == $team['name_sao'])
						{
							$game['team_abbr'.$i] = $team['abbr_espn'];

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['score'.$i] = ($team['pts_per_game'] + $row['pts_opp_per_game']) / 2;

									if ($i == 2) { $game['score'.$i] += 3; } // home court advantage

									break;
								}
							}

							$game['pts_plus_minus'.$i] = $game['score'.$i] - $team['pts_per_game'];

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['team_abbr'.$opp] = $row['abbr_espn'];

									$ratio = ($team['ratio'] + $row['ratio_opp']) / 2;

									break;
								}
							}

							$game['fpts_plus_minus'.$i] = $game['pts_plus_minus'.$i] * $ratio;

							$game['line_adj'.$i] = $game['fpts_plus_minus'.$i] / $team['fpts_per_game'];

							break;
						}
					}
				}
			}

			unset($game);			
		}

		foreach ($games as &$type) 
		{
			foreach ($type as &$game) 
			{
				foreach ($game as $key => &$row) 
				{
					if (is_numeric($row) AND 
						$key != 'line_adj1' AND 
						$key != 'line_adj2' AND
						$key != 'ps1' AND
						$key != 'ps2')
					{
						$row = number_format($row, 2);
					}
				}

				unset($row);
			}

			unset($game);
		}

		unset($type);

		# echo '<pre>';
		# var_dump($games);
		# echo '</pre>'; exit();

		return $games;
	}

}