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
							$game['team_abbr'.$i] = $team['team'];

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['team_abbr'.$opp] = $row['team'];

									$ratio = ($team['ratio_fpt_per_pt'] + $row['ratio_fpt_per_pt_opp']) / 2;

									break;
								}
							}

							$game['fpts_plus_minus'.$i] = ($game['score'.$i] * $ratio) - $team['fpts_per_game'];

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
							$game['team_abbr'.$i] = $team['team'];

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['score'.$i] = ($team['pts_per_game'] + $row['pts_per_game_opp']) / 2;

									if ($i == 2) { $game['score'.$i] += 3; } // home court advantage

									break;
								}
							}

							foreach ($teams as $row) 
							{
								if ($game['team'.$opp] == $row['name_sao']) 
								{ 
									$game['team_abbr'.$opp] = $row['team'];

									$ratio = ($team['ratio_fpt_per_pt'] + $row['ratio_fpt_per_pt_opp']) / 2;

									break;
								}
							}

							$game['fpts_plus_minus'.$i] = ($game['score'.$i] * $ratio) - $team['fpts_per_game'];

							$game['line_adj'.$i] = $game['fpts_plus_minus'.$i] / $team['fpts_per_game'];

							break;
						}
					}
				}
			}

			unset($game);			
		}

		foreach ($games['has_lines'] as $key => $game) 
		{ 
			for ($i=1; $i<=2; $i++) 
			{ 
				$teams_today[] = $this->modify_team_abbr_match_ds($game['team_abbr'.$i]);
			}
		}

		if (isset($games['no_lines']))
		{
			foreach ($games['no_lines'] as $key => $game) 
			{ 
				for ($i=1; $i<=2; $i++) 
				{ 
					$teams_today[] = $this->modify_team_abbr_match_ds($game['team_abbr'.$i]);
				}
			}		
		}

		sort($teams_today);

		# echo '<pre>';
		# var_dump($games);
		# echo '</pre>'; exit();

		return array($games, $teams_today);
	}

	public function modify_team_abbr_match_ds($team)
	{
		switch ($team) 
		{
		    case 'PHX':
		        return 'PHO';
		    case 'UTAH':
		        return 'UTA';
		    case 'WSH':
		        return 'WAS';
		}

		return $team;
	}

}