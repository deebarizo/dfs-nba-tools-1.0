<?php
class matchups_model extends CI_Model 
{

	public function get_todays_matchups($games, $teams)
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
						$game['pts_plus_minus'.$i] = $game['score'.$i] - $team['pts_per_game'];

						foreach ($teams as $row) 
						{
							if ($game['team'.$opp] == $row['name_sao']) 
							{ 
								$ratio = ($team['ratio'] + $row['ratio_opp']) / 2;

								break;
							}
						}

						$game['fpts_plus_minus'.$i] = $game['pts_plus_minus'.$i] * $ratio;

						break;
					}
				}
			}
		}

		unset($game);

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
								$ratio = ($team['ratio'] + $row['ratio_opp']) / 2;

								break;
							}
						}

						$game['fpts_plus_minus'.$i] = $game['pts_plus_minus'.$i] * $ratio;

						break;
					}
				}
			}
		}

		unset($game);

		foreach ($games as &$type) 
		{
			foreach ($type as &$game) 
			{
				foreach ($game as &$row) 
				{
					if (is_numeric($row))
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